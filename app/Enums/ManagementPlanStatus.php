<?php

namespace App\Enums;

enum ManagementPlanStatus: string {
    case DRAFT = 'draft';
    case REQUESTED = 'requested';
    case MATCHING = 'matching';
    case CANDIDATE_SELECTED = 'candidate_selected';
    case VERIFICATION_PENDING = 'verification_pending';
    case READY = 'ready';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case REPLACEMENT_PENDING = 'replacement_pending';
    case RENEWAL_DUE = 'renewal_due';
    case RENEWED = 'renewed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}