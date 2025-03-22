<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\JobTier;
use Illuminate\Database\Seeder;

final class JobTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTiers = [
            [
                'name' => 'Basic',
                'description' => 'Basic job listing with essential features.',
                'price' => 99.00,
                'duration_days' => 30,
                'featured' => false,
                'max_applications' => 100,
                'max_active_jobs' => 1,
                'has_analytics' => false,
            ],
            [
                'name' => 'Premium',
                'description' => 'Premium job listing with enhanced visibility and more applications.',
                'price' => 199.00,
                'duration_days' => 60,
                'featured' => true,
                'max_applications' => 300,
                'max_active_jobs' => 3,
                'has_analytics' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Enterprise job listing with maximum visibility, unlimited applications, and comprehensive analytics.',
                'price' => 499.00,
                'duration_days' => 90,
                'featured' => true,
                'max_applications' => null, // unlimited
                'max_active_jobs' => 10,
                'has_analytics' => true,
            ],
        ];

        foreach ($jobTiers as $tierData) {
            JobTier::query()->create($tierData);
        }
    }
}
