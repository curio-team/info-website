<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'year' => fake()->numberBetween(1, 4),
            'path_nl' => Str::random(40).'.zip',
            'path_en' => null,
            'allow_unsafe' => false,
            'publisher_id' => User::factory()->teacher(),
        ];
    }

    /**
     * Indicate that the site also has an English version.
     */
    public function withEnglish(): static
    {
        return $this->state(fn (array $attributes) => [
            'path_en' => Str::random(40).'.zip',
        ]);
    }
}
