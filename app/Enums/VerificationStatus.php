<?php

namespace App\Enums;

enum VerificationStatus: string {
    case NOT_REQUIRED = 'not_required';
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case SUSPENDED = 'suspended';
}