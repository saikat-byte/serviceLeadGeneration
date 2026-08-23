<?php

namespace App\Enums;

enum ProviderAvailabilityStatus: string {
    case AVAILABLE = 'available';
    case BUSY = 'busy';
    case UNAVAILABLE = 'unavailable';
    case OFFLINE = 'offline';
}