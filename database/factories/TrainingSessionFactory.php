<?php

namespace Database\Factories;

use App\Models\PhotoModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingSession>
 */
class TrainingSessionFactory extends Factory
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
            'photo_model_id' => PhotoModel::factory(),
            'session_id' => 'session-' . $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'running', 'completed', 'failed']),
            'training_config' => [
                'model_name' => $this->faker->words(2, true),
                'image_url' => 'https://example.com/test-image.jpg'
            ],
            'training_results' => null,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    /**
     * Indicate that the session is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the session is running.
     */
    public function running(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Indicate that the session is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
            'training_results' => [
                'model_id' => 'test-model-' . $this->faker->uuid(),
                'status' => 'completed'
            ],
        ]);
    }

    /**
     * Indicate that the session failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'error_message' => 'Training failed due to insufficient data',
        ]);
    }
}
