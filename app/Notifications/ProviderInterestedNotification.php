<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProviderInterestedNotification extends Notification
{
    use Queueable;

    public function __construct(public Lead $lead, public User $provider) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $serviceName = $this->lead->serviceRequest->service->name ?? 'a service';
        $url = url('/service-requests/' . $this->lead->serviceRequest->id);

        return (new MailMessage)
            ->subject('A Professional is interested in your job!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("{$this->provider->name} is ready to take your request for {$serviceName}.")
            ->action('View Details & Book', $url)
            ->line('Please log in to your dashboard to review and confirm the booking.');
    }

    public function toArray(object $notifiable): array
    {
        $serviceName = $this->lead->serviceRequest->service->name ?? 'Service';
        
        return [
            'title' => 'New Professional Interest',
            'message' => "{$this->provider->name} is interested in your {$serviceName} request.",
            'link' => '/service-requests/' . $this->lead->serviceRequest->id,
            'lead_id' => $this->lead->id,
        ];
    }
}