<?php

namespace App\Enums;

enum BookingStatus: string {
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROVIDER_ASSIGNED = 'provider_assigned';
    case SCHEDULED = 'scheduled';
    case ON_THE_WAY = 'on_the_way';
    case ARRIVED = 'arrived';
    case WORK_STARTED = 'work_started';
    case WORK_COMPLETED = 'work_completed';
    case PAYMENT_PENDING = 'payment_pending';
    case PAID = 'paid';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
    case DISPUTED = 'disputed';
    case FAILED = 'failed';
}