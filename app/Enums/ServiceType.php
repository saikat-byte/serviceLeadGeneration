<?php

namespace App\Enums;

enum ServiceType: string {
    case ON_DEMAND = 'on_demand';
    case SCHEDULED = 'scheduled';
    case RECURRING = 'recurring';
    case PROJECT = 'project';
}