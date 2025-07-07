<?php

use App\Modules\Supplier\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as StatusCode;

Route::get('/healthcheck', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ])->setStatusCode(StatusCode::HTTP_OK);
});

Route::prefix('v1')->group(function () {
    Route::prefix('suppliers')->group(function () {
        Route::post('/', [SupplierController::class, 'create']);
        Route::put('/{supplier_id}', [SupplierController::class, 'update']);
        Route::delete('/{supplier_id}', [SupplierController::class, 'delete']);
        Route::get('/', [SupplierController::class, 'findAll']);
        Route::get('/{supplier_id}', [SupplierController::class, 'findById']);
        Route::get('/cnpj/{cnpj}', [SupplierController::class, 'getSupplierByCnpj']);
    });
});
