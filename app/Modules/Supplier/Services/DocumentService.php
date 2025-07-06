<?php

namespace App\Modules\Supplier\Services;

use App\Modules\Supplier\DTOs\CreateSupplierDTO;
use App\Modules\Supplier\DTOs\SupplierAddressDTO;
use App\Modules\Supplier\Enums\DocumentType;
use App\Modules\Supplier\Exceptions\DocumentException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class DocumentService
{
    protected string $baseUrl = 'https://brasilapi.com.br/api';

    protected int $cacheTtl = 60 * 60;

    public function getInfosByCnpj(string $cnpj): CreateSupplierDTO
    {
        $hash = md5($cnpj);

        $cacheKey = "supplier_cnpj_{$hash}";

        return cache()->remember($cacheKey, $this->cacheTtl, function () use ($cnpj) {
            if (strlen($cnpj) !== 14) {
                throw DocumentException::invalidDocument(DocumentType::CNPJ);
            }

            $result = Http::timeout(10)->get("{$this->baseUrl}/cnpj/v1/{$cnpj}");

            if ($result->status() !== StatusCode::HTTP_OK) {
                throw DocumentException::brazilApiRequestFailed($result->status());
            }

            $data = $result->json();

            return CreateSupplierDTO::make([
                'name' => $data['razao_social'] ?? '',
                'document' => $data['cnpj'],
                'document_type' => DocumentType::CNPJ,
                'email' => $data['email'] ?? '',
                'phone' => $data['ddd_telefone_1'] ?? '',
                'address' => [
                    'street' => $data['logradouro'] ?? '',
                    'number' => $data['numero'] ?? '',
                    'complement' => $data['complemento'] ?? '',
                    'neighborhood' => $data['bairro'] ?? '',
                    'city' => $data['municipio'] ?? '',
                    'state' => $data['uf'] ?? '',
                    'zip_code' => $data['cep'] ?? '',
                ],
            ]);
        });
    }

    public function validateCnpjAddress(string $cnpj, SupplierAddressDTO $supplierAddressDTO): bool
    {
        $supplierFromApi = $this->getInfosByCnpj($cnpj);

        $apiAddress = $supplierFromApi->address;

        $fields = [
            'street',
            'city',
            'state',
            'zipCode',
        ];

        foreach ($fields as $field) {
            $apiValue = $apiAddress->{$field} ?? '';
            $inputValue = $supplierAddressDTO->{$field} ?? '';
            if (trim((string) $apiValue) !== trim((string) $inputValue)) {
                return false;
            }
        }

        return true;
    }

    public function validateCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
