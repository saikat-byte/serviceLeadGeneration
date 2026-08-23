<?php

namespace App\Enums;

enum ServiceJobStatus: string {
    case CREATED = 'created';
    case SCHEDULED = 'scheduled';
    case ARRIVED = 'arrived';
    case STARTED = 'started';
    case COMPLETED = 'completed';
    case VERIFIED = 'verified';
    case CLOSED = 'closed';
    case DISPUTED = 'disputed';
    case CANCELLED = 'cancelled';
}