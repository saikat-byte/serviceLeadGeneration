<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->nullable();
            $table->text('address')->nullable();
            $table->string('locality')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });

        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'unavailable', 'offline'])
                ->default('offline')->index();
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('completed_jobs_count')->default(0);
            $table->unsignedInteger('cancellation_count')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
        Schema::dropIfExists('locations');
    }
};
