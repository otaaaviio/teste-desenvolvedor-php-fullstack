<?php

namespace App\Modules\Supplier\Repositories\Contracts;

use App\Modules\Supplier\DTOs\CreateSupplierDTO;
use App\Modules\Supplier\DTOs\SupplierFilterDTO;
use App\Modules\Supplier\DTOs\UpdateSupplierDTO;

interface SupplierRepository
{
    public function createSupplier(CreateSupplierDTO $dto): array;

    public function updateSupplier(UpdateSupplierDTO $dto): array;

    public function deleteSupplier(int $id): void;

    public function findSupplierById(int $id): ?array;

    public function findAllSuppliers(SupplierFilterDTO $filters): array;
}
