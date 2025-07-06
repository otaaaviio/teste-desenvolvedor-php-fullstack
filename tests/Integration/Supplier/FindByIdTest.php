<?php

namespace Tests\Integration\Supplier;

use App\Modules\Supplier\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response as StatusCode;

use function Pest\Laravel\getJson;

uses(DatabaseTransactions::class);

uses()->group('supplier-find-by-id');

$baseUrl = 'api/v1/suppliers';

test('can receive a supplier by id', function () use ($baseUrl) {
    $supplier = Supplier::factory()->withAddress()->create();

    $response = getJson($baseUrl.'/'.$supplier->id);

    $response->assertStatus(StatusCode::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'document',
                'document_type',
                'address' => [
                    'street',
                    'number',
                    'complement',
                    'neighborhood',
                    'city',
                    'state',
                    'zip_code',
                ],
            ],
        ]);
});

test('should return 404 when supplier does not exist', function () use ($baseUrl) {
    $response = getJson($baseUrl.'/999999');

    $response->assertStatus(StatusCode::HTTP_NOT_FOUND)
        ->assertJsonStructure(['message']);
});

test('should not return supplier soft deleted', function () use ($baseUrl) {
    $supplier = Supplier::factory()->withAddress()->create();
    $supplier->delete();

    $response = getJson($baseUrl.'/'.$supplier->id);

    $response->assertStatus(StatusCode::HTTP_NOT_FOUND)
        ->assertJsonStructure(['message']);
});
