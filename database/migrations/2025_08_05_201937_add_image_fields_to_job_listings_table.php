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
            // Flags to determine if job listing uses company images or custom images
            $table->boolean('use_company_logo')->default(true)->after('internal_notes');
            $table->boolean('use_company_banner')->default(true)->after('use_company_logo');

            // Custom logo fields (following same pattern as companies table)
            $table->string('logo_path')->nullable()->after('use_company_banner');
            $table->string('logo_original_name')->nullable()->after('logo_path');
            $table->unsignedInteger('logo_file_size')->nullable()->after('logo_original_name');
            $table->string('logo_mime_type')->nullable()->after('logo_file_size');
            $table->json('logo_dimensions')->nullable()->after('logo_mime_type');
            $table->timestamp('logo_uploaded_at')->nullable()->after('logo_dimensions');

            // Custom banner fields (following same pattern as companies table)
            $table->string('banner_path')->nullable()->after('logo_uploaded_at');
            $table->string('banner_original_name')->nullable()->after('banner_path');
            $table->unsignedInteger('banner_file_size')->nullable()->after('banner_original_name');
            $table->string('banner_mime_type')->nullable()->after('banner_file_size');
            $table->json('banner_dimensions')->nullable()->after('banner_mime_type');
            $table->timestamp('banner_uploaded_at')->nullable()->after('banner_dimensions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->dropColumn([
                'use_company_logo',
                'use_company_banner',
                'logo_path',
                'logo_original_name',
                'logo_file_size',
                'logo_mime_type',
                'logo_dimensions',
                'logo_uploaded_at',
                'banner_path',
                'banner_original_name',
                'banner_file_size',
                'banner_mime_type',
                'banner_dimensions',
                'banner_uploaded_at',
            ]);
        });
    }
};
