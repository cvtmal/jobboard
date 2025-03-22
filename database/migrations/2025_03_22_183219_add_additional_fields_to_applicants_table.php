<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table): void {
            // Remove name field
            $table->dropColumn('name');

            // Add new fields
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('mobile_phone')->nullable()->after('phone');
            $table->string('headline')->nullable()->after('mobile_phone');
            $table->text('bio')->nullable()->after('headline');
            $table->boolean('work_permit')->default(false)->after('bio');
            $table->string('employment_type_preference')->nullable()->after('work_permit');
            $table->string('workplace_preference')->nullable()->after('employment_type_preference');
            $table->date('available_from')->nullable()->after('workplace_preference');
            $table->decimal('salary_expectation', 10, 2)->nullable()->after('available_from');
            $table->string('resume_path')->nullable()->after('salary_expectation');
            $table->string('profile_photo_path')->nullable()->after('resume_path');
            $table->string('portfolio_url')->nullable()->after('profile_photo_path');
            $table->string('linkedin_url')->nullable()->after('portfolio_url');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->string('website_url')->nullable()->after('github_url');
            $table->date('date_of_birth')->nullable()->after('website_url');
            $table->string('address')->nullable()->after('date_of_birth');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table): void {
            // Remove new fields
            $table->string('name')->after('id');

            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'mobile_phone',
                'headline',
                'bio',
                'work_permit',
                'employment_type_preference',
                'workplace_preference',
                'available_from',
                'salary_expectation',
                'resume_path',
                'profile_photo_path',
                'portfolio_url',
                'linkedin_url',
                'github_url',
                'website_url',
                'date_of_birth',
                'address',
                'city',
                'state',
                'postal_code',
                'country',
            ]);
        });
    }
};
