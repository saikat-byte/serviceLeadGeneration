<?php

namespace App\Events;

use App\Models\ServiceJob;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobStarted
{
    use Dispatchable, SerializesModels;
    public function __construct(public ServiceJob $job) {}
}