<?php

namespace App\Events;

use App\Models\Review;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewRemoved
{
    use Dispatchable, SerializesModels;

    public function __construct(public Review $review) {}
}