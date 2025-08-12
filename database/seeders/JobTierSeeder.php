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
                'description' => 'Essential features to get your job posted quickly.',
                'price' => 149.00,
                'duration_days' => 30,
                'featured' => false,
                'max_applications' => 50,
                'max_active_jobs' => 1,
                'has_analytics' => false,
            ],
            [
                'name' => 'Professional',
                'description' => 'Enhanced visibility and more applications for better hiring.',
                'price' => 299.00,
                'duration_days' => 45,
                'featured' => true,
                'max_applications' => 200,
                'max_active_jobs' => 3,
                'has_analytics' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'Maximum exposure with unlimited applications and priority support.',
                'price' => 599.00,
                'duration_days' => 60,
                'featured' => true,
                'max_applications' => null, // unlimited
                'max_active_jobs' => 10,
                'has_analytics' => true,
            ],
        ];

        foreach ($jobTiers as $tierData) {
            JobTier::query()->updateOrCreate(
                ['name' => $tierData['name']],
                $tierData
            );
        }
    }
}
