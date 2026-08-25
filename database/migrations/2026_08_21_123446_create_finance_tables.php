<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('payment_method')->nullable();
            $table->string('gateway')->nullable();
            $table->string('gateway_reference')->nullable()->index();
            $table->enum('status', [
                'initiated', 'pending', 'processing', 'paid', 'failed',
                'cancelled', 'refund_pending', 'refunded', 'partially_refunded'
            ])->default('initiated')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', [
                'pending', 'authorized', 'completed', 'failed',
                'reversed', 'refunded', 'partially_refunded'
            ])->default('pending')->index();
            $table->string('reference')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // MOVED UP: Settlements must be created before commissions
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'eligible', 'processing', 'settled', 'failed', 'reversed'])
                ->default('pending')->index();
            $table->string('payout_reference')->nullable()->index();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete();
            
            // NEW: Batch Settlement Relation (Multiple commissions belong to 1 settlement)
            $table->foreignId('settlement_id')->nullable()->constrained()->nullOnDelete();
            
            $table->enum('model', ['lead_fee', 'commission', 'management_fee', 'placement_fee']);
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->decimal('rate', 5, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'calculated', 'earned', 'adjusted', 'reversed', 'settled'])
                ->default('pending')->index();
            $table->timestamp('earned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled', 'refunded'])
                ->default('draft')->index();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
    }
};