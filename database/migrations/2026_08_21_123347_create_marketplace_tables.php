<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'validating', 'qualified', 'rejected', 'cancelled', 'expired'])
                ->default('draft')->index();
            $table->enum('urgency', ['normal', 'urgent', 'emergency'])->default('normal')->index();
            $table->timestamp('preferred_at')->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'created', 'qualified', 'matching', 'distributed', 'responding',
                'interested', 'selected', 'converted', 'expired', 'cancelled', 'unfulfilled'
            ])->default('created')->index();
            $table->unsignedSmallInteger('quality_score')->nullable();
            $table->timestamp('distributed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', [
                'created', 'ranked', 'offered', 'responded', 'accepted',
                'rejected', 'expired', 'selected', 'not_selected'
            ])->default('created')->index();
            $table->decimal('match_score', 6, 2)->nullable();
            $table->decimal('location_score', 6, 2)->nullable();
            $table->decimal('availability_score', 6, 2)->nullable();
            $table->decimal('skill_score', 6, 2)->nullable();
            $table->decimal('trust_score', 6, 2)->nullable();
            $table->decimal('price_score', 6, 2)->nullable();
            $table->unsignedSmallInteger('rank')->nullable();
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['lead_id', 'provider_id']);
            $table->index(['lead_id', 'status']);
        });

        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->enum('actor_type', ['provider', 'customer']);
            $table->enum('status', ['expressed', 'active', 'withdrawn', 'rejected', 'expired', 'selected'])
                ->default('expressed')->index();
            $table->timestamp('expressed_at')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'provider_id']);
        });

        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'unlocked', 'active', 'closed', 'blocked'])
                ->default('pending')->index();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
        Schema::dropIfExists('interests');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('service_requests');
    }
};
