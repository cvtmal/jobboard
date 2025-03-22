<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobTier>
 */
final class JobTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Basic', 'Standard', 'Premium', 'Enterprise']),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'duration_days' => $this->faker->randomElement([30, 60, 90, 180]),
            'featured' => $this->faker->boolean(20),
            'max_applications' => $this->faker->optional(0.7)->numberBetween(50, 500),
            'max_active_jobs' => $this->faker->numberBetween(1, 20),
            'has_analytics' => $this->faker->boolean(30),
        ];
    }
}
