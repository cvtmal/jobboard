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
            $table->json('application_documents')->nullable()->after('status');
            $table->json('screening_questions')->nullable()->after('application_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table): void {
            $table->dropColumn('screening_questions');
            $table->dropColumn('application_documents');
        });
    }
};
