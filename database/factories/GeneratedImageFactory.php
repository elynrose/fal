<?php

namespace Database\Factories;

use App\Models\PhotoModel;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GeneratedImage>
 */
class GeneratedImageFactory extends Factory
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
            'theme_id' => Theme::factory(),
            'image_path' => null,
            'prompt_used' => $this->faker->sentence(),
            'generation_parameters' => [
                'prompt' => $this->faker->sentence(),
                'theme' => $this->faker->word()
            ],
            'generation_id' => null,
            'status' => $this->faker->randomElement(['pending', 'generating', 'completed', 'failed']),
            'error_message' => null,
            'generated_at' => null,
        ];
    }

    /**
     * Indicate that the generation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the generation is in progress.
     */
    public function generating(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'generating',
            'generation_id' => 'gen-' . $this->faker->uuid(),
        ]);
    }

    /**
     * Indicate that the generation is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'image_path' => 'generated/test-image.jpg',
            'generation_id' => 'gen-' . $this->faker->uuid(),
            'generated_at' => now(),
        ]);
    }

    /**
     * Indicate that the generation failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'error_message' => 'Generation failed due to invalid prompt',
        ]);
    }
}
