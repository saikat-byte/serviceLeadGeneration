<?php

namespace App\Enums;

enum CommissionStatus: string {
    case PENDING = 'pending';
    case CALCULATED = 'calculated';
    case EARNED = 'earned';
    case ADJUSTED = 'adjusted';
    case REVERSED = 'reversed';
    case SETTLED = 'settled';
}