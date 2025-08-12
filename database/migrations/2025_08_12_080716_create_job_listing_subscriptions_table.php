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
        Schema::create('job_listing_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_listing_id')
                ->constrained('job_listings')
                ->cascadeOnDelete();
            $table->foreignId('job_tier_id')
                ->constrained('job_tiers');
            $table->timestamp('purchased_at');
            $table->timestamp('expires_at');
            $table->decimal('price_paid', 10, 2);
            $table->decimal('discount_applied', 10, 2)->nullable();
            $table->string('promo_code')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();

            // Add indexes for performance
            $table->index(['job_listing_id', 'payment_status']);
            $table->index('expires_at');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listing_subscriptions');
    }
};
