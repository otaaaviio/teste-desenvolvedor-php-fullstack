<?php

namespace App\Modules\Supplier\Repositories;

use App\Modules\Supplier\DTOs\CreateSupplierDTO;
use App\Modules\Supplier\DTOs\SupplierFilterDTO;
use App\Modules\Supplier\DTOs\UpdateSupplierDTO;
use App\Modules\Supplier\Jobs\ClearSuppliersCacheJob;
use App\Modules\Supplier\Models\Supplier;
use App\Modules\Supplier\Repositories\Contracts\SupplierRepository as SupplierRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SupplierRepository implements SupplierRepositoryContract
{
    protected string $cacheTag = 'suppliers_list';

    protected int $cacheTtl = 60 * 60 * 24;

    public function createSupplier(CreateSupplierDTO $dto): array
    {
        DB::beginTransaction();

        try {
            $supplier = Supplier::create([
                'name' => $dto->name,
                'document' => $dto->document,
                'document_type' => $dto->documentType->value,
                'email' => $dto->email,
                'phone' => $dto->phone,
            ]);

            $supplier->address()->create([
                'street' => $dto->address->street,
                'number' => $dto->address->number,
                'complement' => $dto->address->complement,
                'neighborhood' => $dto->address->neighborhood,
                'city' => $dto->address->city,
                'state' => $dto->address->state,
                'zip_code' => $dto->address->zipCode,
            ]);

            DB::commit();

            dispatch(new ClearSuppliersCacheJob($this->cacheTag));

            return $supplier
                ->with(['address' => function ($query) {
                    $query->select('id', 'supplier_id', 'street', 'city', 'state', 'zip_code');
                }])
                ->select('id', 'name', 'email', 'phone', 'document', 'created_at')
                ->first()
                ->toArray();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateSupplier(UpdateSupplierDTO $dto): array
    {
        $supplier = Supplier::findOrFail($dto->id);

        $updateData = [
            'email' => $dto->email,
            'phone' => $dto->phone,
        ];

        if ($dto->name) {
            $updateData['name'] = $dto->name;
        }

        $supplier->update($updateData);

        $supplier->refresh();

        dispatch(new ClearSuppliersCacheJob($this->cacheTag));

        return $supplier->only(['id', 'name', 'email', 'phone']);
    }

    public function deleteSupplier(int $id): void
    {
        DB::beginTransaction();

        try {
            $supplier = Supplier::find($id);

            if (! $supplier) {
                return;
            }

            $supplier->delete();
            $supplier->address()->delete();

            dispatch(new ClearSuppliersCacheJob($this->cacheTag));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findSupplierById(int $id): ?array
    {
        $supplier = Supplier::with('address')->find($id);

        if (!$supplier) {
            return null;
        }

        $array = $supplier->toArray();
        $array['document'] = $this->maskDocument($array['document'], $array['document_type'] ?? null);

        return $array;
    }

    public function findAllSuppliers(SupplierFilterDTO $filters): array
    {
        $cacheKey = md5(json_encode($filters));

        return cache()->tags([$this->cacheTag])->remember($cacheKey, $this->cacheTtl, function () use ($filters) {
            $colsToReturn = ['id', 'name', 'email', 'phone', 'created_at'];

            $query = Supplier::query();

            $this->applySearchFilters($query, $filters->search);
            $this->applySorting($query, $filters->sortColumn, $filters->sortOrdenation);

            return $query->paginate($filters->perPage, $colsToReturn, 'page', $filters->page)->toArray();
        });
    }

    protected function applySearchFilters(Builder $query, ?string $searchFilter): void
    {
        if (empty($searchFilter)) {
            return;
        }

        $searchableFields = ['name', 'email', 'phone', 'document'];

        $query->where(function ($q) use ($searchableFields, $searchFilter) {
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'like', '%'.$searchFilter.'%');
            }
        });
    }

    protected function applySorting(Builder $query, string $sortColumn, string $sortOrdenation): void
    {
        $allowedSortColumns = ['name', 'email', 'created_at'];

        if (in_array($sortColumn, $allowedSortColumns) && in_array(strtolower($sortOrdenation), ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortOrdenation);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    protected function maskDocument($document, $type) {
        if ($type === 'CPF') {
            return substr($document, 0, 3) . '******' . substr($document, -2);
        }

        return $document;
    }
}
