<?php

namespace App\Enums;

enum SkillVerificationStatus: string {
    case UNVERIFIED = 'unverified';
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
}