<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('connection_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', [
                'pending', 'confirmed', 'provider_assigned', 'scheduled',
                'on_the_way', 'arrived', 'work_started', 'work_completed',
                'payment_pending', 'paid', 'closed', 'cancelled', 'no_show',
                'disputed', 'failed'
            ])->default('pending')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->decimal('estimated_amount', 12, 2)->nullable();
            $table->decimal('final_amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'created', 'scheduled', 'arrived', 'started',
                'completed', 'verified', 'closed', 'disputed', 'cancelled'
            ])->default('created')->index();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('final_service_value', 12, 2)->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('completion_evidence')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason_code')->nullable()->index();
            $table->text('reason')->nullable();
            $table->decimal('fee', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('state_transitions', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('from_state')->nullable();
            $table->string('to_state');
            $table->string('event');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('state_transitions');
        Schema::dropIfExists('booking_cancellations');
        Schema::dropIfExists('service_jobs');
        Schema::dropIfExists('bookings');
    }
};
