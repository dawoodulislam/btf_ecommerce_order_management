<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'sku' => strtoupper($this->faker->unique()->lexify('PROD???')),
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'vendor_id' => 1, // default, can override in seeder
            'active' => true,
            'meta' => [],
        ];
    }
}
