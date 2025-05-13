<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name,
            'product' => $this->faker->word,
            'quantity' => $this->faker->numberBetween(1, 10),
            'status' => 'pending',
        ];
    }
}
