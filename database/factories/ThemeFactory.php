<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theme>
 */
class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'prompt_template' => $this->faker->sentence() . ' ' . $this->faker->words(3, true),
            'icon' => $this->faker->randomElement(['briefcase', 'plane', 'tshirt', 'smile', 'palette', 'trophy', 'heart', 'tree']),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the theme is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
