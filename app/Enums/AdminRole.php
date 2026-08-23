<?php

namespace App\Enums;

enum AdminRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case OPERATIONS_ADMIN = 'operations_admin';
    case FINANCE_ADMIN = 'finance_admin';
    case TRUST_SAFETY_ADMIN = 'trust_safety_admin';
    case SUPPORT_AGENT = 'support_agent';
}