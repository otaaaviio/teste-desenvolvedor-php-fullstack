<?php

namespace App\Modules\Supplier\DTOs;

use App\Modules\Supplier\Enums\DocumentType;

readonly class CreateSupplierDTO implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $document,
        public DocumentType $documentType,
        public ?string $email,
        public ?string $phone,
        public SupplierAddressDTO $address
    ) {}

    public static function make(array $payload): self
    {
        $document = preg_replace('/\D/', '', $payload['document']);
        $documentType = strlen($document) === 14 ? DocumentType::CNPJ : DocumentType::CPF;

        return new self(
            name: $payload['name'],
            document: $document,
            documentType: $documentType,
            email: $payload['email'] ?? null,
            phone: $payload['phone'] ?? null,
            address: SupplierAddressDTO::make($payload['address'])
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'document' => $this->document,
            'document_type' => $this->documentType->value,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address->jsonSerialize(),
        ];
    }
}
