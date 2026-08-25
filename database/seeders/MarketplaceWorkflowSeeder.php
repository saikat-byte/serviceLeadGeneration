<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Ensure Schema is imported
use Carbon\Carbon;
use Faker\Factory as Faker;

class MarketplaceWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $customers = DB::table('users')->where('role', 'customer')->pluck('id')->toArray();
        $providers = DB::table('users')->where('role', 'provider')->pluck('id')->toArray();
        $services = DB::table('services')->pluck('id')->toArray();
        $locations = DB::table('locations')->pluck('id')->toArray();

        if (empty($customers) || empty($providers) || empty($services)) return;

        // Dynamic schema checks
        $hasCommissionId = Schema::hasColumn('settlements', 'commission_id');

        // Generate 100 Scenarios
        for ($i = 0; $i < 100; $i++) {
            $now = Carbon::now()->subDays(rand(1, 30));
            
            $customerId = $faker->randomElement($customers);
            $providerId = $faker->randomElement($providers);
            $serviceId = $faker->randomElement($services);
            $locId = $faker->randomElement($locations);

            // Determine Scenario Type 
            // 50% Success (A), 10% Cancelled (D), 10% Pending Match (B), 10% Pending Payment (G) etc.
            $scenario = $faker->randomElement(['A','A','A','A','A','B','C','D','F','G','H']); 

            // 1. Service Request
            $reqStatus = in_array($scenario, ['B']) ? 'submitted' : 'qualified';
            $reqId = DB::table('service_requests')->insertGetId([
                'customer_id' => $customerId,
                'service_id' => $serviceId,
                'location_id' => $locId,
                'status' => $reqStatus,
                'urgency' => 'normal',
                'budget_min' => 200,
                'budget_max' => 1000,
                'description' => $faker->sentence(),
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);
            $this->logTransition('App\Models\ServiceRequest', $reqId, 'draft', $reqStatus, 'ServiceRequestSubmitted', $customerId, $now);

            // If only submitted, stop here (Scenario B partial)
            if ($scenario === 'B' && rand(0,1)) continue;

            // 2. Lead
            $leadStatus = $scenario === 'B' ? 'matching' : ($scenario === 'C' ? 'interested' : 'converted');
            $leadId = DB::table('leads')->insertGetId([
                'service_request_id' => $reqId,
                'status' => $leadStatus,
                'quality_score' => rand(50, 100),
                'distributed_at' => clone $now,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);
            $this->logTransition('App\Models\Lead', $leadId, 'created', $leadStatus, 'LeadGenerated', null, $now);

            if ($scenario === 'B') continue; // Stop at matching

            // 3. Match & Interest
            $matchStatus = ($scenario === 'A' || $scenario === 'D') ? 'selected' : 'offered';
            $matchId = DB::table('matches')->insertGetId([
                'lead_id' => $leadId,
                'provider_id' => $providerId,
                'status' => $matchStatus,
                'match_score' => rand(80, 100),
                'rank' => 1,
                'offered_at' => clone $now,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            $interestId = DB::table('interests')->insertGetId([
                'lead_id' => $leadId,
                'provider_id' => $providerId,
                'actor_type' => 'provider',
                'status' => ($matchStatus === 'selected') ? 'selected' : 'active',
                'expressed_at' => clone $now,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            if ($scenario === 'C') continue; // Stop at multiple interests

            // 4. Connection & Booking
            $connId = DB::table('connections')->insertGetId([
                'customer_id' => $customerId,
                'provider_id' => $providerId,
                'lead_id' => $leadId,
                'status' => 'active',
                'unlocked_at' => clone $now,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            $bookingStatus = $scenario === 'D' ? 'cancelled' : 'confirmed'; 
            $amount = rand(300, 2000);
            $bookingId = DB::table('bookings')->insertGetId([
                'customer_id' => $customerId,
                'provider_id' => $providerId,
                'service_id' => $serviceId,
                'service_request_id' => $reqId,
                'lead_id' => $leadId,
                'connection_id' => $connId,
                'status' => $bookingStatus,
                'scheduled_at' => (clone $now)->addDays(1),
                'estimated_amount' => $amount,
                'final_amount' => $amount,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            if ($scenario === 'D') {
                DB::table('booking_cancellations')->insert([
                    'booking_id' => $bookingId,
                    'cancelled_by' => $customerId,
                    'reason' => 'Change of mind',
                    'fee' => 0,
                    'created_at' => clone $now
                ]);
                $this->logTransition('App\Models\Booking', $bookingId, 'confirmed', 'cancelled', 'BookingCancelled', $customerId, $now);
                continue; // Stop at Cancelled
            }

            // 5. Job Fulfillment
            $jobStatus = 'completed';
            $jobId = DB::table('service_jobs')->insertGetId([
                'booking_id' => $bookingId,
                'status' => $jobStatus,
                'started_at' => clone $now,
                'completed_at' => (clone $now)->addHours(2),
                'final_service_value' => $amount,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);
            
            DB::table('bookings')->where('id', $bookingId)->update(['status' => 'work_completed']);

            // 6. Finances
            $paymentStatus = in_array($scenario, ['G']) ? 'pending' : ($scenario === 'H' ? 'failed' : 'paid');
            $paymentId = DB::table('payments')->insertGetId([
                'customer_id' => $customerId,
                'booking_id' => $bookingId,
                'amount' => $amount,
                'payment_method' => 'card',
                'gateway' => 'stripe',
                'status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'paid' ? clone $now : null,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            if ($paymentStatus === 'paid') {
                DB::table('bookings')->where('id', $bookingId)->update(['status' => 'paid']);

                $commissionAmount = $amount * 0.10; // 10%
                $commId = DB::table('commissions')->insertGetId([
                    'booking_id' => $bookingId,
                    'provider_id' => $providerId,
                    'model' => 'commission',
                    'base_amount' => $amount,
                    'rate' => 10.00,
                    'amount' => $commissionAmount,
                    'status' => 'earned',
                    'earned_at' => clone $now,
                    'created_at' => clone $now,
                ]);

                // Safe Settlement Insert
                $settlementData = [
                    'provider_id' => $providerId,
                    'gross_amount' => $amount,
                    'platform_fee' => $commissionAmount,
                    'net_amount' => $amount - $commissionAmount,
                    'status' => 'settled',
                    'settled_at' => clone $now,
                    'created_at' => clone $now,
                ];

                if ($hasCommissionId) {
                    $settlementData['commission_id'] = $commId;
                }

                DB::table('settlements')->insert($settlementData);

                // 7. Reviews & Complaints
                if (rand(0, 1)) {
                    DB::table('reviews')->insert([
                        'booking_id' => $bookingId,
                        'reviewer_id' => $customerId,
                        'reviewee_id' => $providerId,
                        'rating' => rand(3, 5),
                        'comment' => 'Great service!',
                        'status' => 'published',
                        'created_at' => clone $now,
                    ]);
                }

                if ($scenario === 'F') { // Complaint Resolved
                    DB::table('complaints')->insert([
                        'booking_id' => $bookingId,
                        'complainant_id' => $customerId,
                        'against_user_id' => $providerId,
                        'category' => 'pricing_issue',
                        'description' => 'Overcharged a bit.',
                        'status' => 'resolved',
                        'resolved_at' => clone $now,
                        'created_at' => clone $now,
                    ]);
                }
            }
        }
    }

    private function logTransition($type, $id, $from, $to, $event, $actorId, $time)
    {
        DB::table('state_transitions')->insert([
            'entity_type' => $type,
            'entity_id' => $id,
            'from_state' => $from,
            'to_state' => $to,
            'event' => $event,
            'actor_id' => $actorId,
            'reason' => 'Seeded automatically',
            'created_at' => clone $time
        ]);

        DB::table('business_events')->insert([
            'event_name' => $event,
            'entity_type' => $type,
            'entity_id' => $id,
            'actor_id' => $actorId,
            'from_state' => $from,
            'to_state' => $to,
            'occurred_at' => clone $time
        ]);
    }
}