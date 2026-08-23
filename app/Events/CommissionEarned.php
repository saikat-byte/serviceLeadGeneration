<?php

namespace App\Events;

use App\Models\Commission;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommissionEarned
{
    use Dispatchable, SerializesModels;
    public function __construct(public Commission $commission) {}
}