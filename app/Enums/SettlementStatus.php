<?php

namespace App\Enums;

enum SettlementStatus: string {
    case PENDING = 'pending';
    case ELIGIBLE = 'eligible';
    case PROCESSING = 'processing';
    case SETTLED = 'settled';
    case FAILED = 'failed';
    case REVERSED = 'reversed';
}