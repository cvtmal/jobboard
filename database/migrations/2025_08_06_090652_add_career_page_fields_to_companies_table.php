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
            $table->boolean('career_page_enabled')->default(true);
            $table->string('career_page_slug')->unique()->nullable();
            $table->json('career_page_images')->nullable();
            $table->json('career_page_videos')->nullable();
            $table->string('career_page_domain')->nullable();
            $table->boolean('spontaneous_application_enabled')->default(false);
            $table->boolean('career_page_visibility')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'career_page_enabled',
                'career_page_slug',
                'career_page_images',
                'career_page_videos',
                'career_page_domain',
                'spontaneous_application_enabled',
                'career_page_visibility',
            ]);
        });
    }
};
