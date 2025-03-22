<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
final class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->streetAddress(),
            'postcode' => fake()->postcode(),
            'city' => fake()->city(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'url' => fake()->url(),
            'size' => fake()->randomElement(['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+']),
            'type' => fake()->randomElement(['Startup', 'SME', 'Enterprise', 'Agency', 'Consultancy', 'Non-profit']),
            'description_german' => fake()->optional(0.7)->paragraphs(3, true),
            'description_english' => fake()->paragraphs(3, true),
            'description_french' => fake()->optional(0.5)->paragraphs(3, true),
            'description_italian' => fake()->optional(0.5)->paragraphs(3, true),
            'logo' => fake()->optional(0.8)->imageUrl(200, 200, 'business'),
            'cover' => fake()->optional(0.6)->imageUrl(1200, 400, 'business'),
            'video' => fake()->optional(0.3)->url(),
            'newsletter' => fake()->boolean(70),
            'internal_notes' => fake()->optional(0.4)->text(),
            'active' => fake()->boolean(90),
            'blocked' => fake()->boolean(5),
            'email' => fake()->unique()->companyEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
