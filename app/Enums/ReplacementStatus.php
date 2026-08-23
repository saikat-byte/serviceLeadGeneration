<?php

namespace App\Enums;

enum ReplacementStatus: string {
    case REQUESTED = 'requested';
    case MATCHING = 'matching';
    case CANDIDATE_SELECTED = 'candidate_selected';
    case VERIFICATION_PENDING = 'verification_pending';
    case APPROVED = 'approved';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}