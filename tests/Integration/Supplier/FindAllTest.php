<?php

namespace Tests\Integration\Supplier;

use App\Modules\Supplier\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

uses()->group('supplier-find-all');

$baseUrl = 'api/v1/suppliers';

test('can receive suppliers paginated', function () use ($baseUrl) {
    Supplier::factory()->withAddress()->count(5)->create();

    $response = getJson($baseUrl);

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.data'))
        ->toHaveCount(5);
});

test('should receive cache return', function () use ($baseUrl) {
    Supplier::factory()->withAddress()->count(2)->create();
    $response1 = getJson($baseUrl);
    $data1 = $response1->json('data.data');
    expect($data1)->toHaveCount(2);

    Supplier::factory()->withAddress()->create();

    $response2 = getJson($baseUrl);
    $data2 = $response2->json('data.data');
    expect($data2)->toEqual($data1);

    Cache::tags(['suppliers_list'])->flush();

    $response3 = getJson($baseUrl);
    $data3 = $response3->json('data.data');
    expect($data3)->toHaveCount(3);
});

test('should return empty data when no suppliers exist', function () use ($baseUrl) {
    $response = getJson($baseUrl);

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.data'))
        ->toHaveCount(0);
});

test('can be possible send all filters and receive the paginated data', function () use ($baseUrl) {
    $suppliers = Supplier::factory()->withAddress()->count(5)->create();
    $filters = [
        'search' => $suppliers->first()->name,
        'sort_ordenation' => 'asc',
        'sort_column' => 'name',
        'per_page' => 2,
        'page' => 1,
    ];

    $response = getJson($baseUrl.'?'.http_build_query($filters));

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.data'))
        ->toHaveCount(1)
        ->and($response->json('data.per_page'))
        ->toBe(2)
        ->and($response->json('data.data.0.name'))
        ->toBe($suppliers->first()->name);
});

test('can be possible send search filter and receive the paginated data', function (string $columnToFilter) use ($baseUrl) {
    $suppliers = Supplier::factory()->withAddress()->count(5)->create();

    $filters = [
        'search' => $suppliers->first()->{$columnToFilter},
    ];

    $response = getJson($baseUrl.'?'.http_build_query($filters));

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.data'))
        ->toHaveCount(1)
        ->and($response->json("data.data.0.name"))
        ->toBe($suppliers->first()->name);
})->with(['name', 'email', 'phone', 'document']);

test('can be possible send sort column filter and receive the paginated data', function (string $sortColumn) use ($baseUrl) {
    $suppliers = Supplier::factory()->withAddress()->count(5)->create();
    $filters = [
        'sort_ordenation' => 'asc',
        'sort_column' => $sortColumn,
    ];

    $response = getJson($baseUrl.'?'.http_build_query($filters));

    $expectedValue = $suppliers->sortBy($sortColumn)->first()->{$sortColumn};

    if ($expectedValue instanceof Carbon) {
        $expectedValue = $expectedValue->toJSON();
    }

    expect($response->json())
        ->toBePaginated()
        ->and($response->json("data.data.0.{$sortColumn}"))
        ->toContain($expectedValue);
})->with(['name', 'email', 'created_at']);

test('can be possible send sort ordenation filter and receive the paginated data', function (string $sortOrdenation) use ($baseUrl) {
    $suppliers = Supplier::factory()->withAddress()->count(5)->create();
    $filters = [
        'sort_ordenation' => $sortOrdenation,
        'sort_column' => 'name',
    ];

    $response = getJson($baseUrl.'?'.http_build_query($filters));

    $sorted = $sortOrdenation === 'asc'
        ? $suppliers->sortBy('name')->values()
        : $suppliers->sortByDesc('name')->values();

    $expectedValue = $sorted->first()->name;

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.data.0.name'))
        ->toBe($expectedValue);
})->with(['asc', 'desc']);

test('can be possible send pagination filter and receive the paginated data', function (int $perPage, int $page) use ($baseUrl) {
    Supplier::factory()->withAddress()->count(5)->create();
    $filters = [
        'per_page' => $perPage,
        'page' => $page,
    ];

    $response = getJson($baseUrl.'?'.http_build_query($filters));

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.per_page'))->toBe($perPage)
        ->and($response->json('data.current_page'))->toBe($page)
        ->and($response->json('data.data'))->toHaveCount(
            $page === 1
                ? min($perPage, 5)
                : max(0, min($perPage, 5 - $perPage * ($page - 1)))
        );
})->with([
    [1, 1],
    [2, 1],
    [2, 2],
    [3, 2],
    [5, 1],
    [5, 2],
]);

test('should use default values when invalid filters are provided', function () use ($baseUrl) {
    Supplier::factory()->withAddress()->count(5)->create();
    $filters = [
        'sort_ordenation' => 'invalid',
        'sort_column' => 'invalid',
        'per_page' => 0,
        'page' => 0,
    ];

    $response = getJson($baseUrl.'?'.http_build_query($filters));

    expect($response->json())
        ->toBePaginated()
        ->and($response->json('data.data'))
        ->toHaveCount(5)
        ->and($response->json('data.per_page'))->toBe(15)
        ->and($response->json('data.current_page'))->toBe(1);
});
