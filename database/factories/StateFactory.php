<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State>
 */
class StateFactory extends Factory
{
    public function definition(): array
    {
        $word = strtoupper(substr(fake()->word, 0, 1)).strtoupper(fake()->randomLetter());

        while(State::query()->where('acronym', $word)->exists()) {
            $word = strtoupper(substr(fake()->word, 0, 1)).strtoupper(fake()->randomLetter());
        }

        return [
            'name' => fake()->name,
            'acronym' => $word,
        ];
    }
}
