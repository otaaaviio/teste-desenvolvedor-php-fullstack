<?php

namespace Tests\Integration\Supplier;

use App\Modules\Supplier\Jobs\ClearSuppliersCacheJob;
use App\Modules\Supplier\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response as StatusCode;
use Illuminate\Support\Facades\Queue;

uses(DatabaseTransactions::class, WithFaker::class);

uses()->group('Supplier Integration Test');

$baseUrl = 'api/v1/suppliers';

test('can create a supplier with valid cnpj data', function () use ($baseUrl) {
    $petobrasCnpj = '33000167000101';
    $cnpjResponse = $this->getJson($baseUrl.'/cnpj/'.$petobrasCnpj);

    $cnpjData = $cnpjResponse->json() ?? [];

    $response = $this->postJson($baseUrl, $cnpjData['data']);

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
    $response = $this->postJson($baseUrl, []);

    $response->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message',
            'errors' => [],
        ]);
});

test('can create a supplier with valid cpf data', function () use ($baseUrl) {
    $payload = [
        'name' => $this->faker->company(),
        'email' => $this->faker->email(),
        'phone' => $this->faker->phoneNumber(),
        'document' => '91905567049',
        'document_type' => 'CPF',
        'address' => [
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->secondaryAddress(),
            'neighborhood' => $this->faker->streetSuffix(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'zip_code' => '12345-678',
        ],
    ];

    $response = $this->postJson($baseUrl, $payload);

    expect($response->status())->toBe(StatusCode::HTTP_CREATED);

    $supplierId = $response->json('data.id');

    $supplier = Supplier::find($supplierId);

    expect($supplier)->not->toBeNull();
});

test('should call cache cleaning job after creating a supplier', function () use ($baseUrl) {
    Queue::fake();

    $payload = [
        'name' => $this->faker->company(),
        'email' => $this->faker->email(),
        'phone' => $this->faker->phoneNumber(),
        'document' => '91905567049',
        'document_type' => 'CPF',
        'address' => [
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->secondaryAddress(),
            'neighborhood' => $this->faker->streetSuffix(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'zip_code' => '12345-678',
        ],
    ];

    $response = $this->postJson($baseUrl, $payload);
    $response->assertStatus(StatusCode::HTTP_CREATED);

    Queue::assertPushed(ClearSuppliersCacheJob::class);
})->only();
