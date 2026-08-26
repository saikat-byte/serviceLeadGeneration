<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Review Received',
            'message' => "You have received a {$this->review->rating}-star review for a recent booking.",
            'booking_id' => $this->review->booking_id,
            'rating' => $this->review->rating,
        ];
    }
}