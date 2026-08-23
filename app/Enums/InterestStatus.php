<?php

namespace App\Enums;

enum InterestStatus: string {
    case EXPRESSED = 'expressed';
    case ACTIVE = 'active';
    case WITHDRAWN = 'withdrawn';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case SELECTED = 'selected';
}