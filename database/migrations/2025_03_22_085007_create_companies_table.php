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
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('url')->nullable();
            $table->string('size')->nullable();
            $table->string('type')->nullable();
            $table->text('description_german')->nullable();
            $table->text('description_english')->nullable();
            $table->text('description_french')->nullable();
            $table->text('description_italian')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->string('video')->nullable();
            $table->boolean('newsletter')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('blocked')->default(false);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('company_password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('company_password_reset_tokens');
    }
};
