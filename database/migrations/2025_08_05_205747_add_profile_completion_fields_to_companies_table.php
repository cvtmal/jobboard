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
        Schema::table('companies', function (Blueprint $table): void {
            // Profile completion tracking
            $table->boolean('profile_completed')->default(false)->after('blocked');
            $table->timestamp('profile_completed_at')->nullable()->after('profile_completed');
            $table->json('profile_completion_steps')->nullable()->after('profile_completed_at');

            // Additional profile fields (first_name, last_name, phone_number already exist)
            $table->string('industry')->nullable()->after('type');
            $table->year('founded_year')->nullable()->after('industry');
            $table->text('mission_statement')->nullable()->after('description_italian');
            $table->json('benefits')->nullable()->after('mission_statement');
            $table->json('company_culture')->nullable()->after('benefits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'profile_completed',
                'profile_completed_at',
                'profile_completion_steps',
                'industry',
                'founded_year',
                'mission_statement',
                'benefits',
                'company_culture',
            ]);
        });
    }
};
