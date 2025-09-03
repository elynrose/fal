<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'training', 'completed', 'failed']),
            'model_id' => fake()->optional()->uuid(),
        ];
    }

    /**
     * Indicate that the album is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the album is training.
     */
    public function training(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'training',
        ]);
    }

    /**
     * Indicate that the album is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'model_id' => fake()->uuid(),
        ]);
    }

    /**
     * Indicate that the album is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
