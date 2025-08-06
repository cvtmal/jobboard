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
            $table->dropColumn([
                'profile_completed',
                'profile_completed_at',
                'profile_completion_steps',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->boolean('profile_completed')->default(false);
            $table->timestamp('profile_completed_at')->nullable();
            $table->json('profile_completion_steps')->nullable();
        });
    }
};
