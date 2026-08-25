<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class RecurringManagementSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $now = Carbon::now();

        $customers = DB::table('users')->where('role', 'customer')->pluck('id')->toArray();
        $providers = DB::table('users')->where('role', 'provider')->pluck('id')->toArray();
        $services = DB::table('services')->pluck('id')->toArray();

        if (empty($customers) || empty($providers) || empty($services)) return;

        // Generate ~15 Management Plans
        for ($i = 0; $i < 15; $i++) {
            $status = $faker->randomElement(['active', 'paused', 'renewal_due', 'renewed', 'replacement_pending']);
            $startDate = (clone $now)->subMonths(rand(1, 6));
            $endDate = (clone $startDate)->addMonths(12);

            $planId = DB::table('management_plans')->insertGetId([
                'customer_id' => $faker->randomElement($customers),
                'provider_id' => $faker->randomElement($providers),
                'service_id' => $faker->randomElement($services),
                'status' => $status,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'service_amount' => 1500.00,
                'management_fee' => 150.00,
                'currency' => 'INR',
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            // If the plan is renewed, create a valid renewal record
            if ($status === 'renewed') {
                DB::table('renewals')->insert([
                    'management_plan_id' => $planId,
                    'previous_end_date' => $endDate->toDateString(),
                    'new_start_date' => (clone $endDate)->addDay()->toDateString(),
                    'new_end_date' => (clone $endDate)->addYear()->toDateString(),
                    'amount' => 1500.00,
                    'management_fee' => 150.00,
                    'status' => 'completed',
                    'created_at' => clone $now,
                    'updated_at' => clone $now,
                ]);
            }

            // If a replacement is requested, create a replacement record
            if ($status === 'replacement_pending') {
                DB::table('replacements')->insert([
                    'management_plan_id' => $planId,
                    'old_provider_id' => $faker->randomElement($providers),
                    'status' => 'requested',
                    'reason' => 'Provider relocated to another city',
                    'created_at' => clone $now,
                    'updated_at' => clone $now,
                ]);
            }
        }
    }
}