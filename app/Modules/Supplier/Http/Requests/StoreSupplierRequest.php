<?php

namespace App\Modules\Supplier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'document' => [
                'required',
                'string',
                'max:18',
                'min:11',
                Rule::unique('suppliers')->whereNull('deleted_at'),
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:13',

            'address.street' => 'required|string|max:255',
            'address.number' => 'nullable|string|max:20',
            'address.complement' => 'nullable|string|max:255',
            'address.neighborhood' => 'nullable|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.state' => 'required|string|size:2',
            'address.zip_code' => 'required|string|size:9',
        ];
    }
}
