<?php

namespace App\Enums;

enum UserStatus: string {
    case REGISTERED = 'registered';
    case PROFILE_INCOMPLETE = 'profile_incomplete';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case BLOCKED = 'blocked';
    case DEACTIVATED = 'deactivated';
}