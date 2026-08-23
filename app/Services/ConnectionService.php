<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Models\Connection;
use App\Enums\ConnectionStatus;
use App\Events\ConnectionEstablished;
use Illuminate\Support\Facades\DB;

class ConnectionService
{
    /**
     * Establish a connection between Customer and Provider after selection.
     */
    public function establish(Lead $lead, User $customer, User $provider): Connection
    {
        return DB::transaction(function () use ($lead, $customer, $provider) {
            
            // 1. Create Connection Record
            $connection = Connection::create([
                'customer_id' => $customer->id,
                'provider_id' => $provider->id,
                'lead_id'     => $lead->id,
                'status'      => ConnectionStatus::PENDING,
            ]);

            // 2. Transition to UNLOCKED (meaning contact info / chat is now available)
            $connection->transitionState(
                newState: ConnectionStatus::UNLOCKED,
                eventName: 'ConnectionUnlocked',
                reason: 'Customer selected the provider, unlocking communication.'
            );
            
            $connection->update(['unlocked_at' => now()]);

            // 3. Dispatch Event
            ConnectionEstablished::dispatch($connection);

            return $connection;
        });
    }
}