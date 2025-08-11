<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\JobCategory;
use App\Enums\JobStatus;
use App\Enums\SwissCanton;
use App\Enums\Workplace;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test company
        $company = Company::firstOrCreate(
            ['email' => 'damian.ermanni@myitjob.ch'],
            [
                'name' => 'myitjob',
                'first_name' => 'Damian',
                'last_name' => 'Ermanni',
                'email' => 'damian.ermanni@myitjob.ch',
                'phone_number' => '0797166222',
                'password' => Hash::make('super888'),
                'email_verified_at' => '2025-08-11 00:00:00',
                'active' => true,
                'blocked' => false,
            ]
        );

        JobListing::firstOrCreate(
            [
                'company_id' => $company->id,
                'title' => 'Senior PHP Developer',
            ],
            [
                'company_id' => $company->id,
                'title' => 'Senior PHP Developer',
                'description' => 'We are looking for an experienced PHP developer with Laravel expertise to join our team. The ideal candidate will have strong knowledge of PHP 8.4, Laravel, and modern frontend frameworks.',
                'employment_type' => EmploymentType::PERMANENT,
                'workload_min' => 80,
                'workload_max' => 100,
                'workplace' => Workplace::HYBRID,
                'allows_remote' => true,
                'no_salary' => false,
                'salary_min' => 100000,
                'salary_max' => 130000,
                'salary_currency' => 'CHF',
                'application_process' => ApplicationProcess::EMAIL,
                'application_email' => 'jobs@myitjob.ch',
                'contact_person' => 'Damian Ermanni',
                'contact_email' => 'damian.ermanni@myitjob.ch',
                'status' => JobStatus::DRAFT,
                'use_company_logo' => true,
                'use_company_banner' => true,
            ]
        );
    }
}
