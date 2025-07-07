<?php

namespace App\Modules\Supplier\DTOs;

readonly class UpdateSupplierDTO
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}

    public static function make(array $payload): self
    {
        return new self(
            id: $payload['id'],
            name: $payload['name'],
            email: $payload['email'],
            phone: $payload['phone'],
        );
    }
}
