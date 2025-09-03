<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhotoModel>
 */
class PhotoModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'image_path' => 'photos/test-image.jpg',
            'model_id' => null,
            'status' => $this->faker->randomElement(['pending', 'training', 'completed', 'failed']),
            'training_metadata' => null,
        ];
    }

    /**
     * Indicate that the model is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the model is training.
     */
    public function training(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'training',
        ]);
    }

    /**
     * Indicate that the model is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'model_id' => 'test-model-' . $this->faker->uuid(),
        ]);
    }

    /**
     * Indicate that the model failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
