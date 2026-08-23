<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('management_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->enum('status', [
                'draft', 'requested', 'matching', 'candidate_selected',
                'verification_pending', 'ready', 'active', 'paused',
                'replacement_pending', 'renewal_due', 'renewed',
                'cancelled', 'completed'
            ])->default('draft')->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('schedule')->nullable();
            $table->decimal('service_amount', 12, 2)->nullable();
            $table->decimal('management_fee', 12, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->text('terms')->nullable();
            $table->timestamps();
        });

        Schema::create('replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('management_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('old_provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('new_provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'requested', 'matching', 'candidate_selected',
                'verification_pending', 'approved', 'completed',
                'cancelled'
            ])->default('requested')->index();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('management_plan_id')->constrained()->cascadeOnDelete();
            $table->date('previous_end_date')->nullable();
            $table->date('new_start_date')->nullable();
            $table->date('new_end_date')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('management_fee', 12, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected', 'cancelled'])
                ->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renewals');
        Schema::dropIfExists('replacements');
        Schema::dropIfExists('management_plans');
    }
};
