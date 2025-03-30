<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listing_additional_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_listing_id')->constrained()->onDelete('cascade');
            $table->string('canton_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index('canton_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listing_additional_locations');
    }
};
