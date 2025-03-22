<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
final class AdminFactory extends Factory
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
            'email' => fake()->unique()->safeEmail(),
            // Admin accounts are pre-verified by default
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * Note: Admins don't actually need to verify their emails since they
     * don't implement MustVerifyEmail. Since the email_verified_at column
     * is NOT NULL in the database, we set it to a past date instead of null.
     * This method exists for testing purposes and consistency with other user models.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            // Use a very old verification date instead of null
            'email_verified_at' => '2000-01-01 00:00:00',
        ]);
    }
}
