<?php

namespace Tests\Integration\Supplier;

use App\Modules\Supplier\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response as StatusCode;

use function Pest\Faker\fake;
use function Pest\Laravel\putJson;

uses(DatabaseTransactions::class);

uses()->group('supplier-update');

$baseUrl = 'api/v1/suppliers';

test('can update a supplier with valid data', function () use ($baseUrl) {
    $supplier = Supplier::factory()->withAddress()->create();

    $payload = [
        'name' => 'Updated Supplier Name',
        'email' => fake()->email(),
        'phone' => fake()->e164PhoneNumber(),
    ];

    $response = putJson("{$baseUrl}/{$supplier->id}", $payload);

    $response->assertStatus(StatusCode::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'email', 'phone'],
        ]);
});

test('can not update a supplier with invalid data', function () use ($baseUrl) {
    $supplier = Supplier::factory()->withAddress()->create();

    $payload = [
        'name' => str_repeat('a', 300),
        'email' => 'invalid-email',
        'phone' => '12345',
    ];

    $response = putJson("{$baseUrl}/{$supplier->id}", $payload);

    $response->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name', 'email', 'phone']);
});

test('can not update a supplier with non-existent id', function () use ($baseUrl) {
    $payload = [
        'name' => 'Non-existent Supplier',
        'email' => fake()->email(),
        'phone' => fake()->e164PhoneNumber(),
    ];

    $response = putJson("{$baseUrl}/9999", $payload);

    $response->assertStatus(StatusCode::HTTP_NOT_FOUND)
        ->assertJsonStructure(['message']);
});
