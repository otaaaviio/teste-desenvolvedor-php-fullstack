<?php

namespace App\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class BaseController
{
    public function success(mixed $data = null, string $message = 'Success', int $code = StatusCode::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
