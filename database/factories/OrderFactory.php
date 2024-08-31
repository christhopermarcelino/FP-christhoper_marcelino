<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 3),
            'product_id' => fake()->numberBetween(1, 10),
            'quantity' => fake()->randomNumber(2, false),
            'total_price' => fake()->randomFloat(2, 1000, 100000),
            'order_date' => fake()->dateTime(),
        ];
    }
}
