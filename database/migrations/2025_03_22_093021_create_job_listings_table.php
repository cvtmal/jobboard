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
        Schema::create('job_listings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->unique()->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('employment_type')->nullable();
            $table->integer('workload_min')->nullable(); // In percentage (e.g. 80 for 80%)
            $table->integer('workload_max')->nullable(); // In percentage (e.g. 100 for 100%)
            $table->date('active_from')->nullable();
            $table->date('active_until')->nullable();
            $table->string('workplace')->nullable();
            $table->string('hierarchy')->nullable(); // e.g. "Reports to CTO"
            $table->string('experience_level')->nullable();
            $table->integer('experience_years_min')->nullable();
            $table->integer('experience_years_max')->nullable();
            $table->string('education_level')->nullable(); // e.g. "Bachelor's degree"
            $table->json('languages')->nullable(); // JSON array of required languages
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->boolean('no_salary')->default(false); // True if company doesn't want to mention salary
            $table->string('salary_type')->nullable();
            $table->string('salary_option')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('job_tier')->nullable(); // Based on subscription level
            $table->string('application_process')->default('both');
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
