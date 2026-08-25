<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ProviderAndCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $now = Carbon::now();
        $serviceIds = DB::table('services')->pluck('id')->toArray();
        
        if (empty($serviceIds)) return;

        // 1. Generate Customers (~40)
        for ($i = 0; $i < 40; $i++) {
            $custId = DB::table('users')->insertGetId([
                'name' => $faker->name,
                'email' => "customer_" . uniqid() . "@example.com", // Unique email
                'mobile' => '9' . $faker->unique()->numerify('#########'), // 10 digit
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active', // FIXED: Ensured it's exactly 'active'
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Customer Location
            DB::table('locations')->insert([
                'user_id' => $custId,
                'label' => $faker->randomElement(['Home', 'Office', 'Other']),
                'address' => $faker->streetAddress,
                'city' => 'Kolkata',
                'postal_code' => $faker->numerify('700###'),
                'is_default' => 1,
                'created_at' => $now,
            ]);
        }

        // 2. Generate Providers (~25)
        for ($i = 0; $i < 25; $i++) {
            $provId = DB::table('users')->insertGetId([
                'name' => $faker->name,
                'email' => "provider_" . uniqid() . "@example.com", // Unique email
                'mobile' => '8' . $faker->unique()->numerify('#########'), // 10 digit
                'password' => Hash::make('password'),
                'role' => 'provider',
                'status' => 'active', // FIXED: Changed from random 'inactive' to 'active'
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Provider Profile
            DB::table('provider_profiles')->insert([
                'user_id' => $provId,
                'bio' => $faker->realText(100),
                'experience_years' => $faker->numberBetween(1, 15),
                'availability_status' => $faker->randomElement(['available', 'busy', 'offline']),
                'rating_average' => $faker->randomFloat(2, 3.5, 5.0),
                'completed_jobs_count' => $faker->numberBetween(0, 150),
                'response_rate' => $faker->randomFloat(2, 80, 100),
                'created_at' => $now,
            ]);

            // Provider Services
            $assignedServices = $faker->randomElements($serviceIds, $faker->numberBetween(1, 3));
            foreach ($assignedServices as $sId) {
                DB::table('provider_services')->insertOrIgnore([
                    'provider_id' => $provId,
                    'service_id' => $sId,
                    'status' => 'approved',
                    'starting_price' => $faker->randomElement([200, 300, 500, 1000]),
                    'created_at' => $now,
                ]);
            }

            // Provider Area
            DB::table('provider_service_areas')->insert([
                'provider_id' => $provId,
                'city' => 'Kolkata',
                'radius_km' => $faker->numberBetween(5, 25),
                'created_at' => $now,
            ]);

            // Provider Availabilities
            for ($day = 1; $day <= 5; $day++) {
                DB::table('provider_availabilities')->insert([
                    'provider_id' => $provId,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00',
                    'is_available' => 1,
                    'created_at' => $now,
                ]);
            }
        }
    }
}