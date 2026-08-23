<?php

namespace App\Enums;

enum LeadStatus: string
{
    case CREATED = 'created';
    case QUALIFIED = 'qualified';
    case MATCHING = 'matching';
    case DISTRIBUTED = 'distributed';
    case RESPONDING = 'responding';
    case INTERESTED = 'interested';
    case SELECTED = 'selected';
    case CONVERTED = 'converted';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case UNFULFILLED = 'unfulfilled';
}