<?php

namespace App\Modules\Supplier\DTOs;

readonly class SupplierFilterDTO
{
    public function __construct(
        public ?string $search,
        public string $sortOrdenation,
        public string $sortColumn,
        public int $perPage,
        public int $page
    ) {}

    public static function make(array $payload): self
    {
        return new self(
            search: $payload['search'] ?? null,
            sortOrdenation: $payload['sort_ordenation'] ?? 'desc',
            sortColumn: $payload['sort_column'] ?? 'created_at',
            perPage: (int) ($payload['per_page'] ?? 10),
            page: (int) ($payload['page'] ?? 1)
        );
    }
}
