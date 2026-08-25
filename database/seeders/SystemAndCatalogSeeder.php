<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class SystemAndCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $now = Carbon::now();

        // 1. Roles & Permissions (Insert or Ignore safely)
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'name' => 'super_admin', 'guard_name' => 'web', 'created_at' => $now],
            ['id' => 2, 'name' => 'Manager', 'guard_name' => 'web', 'created_at' => $now],
        ]);

        // 2. Default Admin - Re-run Safe Check
        $adminUser = DB::table('users')->where('email', 'admin@example.com')->first();
        
        if ($adminUser) {
            $adminId = $adminUser->id;
        } else {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin', // 🟢 FIX: Changed from 'super_admin' to 'admin'
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('model_has_roles')->insertOrIgnore([
            'role_id' => 1,
            'model_type' => 'App\Models\User',
            'model_id' => $adminId
        ]);

        // 3. Service Categories - Re-run Safe Check
        $categories = ['Plumbing', 'Electrical', 'Cleaning', 'Mechanic', 'Carpentry', 'Pest Control', 'Painting', 'Salon & Makeup'];
        $categoryIds = [];
        
        foreach ($categories as $index => $cat) {
            $slug = strtolower(str_replace([' & ', ' '], '-', $cat));
            $existingCat = DB::table('service_categories')->where('slug', $slug)->first();
            
            if ($existingCat) {
                $categoryIds[] = $existingCat->id;
            } else {
                $categoryIds[] = DB::table('service_categories')->insertGetId([
                    'name' => $cat,
                    'slug' => $slug,
                    'description' => "Professional $cat services",
                    'is_active' => 1,
                    'sort_order' => $index,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. Services & Definitions
        $serviceIds = [];
        foreach ($categoryIds as $catId) {
            // Check how many services this category already has
            $existingServicesCount = DB::table('services')->where('category_id', $catId)->count();
            
            // Only insert if it has fewer than 2 services to avoid infinite loops on re-run
            if ($existingServicesCount < 2) {
                for ($i = 1; $i <= 2; $i++) {
                    $serviceName = $faker->words(2, true) . ' Service';
                    $serviceId = DB::table('services')->insertGetId([
                        'category_id' => $catId,
                        'name' => ucwords($serviceName),
                        'slug' => strtolower(str_replace(' ', '-', $serviceName)) . '-' . uniqid(),
                        'description' => $faker->paragraph(),
                        'is_active' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $serviceIds[] = $serviceId;

                    // Service Definition
                    DB::table('service_definitions')->insert([
                        'service_id' => $serviceId,
                        'service_type' => $faker->randomElement(['on_demand', 'scheduled']),
                        'pricing_type' => $faker->randomElement(['fixed', 'inspection', 'quotation']),
                        'revenue_model' => 'commission',
                        'commission_rate' => $faker->randomElement([5.00, 10.00, 15.00]),
                        'currency' => 'INR',
                        'requires_customer_confirmation' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // Service Variants
                    DB::table('service_variants')->insert([
                        ['service_id' => $serviceId, 'name' => 'Basic', 'slug' => 'basic-'.$serviceId.'-'.uniqid(), 'is_active' => 1, 'created_at' => $now],
                        ['service_id' => $serviceId, 'name' => 'Premium', 'slug' => 'premium-'.$serviceId.'-'.uniqid(), 'is_active' => 1, 'created_at' => $now],
                    ]);
                }
            }
        }

        // 5. Skills - Re-run Safe Check
        $skills = ['Pipe Fitting', 'Wiring', 'Deep Cleaning', 'Engine Repair', 'Wood Polishing', 'Bridal Makeup'];
        foreach ($skills as $skill) {
            $slug = strtolower(str_replace(' ', '-', $skill));
            if (!DB::table('skills')->where('slug', $slug)->exists()) {
                DB::table('skills')->insert([
                    'name' => $skill,
                    'slug' => $slug,
                    'is_active' => 1,
                    'created_at' => $now,
                ]);
            }
        }
    }
}