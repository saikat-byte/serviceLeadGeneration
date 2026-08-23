<?php

namespace App\Enums;

enum ProviderServiceStatus: string {
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case SUSPENDED = 'suspended';
}