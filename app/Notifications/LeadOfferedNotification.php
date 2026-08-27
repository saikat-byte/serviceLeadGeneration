<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\Match as MatchRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue; // Use ShouldQueue if database queue is ever configured

class LeadOfferedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead, public MatchRecord $match) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $serviceName = $this->lead->serviceRequest->service->name ?? 'Service';
        $locality = $this->lead->serviceRequest->location->locality ?? 'your service area';
        $budgetMin = $this->lead->serviceRequest->budget_min ?? 'N/A';
        $budgetMax = $this->lead->serviceRequest->budget_max ?? 'N/A';
        $urgency = ucfirst($this->lead->serviceRequest->urgency ?? 'Normal');
        $expires = $this->lead->expires_at ? $this->lead->expires_at->format('M d, Y h:i A') : 'soon';

        return (new MailMessage)
            ->subject("New Lead: {$serviceName} in {$locality}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new customer request for **{$serviceName}** is available near **{$locality}**.")
            ->line("Budget: ₹{$budgetMin} - ₹{$budgetMax}")
            ->line("Urgency: {$urgency}")
            ->action('View Request Details', url("/provider/leads/{$this->lead->id}"))
            ->line("Please respond before this lead expires at {$expires}.")
            ->line("Thank you for using our platform!");
    }

    public function toDatabase($notifiable)
    {
        $serviceName = $this->lead->serviceRequest->service->name ?? 'Service';
        $urgency = $this->lead->serviceRequest->urgency ?? 'normal';
        
        return [
            'lead_id' => $this->lead->id,
            'match_id' => $this->match->id,
            'message' => "New lead available: {$serviceName} (" . ucfirst($urgency) . ")",
            'link' => "/provider/leads/{$this->lead->id}",
            'type' => 'lead_offered'
        ];
    }
}