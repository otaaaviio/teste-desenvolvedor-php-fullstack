<?php

namespace Tests\Integration\Supplier;

use App\Modules\Supplier\Jobs\ClearSuppliersCacheJob;
use App\Modules\Supplier\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response as StatusCode;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;

uses(DatabaseTransactions::class);

uses()->group('supplier-delete');

$baseUrl = 'api/v1/suppliers';

test('can delete a existent supplier', function () use ($baseUrl) {
    $supplier = Supplier::factory()->withAddress()->create();
    $response = deleteJson($baseUrl."/{$supplier->id}");

    $response->assertStatus(StatusCode::HTTP_NO_CONTENT);
    assertSoftDeleted($supplier);
});

test('deleting a non-existent supplier is idempotent and returns no content', function () use ($baseUrl) {
    $response = deleteJson($baseUrl.'/999999');

    $response->assertStatus(StatusCode::HTTP_NO_CONTENT);
});

test('should call cache cleaning job after deleting a supplier', function () use ($baseUrl) {
    Queue::fake();
    getJson($baseUrl);

    $supplier = Supplier::factory()->withAddress()->create();
    $response = deleteJson($baseUrl."/{$supplier->id}");

    $response->assertStatus(StatusCode::HTTP_NO_CONTENT);

    Queue::assertPushed(ClearSuppliersCacheJob::class);
});

test('should clear cache after deleting a supplier', function () use ($baseUrl) {
    cache()->tags(['suppliers_list'])->put('test_key', 'test_value', 60);

    expect(cache()->tags(['suppliers_list'])->get('test_key'))->toBe('test_value');

    $supplier = Supplier::factory()->withAddress()->create();
    $response = deleteJson($baseUrl."/{$supplier->id}");

    $response->assertStatus(StatusCode::HTTP_NO_CONTENT);

    assertSoftDeleted($supplier);
    expect(cache()->tags(['suppliers_list'])->get('test_key'))->toBeNull();
});
