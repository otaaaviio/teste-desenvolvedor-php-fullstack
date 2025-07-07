<?php

namespace App\Modules\Supplier\Models;

use App\Modules\Supplier\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'document',
        'document_type',
        'email',
        'phone',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'document_type' => DocumentType::class,
    ];

    public function address(): HasOne
    {
        return $this->hasOne(SupplierAddress::class);
    }
}
