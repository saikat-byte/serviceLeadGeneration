<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Location;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ProviderServiceArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Customer
        $customer = User::create([
            'name' => 'John Customer',
            'mobile' => '01711000001',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);
        
        echo "Customer Token: " . $customer->createToken('test-token')->plainTextToken . "\n";

        // 2. Create a Provider
        $provider = User::create([
            'name' => 'Mike Provider',
            'mobile' => '01711000002',
            'email' => 'provider@example.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'status' => 'active',
        ]);

        echo "Provider Token: " . $provider->createToken('test-token')->plainTextToken . "\n";

        // 3. Create Provider Profile & Capabilities (Crucial for Matching Engine)
        ProviderProfile::create([
            'user_id' => $provider->id,
            'availability_status' => 'available',
            'rating_average' => 4.9,
            'response_rate' => 98.00,
            'experience_years' => 5,
        ]);

        // 4. Create Customer Location
        Location::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'city' => 'Kolkata',
            'is_default' => true,
        ]);

        // 5. Create Service Catalog
        $category = ServiceCategory::create([
            'name' => 'Plumbing',
            'slug' => 'plumbing',
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Tap Repair',
            'slug' => 'tap-repair',
        ]);

        // 6. Link Provider to Service and Area
        ProviderService::create([
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'status' => 'approved',
            'starting_price' => 150.00,
        ]);

        ProviderServiceArea::create([
            'provider_id' => $provider->id,
            'city' => 'Kolkata',
            'radius_km' => 15,
        ]);
    }
}