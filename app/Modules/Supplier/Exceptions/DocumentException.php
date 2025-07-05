<?php

namespace App\Modules\Supplier\Exceptions;

use App\Modules\Supplier\Enums\DocumentType;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as StatusCode;
use Throwable;

class DocumentException extends Exception
{
    public function __construct($message = '', $code = StatusCode::HTTP_INTERNAL_SERVER_ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidDocument(DocumentType $documentType): self
    {
        return new self("invalid {$documentType->value}.", StatusCode::HTTP_BAD_REQUEST);
    }

    public static function documentAlreadyExists(DocumentType $documentType): self
    {
        return new self("{$documentType->value} already exists.", StatusCode::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function documentNotFound(DocumentType $documentType): self
    {
        return new self("{$documentType->value} not found.", StatusCode::HTTP_NOT_FOUND);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
