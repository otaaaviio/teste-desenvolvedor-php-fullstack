<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_addresses', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->unique(['supplier_id']);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_addresses', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropUnique(['supplier_id']);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }
};
