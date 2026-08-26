<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking, public string $recipientType) {}

    public function via(object $notifiable): array
    {
        // Ekhane 'mail' add kora holo jate database er pashapashi email o jay
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $role = $this->recipientType === 'customer' ? 'provider' : 'customer';
        
        // Frontend URL path (eta pore apnar frontend route onujayi change korte paren)
        $url = url('/bookings/' . $this->booking->id); 
        
        return (new MailMessage)
            ->subject('Booking Confirmed - #' . $this->booking->id)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your booking has been successfully confirmed with the {$role}.")
            ->line("Booking ID: #" . $this->booking->id)
            ->action('View Booking Details', $url)
            ->line('Thank you for using our platform!');
    }

    public function toArray(object $notifiable): array
    {
        $role = $this->recipientType === 'customer' ? 'provider' : 'customer';
        
        return [
            'title' => 'Booking Confirmed',
            'message' => "Your booking has been successfully confirmed with the {$role}.",
            'booking_id' => $this->booking->id,
        ];
    }
}