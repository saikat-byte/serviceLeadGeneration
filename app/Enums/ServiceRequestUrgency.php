<?php

namespace App\Enums;

enum ServiceRequestUrgency: string
{
    case NORMAL = 'normal';
    case URGENT = 'urgent';
    case EMERGENCY = 'emergency';
}