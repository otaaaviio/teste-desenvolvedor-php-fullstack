<?php

namespace App\Modules\Supplier\Http\Controllers;

use App\Modules\Core\Http\Controllers\BaseController;
use App\Modules\Supplier\DTOs\CreateSupplierDTO;
use App\Modules\Supplier\DTOs\SupplierFilterDTO;
use App\Modules\Supplier\DTOs\UpdateSupplierDTO;
use App\Modules\Supplier\Enums\DocumentType;
use App\Modules\Supplier\Exceptions\DocumentException;
use App\Modules\Supplier\Exceptions\SupplierException;
use App\Modules\Supplier\Http\Requests\StoreSupplierRequest;
use App\Modules\Supplier\Repositories\Contracts\SupplierRepository as SupplierRepositoryContract;
use App\Modules\Supplier\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class SupplierController extends BaseController
{
    protected SupplierRepositoryContract $supplierRepository;

    protected DocumentService $documentService;

    public function __construct(SupplierRepositoryContract $supplierRepository, DocumentService $documentService)
    {
        $this->documentService = $documentService;
        $this->supplierRepository = $supplierRepository;
    }

    public function create(StoreSupplierRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dto = CreateSupplierDTO::make($data);

        $this->validateDocument($dto);

        $supplier = $this->supplierRepository->createSupplier($dto);

        return $this->success($supplier, 'Supplier created successfully', StatusCode::HTTP_CREATED);
    }

    public function update(StoreSupplierRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dto = UpdateSupplierDTO::make($data);

        $supplier = $this->supplierRepository->updateSupplier($dto);

        return $this->success($supplier, 'Supplier updated successfully', StatusCode::HTTP_OK);
    }

    public function delete(Request $request): JsonResponse
    {
        $supplier_id = (int) $request->route('supplier_id');

        $this->supplierRepository->deleteSupplier($supplier_id);

        return response()->json(null, StatusCode::HTTP_NO_CONTENT);
    }

    public function findAll(Request $request): JsonResponse
    {
        $filters = SupplierFilterDTO::make($request->query());

        $suppliers = $this->supplierRepository->findAllSuppliers($filters);

        return $this->success($suppliers);
    }

    public function findById(Request $request): JsonResponse
    {
        $supplier_id = (int) $request->route('supplier_id');

        $supplier = $this->supplierRepository->findSupplierById($supplier_id);

        if (! $supplier) {
            throw SupplierException::notFound($supplier_id);
        }

        return $this->success($supplier);
    }

    public function getSupplierByCnpj(string $cnpj): JsonResponse
    {
        $cleanedCnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cleanedCnpj) !== 14) {
            throw DocumentException::invalidDocument(DocumentType::CNPJ);
        }

        $supplier = $this->documentService->getInfosByCnpj($cleanedCnpj);

        return $this->success($supplier);
    }

    protected function validateDocument(CreateSupplierDTO $dto): void
    {
        $isValid = match ($dto->documentType) {
            DocumentType::CNPJ => $this->documentService->validateCnpjAddress($dto->document, $dto->address),
            DocumentType::CPF => $this->documentService->validateCpf($dto->document),
        };

        if (! $isValid) {
            throw DocumentException::invalidDocument($dto->documentType);
        }
    }
}
