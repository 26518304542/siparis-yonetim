<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\Models\Product;


class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
                'customer_id' => Customer::factory(),
                'product_id' => Product::factory(),
                'quantity' => fake()->numberBetween(1, 10),
                'status' => fake()->randomElement(['pending', 'completed']),
            ];

    }
}
