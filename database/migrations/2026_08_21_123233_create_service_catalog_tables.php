<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('service_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['service_id', 'slug']);
        });

        Schema::create('service_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('service_type', ['on_demand', 'scheduled', 'recurring', 'project'])->index();
            $table->enum('pricing_type', ['fixed', 'negotiable', 'inspection', 'quotation', 'monthly'])->index();
            $table->enum('revenue_model', ['lead_fee', 'commission', 'management_fee', 'placement_fee'])->index();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('fixed_lead_fee', 12, 2)->nullable();
            $table->decimal('management_fee', 12, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->unsignedSmallInteger('provider_response_minutes')->nullable();
            $table->unsignedSmallInteger('lead_expiry_minutes')->nullable();
            $table->boolean('requires_provider_verification')->default(false);
            $table->boolean('requires_customer_confirmation')->default(true);
            $table->boolean('requires_payment_before_booking')->default(false);
            $table->json('customer_requirements')->nullable();
            $table->json('provider_requirements')->nullable();
            $table->json('availability_rules')->nullable();
            $table->json('cancellation_rules')->nullable();
            $table->json('completion_rules')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_definitions');
        Schema::dropIfExists('service_variants');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
