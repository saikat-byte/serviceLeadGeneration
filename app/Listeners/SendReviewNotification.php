<?php

namespace App\Listeners;

use App\Events\ReviewPublished;
use App\Notifications\ReviewReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReviewNotification implements ShouldQueue
{
    public function handle(ReviewPublished $event): void
    {
        $reviewee = $event->review->reviewee;
        
        if ($reviewee) {
            $reviewee->notify(new ReviewReceivedNotification($event->review));
        }
    }
}