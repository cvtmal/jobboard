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
        Schema::table('job_listings', function (Blueprint $table): void {
            // First, create a temporary column for the job_tier_id
            $table->foreignId('job_tier_id')->nullable();

            // Add foreign key constraint
            $table->foreign('job_tier_id')
                ->references('id')
                ->on('job_tiers')
                ->onDelete('set null');

            // We'll drop the job_tier column in a separate migration after data migration is complete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->dropForeign(['job_tier_id']);
            $table->dropColumn('job_tier_id');

            // Note: We don't restore the original job_tier column here as it would
            // be complex to restore the enum data correctly
        });
    }
};
