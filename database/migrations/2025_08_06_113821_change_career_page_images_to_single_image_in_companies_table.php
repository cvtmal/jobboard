<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new single image column
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('career_page_image')->nullable()->after('career_page_slug');
        });

        // Migrate existing data: take the first image from the array if it exists
        DB::table('companies')
            ->whereNotNull('career_page_images')
            ->where('career_page_images', '!=', 'null')
            ->where('career_page_images', '!=', '[]')
            ->chunkById(100, function ($companies): void {
                foreach ($companies as $company) {
                    $images = json_decode($company->career_page_images, true);

                    if (is_array($images) && $images !== []) {
                        // Take the first image from the array
                        $firstImage = $images[0];

                        DB::table('companies')
                            ->where('id', $company->id)
                            ->update(['career_page_image' => $firstImage]);
                    }
                }
            });

        // Drop the old array column
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('career_page_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the array column
        Schema::table('companies', function (Blueprint $table): void {
            $table->json('career_page_images')->nullable()->after('career_page_slug');
        });

        // Migrate existing data: convert single image to array format
        DB::table('companies')
            ->whereNotNull('career_page_image')
            ->chunkById(100, function ($companies): void {
                foreach ($companies as $company) {
                    if (! empty($company->career_page_image)) {
                        $imageArray = [$company->career_page_image];

                        DB::table('companies')
                            ->where('id', $company->id)
                            ->update(['career_page_images' => json_encode($imageArray)]);
                    }
                }
            });

        // Drop the single image column
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('career_page_image');
        });
    }
};
