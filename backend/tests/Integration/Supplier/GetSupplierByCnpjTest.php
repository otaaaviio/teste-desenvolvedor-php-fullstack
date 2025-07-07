<?php

namespace Tests\Integration\Supplier;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response as StatusCode;

use function Pest\Laravel\getJson;

uses(DatabaseTransactions::class);

uses()->group('supplier-get-by-cnpj');

$baseUrl = 'api/v1/suppliers';
$petobrasCnpj = '33000167000101';

test('can not get a supplier with non-existent cnpj', function () use ($baseUrl) {
    $response = getJson($baseUrl.'/cnpj/12345678901234');

    $response->assertStatus(StatusCode::HTTP_BAD_GATEWAY)
        ->assertJsonStructure(
            ['message']
        );
});

test('can not get a supplier with invalid cnpj', function () use ($baseUrl) {
    $response = getJson($baseUrl.'/cnpj/000');

    $response->assertStatus(StatusCode::HTTP_BAD_REQUEST);
});

test('can get a supplier by cnpj', function () use ($baseUrl, $petobrasCnpj) {
    $response = getJson($baseUrl.'/cnpj/'.$petobrasCnpj);

    $response->assertStatus(StatusCode::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => [
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

test('should cache supplier data from Brazil API', function () use ($baseUrl, $petobrasCnpj) {
    $cacheTag = 'supplier_cnpj';

    getJson($baseUrl.'/cnpj/'.$petobrasCnpj);

    expect(cache()->tags([$cacheTag])->has(md5($petobrasCnpj)))
        ->toBeTrue();
});
