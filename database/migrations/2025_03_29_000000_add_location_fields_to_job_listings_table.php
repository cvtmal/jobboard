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
            $table->string('primary_canton_code', 2)->nullable()->after('city');
            $table->decimal('primary_latitude', 10, 7)->nullable()->after('primary_canton_code');
            $table->decimal('primary_longitude', 10, 7)->nullable()->after('primary_latitude');

            $table->boolean('has_multiple_locations')->default(false)->after('primary_longitude');
            $table->boolean('allows_remote')->default(false)->after('has_multiple_locations');

            $table->index('primary_canton_code');
            $table->index(['allows_remote', 'has_multiple_locations']);
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->dropColumn([
                'primary_canton_code',
                'primary_latitude',
                'primary_longitude',
                'has_multiple_locations',
                'allows_remote',
            ]);
        });
    }
};
