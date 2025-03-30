<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SwissCanton;
use App\Enums\SwissSubRegion;
use App\Models\JobListing;
use App\Models\JobListingAdditionalLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobListingAdditionalLocation>
 */
final class JobListingAdditionalLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $canton = $this->faker->randomElement(SwissCanton::cases());

        return [
            'job_listing_id' => JobListing::factory(),
            'canton_code' => $canton->value,
            'sub_region' => function (array $attributes) {
                if (isset($attributes['canton_code'])) {
                    $canton = SwissCanton::from($attributes['canton_code']);
                    $subRegions = SwissSubRegion::forCanton($canton);
                    if ($subRegions !== []) {
                        return $this->faker->randomElement($subRegions);
                    }
                }

                return null;
            },
            'city' => $this->faker->city(),
            'postcode' => $this->faker->numerify('####'),
            'latitude' => $this->faker->latitude(45.8, 47.8),
            'longitude' => $this->faker->longitude(5.9, 10.5),
        ];
    }
}
