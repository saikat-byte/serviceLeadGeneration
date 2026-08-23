<?php

namespace App\Events;

use App\Models\Connection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConnectionEstablished
{
    use Dispatchable, SerializesModels;

    public function __construct(public Connection $connection) {}
}