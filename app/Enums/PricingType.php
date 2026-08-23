<?php

namespace App\Enums;

enum PricingType: string {
    case FIXED = 'fixed';
    case NEGOTIABLE = 'negotiable';
    case INSPECTION = 'inspection';
    case QUOTATION = 'quotation';
    case MONTHLY = 'monthly';
}