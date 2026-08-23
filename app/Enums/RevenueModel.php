<?php

namespace App\Enums;

enum RevenueModel: string {
    case LEAD_FEE = 'lead_fee';
    case COMMISSION = 'commission';
    case MANAGEMENT_FEE = 'management_fee';
    case PLACEMENT_FEE = 'placement_fee';
}