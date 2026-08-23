<?php

namespace App\Enums;

enum ConnectionStatus: string {
    case PENDING = 'pending';
    case UNLOCKED = 'unlocked';
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    case BLOCKED = 'blocked';
}