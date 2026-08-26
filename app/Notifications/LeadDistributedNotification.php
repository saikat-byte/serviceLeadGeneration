<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeadDistributedNotification extends Notification
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Lead Matched',
            'message' => "A new service request matches your skills. Check it out now!",
            'lead_id' => $this->lead->id,
            'service_request_id' => $this->lead->service_request_id,
        ];
    }
}