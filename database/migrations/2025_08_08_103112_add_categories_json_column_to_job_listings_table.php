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
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->json('categories')->nullable()->after('company_id');
        });

        // Add index for JSON column based on database driver
        if (DB::getDriverName() === 'mysql') {
            DB::statement('CREATE INDEX job_listings_categories_index ON job_listings ((CAST(categories AS CHAR(255) ARRAY)))');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX job_listings_categories_index ON job_listings USING GIN (categories)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->dropColumn('categories');
        });
    }
};
