<?php

namespace Database\Factories;

use App\Modules\Supplier\Enums\DocumentType;
use App\Modules\Supplier\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        $documentType = $this->faker->randomElement(DocumentType::cases());

        if ($documentType === DocumentType::CNPJ) {
            $document = $this->faker->unique()->numerify('##############');
        } else {
            $document = $this->faker->unique()->numerify('###########');
        }

        return [
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'document' => $document,
            'document_type' => $documentType->value,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
