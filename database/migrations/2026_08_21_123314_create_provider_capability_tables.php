<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('provider_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending')->index();
            $table->decimal('starting_price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['provider_id', 'service_id']);
        });

        Schema::create('provider_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])
                ->default('unverified')->index();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->timestamps();

            $table->unique(['provider_id', 'skill_id']);
        });

        Schema::create('provider_service_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->string('locality')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('postal_code', 20)->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('radius_km')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });

        Schema::create('provider_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('specific_date')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index(['provider_id', 'specific_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_availabilities');
        Schema::dropIfExists('provider_service_areas');
        Schema::dropIfExists('provider_skills');
        Schema::dropIfExists('provider_services');
        Schema::dropIfExists('skills');
    }
};
