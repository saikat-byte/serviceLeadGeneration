<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingNotification implements ShouldQueue
{
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;

        // Notify Customer
        $customer = User::find($booking->customer_id);
        if ($customer) {
            $customer->notify(new BookingConfirmedNotification($booking, 'customer'));
        }

        // Notify Provider
        $provider = User::find($booking->provider_id);
        if ($provider) {
            $provider->notify(new BookingConfirmedNotification($booking, 'provider'));
        }
    }
}