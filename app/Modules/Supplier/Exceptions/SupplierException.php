<?php

namespace App\Modules\Supplier\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as StatusCode;
use Throwable;

class SupplierException extends Exception
{
    public function __construct($message = '', $code = StatusCode::HTTP_INTERNAL_SERVER_ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function notFound(int $supplierId): self
    {
        return new self("Supplier with ID {$supplierId} not found.", StatusCode::HTTP_NOT_FOUND);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
