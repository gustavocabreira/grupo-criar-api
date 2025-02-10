<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State>
 */
class StateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'acronym' => strtoupper(substr(fake()->word, 0, 1)).strtoupper(fake()->randomLetter()),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
