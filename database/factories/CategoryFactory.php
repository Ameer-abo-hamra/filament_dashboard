<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Electronics',
                'Clothing',
                'Home Appliances',
                'Books',
                'Toys',
                'Sports',
                'Beauty & Health'
            ])
            ,
            "description" => $this->faker->sentence(10),
            "color" => $this->faker->colorName(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
