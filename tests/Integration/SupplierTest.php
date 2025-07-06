<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response as StatusCode;

uses(DatabaseTransactions::class);

uses()->group('Supplier Integration Test');

$baseUrl = 'api/v1/suppliers';
$petobrasCnpj = '33000167000101';

test('can create a supplier with valid cnpj data', function () use ($baseUrl, $petobrasCnpj) {
    $cnpjResponse = $this->getJson($baseUrl . '/cnpj/' . $petobrasCnpj);

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
                    'zip_code'
                ]
            ]
        ]);
});

test('can not create a supplier with invalid data', function () use ($baseUrl) {
    $response = $this->postJson($baseUrl, []);

    $response->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message',
            'errors' => []
        ]);
});

test('can not create a supplier with invalid cnpj', function () use ($baseUrl) {
    $response = $this->getJson($baseUrl . '/cnpj/12345678901234');

    $response->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'document'
            ]
        ]);
});
