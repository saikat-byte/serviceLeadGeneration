<?php

namespace App\Listeners;

use App\Events\LeadDistributed;
use App\Models\MatchRecord;
use App\Models\User;
use App\Notifications\LeadDistributedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLeadNotification implements ShouldQueue
{
    public function handle(LeadDistributed $event): void
    {
        // Find all providers matched with this lead
        $matches = MatchRecord::where('lead_id', $event->lead->id)->get();

        foreach ($matches as $match) {
            $provider = User::find($match->provider_id);
            if ($provider) {
                $provider->notify(new LeadDistributedNotification($event->lead));
            }
        }
    }
}