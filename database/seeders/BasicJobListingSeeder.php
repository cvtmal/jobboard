<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationProcess;
use App\Enums\JobStatus;
use App\Enums\SwissCanton;
use App\Enums\SwissSubRegion;
use App\Models\Company;
use App\Models\JobListing;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class BasicJobListingSeeder extends Seeder
{
    /**
     * Seed the application's database with sample job listings.
     */
    public function run(): void
    {
        // First clear existing job listings to avoid duplication
        JobListing::query()->delete();

        // Create a few sample companies first (or use existing ones)
        $companies = collect([
            'Swiss Tech Solutions' => ['ZH', 'Zürich'],
            'Alpine Software AG' => ['BE', 'Bern'],
            'Geneva Digital' => ['GE', 'Geneva'],
            'Lugano Systems' => ['TI', 'Lugano'],
            'Basel Innovations' => ['BS', 'Basel'],
            'Zug Fintech' => ['ZG', 'Zug'],
            'St. Gallen Consulting' => ['SG', 'St. Gallen'],
            'Lausanne Tech' => ['VD', 'Lausanne'],
        ]);

        $companyModels = collect();

        foreach ($companies as $name => $details) {
            [$canton, $city] = $details;
            $email = mb_strtolower(str_replace(' ', '.', $name)).'@example.com';

            // Check if company already exists
            $company = Company::where('email', $email)->first();

            if (! $company) {
                $company = Company::create([
                    'name' => $name,
                    'city' => $city,
                    'postcode' => fake()->postcode(),
                    'email' => $email,
                    'password' => bcrypt('password'),
                    'active' => true,
                    'blocked' => false,
                ]);
            }

            $companyModels->push($company);
        }

        // Job titles in tech
        $jobTitles = [
            'Senior PHP Developer',
            'React Frontend Engineer',
            'Full-Stack Laravel Developer',
            'DevOps Engineer',
            'UI/UX Designer',
            'Mobile App Developer',
            'Data Engineer',
            'Machine Learning Specialist',
            'Product Manager',
            'Scrum Master',
            'Technical Project Manager',
            'Software Architect',
            'QA Engineer',
            'Site Reliability Engineer',
            'Backend Developer',
        ];

        // Create Zurich jobs
        $this->createZurichJobs($jobTitles, $companyModels);

        // Create Bern jobs
        $this->createBernJobs($jobTitles, $companyModels);

        // Create Geneva jobs
        $this->createGenevaJobs($jobTitles, $companyModels);

        // Create Basel jobs
        $this->createBaselJobs($jobTitles, $companyModels);

        // Create remote jobs
        $this->createRemoteJobs($jobTitles, $companyModels);

        // Create multi-location jobs
        $this->createMultiLocationJobs($jobTitles, $companyModels);
    }

    private function createZurichJobs(array $jobTitles, $companies): void
    {
        foreach (range(1, 5) as $i) {
            $job = new JobListing([
                'company_id' => $companies->random()->id,
                'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
                'title' => $jobTitles[array_rand($jobTitles)],
                'description' => fake()->paragraphs(3, true),
                'city' => 'Zürich',
                'primary_canton_code' => SwissCanton::ZURICH,
                'primary_sub_region' => SwissSubRegion::ZURICH_CITY,
                'active_from' => CarbonImmutable::now()->subDays(rand(1, 30)),
                'active_until' => CarbonImmutable::now()->addDays(rand(30, 90)),
                'status' => JobStatus::PUBLISHED,
                'allows_remote' => fake()->boolean(30),
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => fake()->safeEmail(),
            ]);

            $job->save();
        }
    }

    private function createBernJobs(array $jobTitles, $companies): void
    {
        foreach (range(1, 3) as $i) {
            $job = new JobListing([
                'company_id' => $companies->random()->id,
                'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
                'title' => $jobTitles[array_rand($jobTitles)],
                'description' => fake()->paragraphs(3, true),
                'city' => 'Bern',
                'primary_canton_code' => SwissCanton::BERN,
                'active_from' => CarbonImmutable::now()->subDays(rand(1, 30)),
                'active_until' => CarbonImmutable::now()->addDays(rand(30, 90)),
                'status' => JobStatus::PUBLISHED,
                'allows_remote' => fake()->boolean(30),
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => fake()->safeEmail(),
            ]);

            $job->save();
        }
    }

    private function createGenevaJobs(array $jobTitles, $companies): void
    {
        foreach (range(1, 3) as $i) {
            $job = new JobListing([
                'company_id' => $companies->random()->id,
                'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
                'title' => $jobTitles[array_rand($jobTitles)],
                'description' => fake()->paragraphs(3, true),
                'city' => 'Geneva',
                'primary_canton_code' => SwissCanton::GENEVA,
                'active_from' => CarbonImmutable::now()->subDays(rand(1, 30)),
                'active_until' => CarbonImmutable::now()->addDays(rand(30, 90)),
                'status' => JobStatus::PUBLISHED,
                'allows_remote' => fake()->boolean(30),
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => fake()->safeEmail(),
            ]);

            $job->save();
        }
    }

    private function createBaselJobs(array $jobTitles, $companies): void
    {
        foreach (range(1, 2) as $i) {
            $job = new JobListing([
                'company_id' => $companies->random()->id,
                'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
                'title' => $jobTitles[array_rand($jobTitles)],
                'description' => fake()->paragraphs(3, true),
                'city' => 'Basel',
                'primary_canton_code' => SwissCanton::BASEL_STADT,
                'active_from' => CarbonImmutable::now()->subDays(rand(1, 30)),
                'active_until' => CarbonImmutable::now()->addDays(rand(30, 90)),
                'status' => JobStatus::PUBLISHED,
                'allows_remote' => fake()->boolean(30),
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => fake()->safeEmail(),
            ]);

            $job->save();
        }
    }

    private function createRemoteJobs(array $jobTitles, $companies): void
    {
        foreach (range(1, 3) as $i) {
            $job = new JobListing([
                'company_id' => $companies->random()->id,
                'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
                'title' => 'Remote '.$jobTitles[array_rand($jobTitles)],
                'description' => fake()->paragraphs(3, true),
                'primary_canton_code' => null,
                'city' => null,
                'active_from' => CarbonImmutable::now()->subDays(rand(1, 30)),
                'active_until' => CarbonImmutable::now()->addDays(rand(30, 90)),
                'status' => JobStatus::PUBLISHED,
                'allows_remote' => true,
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => fake()->safeEmail(),
            ]);

            $job->save();
        }
    }

    private function createMultiLocationJobs(array $jobTitles, $companies): void
    {
        foreach (range(1, 3) as $i) {
            $job = new JobListing([
                'company_id' => $companies->random()->id,
                'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
                'title' => 'Multi-location '.$jobTitles[array_rand($jobTitles)],
                'description' => fake()->paragraphs(3, true),
                'city' => 'Zürich',
                'primary_canton_code' => SwissCanton::ZURICH,
                'primary_sub_region' => SwissSubRegion::ZURICH_CITY,
                'active_from' => CarbonImmutable::now()->subDays(rand(1, 30)),
                'active_until' => CarbonImmutable::now()->addDays(rand(30, 90)),
                'status' => JobStatus::PUBLISHED,
                'has_multiple_locations' => true,
                'allows_remote' => fake()->boolean(50),
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => fake()->safeEmail(),
            ]);

            $job->save();

            // Add 1-2 additional locations
            $additionalLocations = [
                ['Geneva', SwissCanton::GENEVA],
                ['Bern', SwissCanton::BERN],
                ['Basel', SwissCanton::BASEL_STADT],
                ['Lausanne', SwissCanton::VAUD],
                ['Zug', SwissCanton::ZUG],
            ];

            shuffle($additionalLocations);

            foreach (array_slice($additionalLocations, 0, rand(1, 2)) as [$city, $canton]) {
                $job->additionalLocations()->create([
                    'city' => $city,
                    'canton_code' => $canton,
                    'postcode' => fake()->postcode(),
                    'latitude' => fake()->latitude(45.8, 47.8),
                    'longitude' => fake()->longitude(5.9, 10.5),
                ]);
            }
        }
    }
}
