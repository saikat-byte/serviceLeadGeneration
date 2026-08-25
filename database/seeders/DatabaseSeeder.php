<?php

namespace Database\Seeders;

use Database\Seeders\SystemAndCatalogSeeder;
use Database\Seeders\ProviderAndCustomerSeeder;
use Database\Seeders\MarketplaceWorkflowSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; // Disables model events to avoid side effects during mass seeding

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Truncate tables for a clean slate if needed (optional, migrate:fresh handles this)
        
        $this->call([
            SystemAndCatalogSeeder::class,
            ProviderAndCustomerSeeder::class,
            MarketplaceWorkflowSeeder::class,
           RecurringManagementSeeder::class,
        ]);
    }
}