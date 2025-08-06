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
            // Rename existing fields
            $table->renameColumn('logo', 'logo_path');
            $table->renameColumn('cover', 'banner_path');

            // Add logo metadata fields
            $table->string('logo_original_name')->nullable()->after('logo_path');
            $table->unsignedInteger('logo_file_size')->nullable()->after('logo_original_name');
            $table->string('logo_mime_type')->nullable()->after('logo_file_size');
            $table->json('logo_dimensions')->nullable()->after('logo_mime_type');
            $table->timestamp('logo_uploaded_at')->nullable()->after('logo_dimensions');

            // Add banner metadata fields
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
        Schema::table('companies', function (Blueprint $table): void {
            // Remove metadata fields
            $table->dropColumn([
                'logo_original_name',
                'logo_file_size',
                'logo_mime_type',
                'logo_dimensions',
                'logo_uploaded_at',
                'banner_original_name',
                'banner_file_size',
                'banner_mime_type',
                'banner_dimensions',
                'banner_uploaded_at',
            ]);

            // Rename fields back
            $table->renameColumn('logo_path', 'logo');
            $table->renameColumn('banner_path', 'cover');
        });
    }
};
