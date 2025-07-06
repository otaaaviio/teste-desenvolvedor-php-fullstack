<?php

namespace Tests\Integration\Supplier;

use App\Modules\Supplier\Jobs\ClearSuppliersCacheJob;
use App\Modules\Supplier\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response as StatusCode;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\fake;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(DatabaseTransactions::class);

uses()->group('supplier-create');

$baseUrl = 'api/v1/suppliers';

function getValidPayloadWithCPF(): array
{
    return [
        'name' => fake()->company(),
        'email' => fake()->email(),
        'phone' => fake()->phoneNumber(),
        'document' => '91905567049',
        'document_type' => 'CPF',
        'address' => [
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'complement' => fake()->secondaryAddress(),
            'neighborhood' => fake()->streetSuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zip_code' => '12345-678',
        ],
    ];
}

test('can create a supplier with valid cnpj data', function () use ($baseUrl) {
    $petobrasCnpj = '33000167000101';
    $cnpjResponse = getJson($baseUrl.'/cnpj/'.$petobrasCnpj);

    $cnpjData = $cnpjResponse->json() ?? [];

    $response = postJson($baseUrl, $cnpjData['data']);

    $response->assertStatus(StatusCode::HTTP_CREATED)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'document',
                'created_at',
                'address' => [
                    'id',
                    'supplier_id',
                    'street',
                    'city',
                    'state',
                    'zip_code',
                ],
            ],
        ]);
});

test('can not create a supplier with invalid data', function () use ($baseUrl) {
    $response = postJson($baseUrl, []);

    $response->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message',
            'errors' => [],
        ]);
});

test('can create a supplier with valid cpf data', function () use ($baseUrl) {
    $payload = getValidPayloadWithCPF();

    $response = postJson($baseUrl, $payload);

    expect($response->status())->toBe(StatusCode::HTTP_CREATED);

    $supplierId = $response->json('data.id');

    $supplier = Supplier::find($supplierId);

    expect($supplier)->not->toBeNull();
});

test('should call cache cleaning job after creating a supplier', function () use ($baseUrl) {
    Queue::fake();

    $payload = getValidPayloadWithCPF();

    $response = postJson($baseUrl, $payload);
    $response->assertStatus(StatusCode::HTTP_CREATED);

    Queue::assertPushed(ClearSuppliersCacheJob::class);
});

test('should clear cache after creating a supplier', function () use ($baseUrl) {
    cache()->tags(['suppliers_list'])->put('test_key', 'test_value', 60);

    expect(cache()->tags(['suppliers_list'])->get('test_key'))->toBe('test_value');

    $payload = getValidPayloadWithCPF();

    $response = postJson($baseUrl, $payload);
    $response->assertStatus(StatusCode::HTTP_CREATED);

    expect(cache()->tags(['suppliers_list'])->get('test_key'))->toBeNull();
});
