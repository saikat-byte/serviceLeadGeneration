<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case VALIDATING = 'validating';
    case QUALIFIED = 'qualified';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}