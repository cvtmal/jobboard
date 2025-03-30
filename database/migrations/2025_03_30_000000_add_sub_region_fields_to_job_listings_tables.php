<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->string('primary_sub_region', 30)->nullable()->after('primary_canton_code');
            $table->index('primary_sub_region');
        });

        Schema::table('job_listing_additional_locations', function (Blueprint $table): void {
            $table->string('sub_region', 30)->nullable()->after('canton_code');
            $table->index('sub_region');
        });
    }

    public function down(): void
    {
        Schema::table('job_listing_additional_locations', function (Blueprint $table): void {
            $table->dropColumn('sub_region');
        });

        Schema::table('job_listings', function (Blueprint $table): void {
            $table->dropColumn('primary_sub_region');
        });
    }
};
