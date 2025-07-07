<?php

namespace App\Modules\Supplier\DTOs;

readonly class SupplierAddressDTO implements \JsonSerializable
{
    public function __construct(
        public string $street,
        public ?string $number,
        public ?string $complement,
        public ?string $neighborhood,
        public string $city,
        public string $state,
        public string $zipCode
    ) {}

    public static function make(array $payload): self
    {
        $zipCode = preg_replace('/\D/', '', $payload['zip_code'] ?? '');

        return new self(
            street: $payload['street'],
            number: $payload['number'] ?? null,
            complement: $payload['complement'] ?? null,
            neighborhood: $payload['neighborhood'] ?? null,
            city: $payload['city'],
            state: $payload['state'],
            zipCode: $zipCode
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
        ];
    }
}
