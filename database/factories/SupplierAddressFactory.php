<?php

namespace Database\Factories;

use App\Modules\Supplier\Models\SupplierAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierAddress>
 */
class SupplierAddressFactory extends Factory
{
    protected $model = SupplierAddress::class;

    public function definition(): array
    {
        return [
            'street' => $this->faker->streetAddress,
            'number' => $this->faker->buildingNumber,
            'complement' => $this->faker->optional()->secondaryAddress,
            'neighborhood' => $this->faker->word,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip_code' => substr($this->faker->postcode, 0, 9),
        ];
    }
}
