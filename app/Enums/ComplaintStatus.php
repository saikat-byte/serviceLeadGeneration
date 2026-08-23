<?php

namespace App\Enums;

enum ComplaintStatus: string {
    case CREATED = 'created';
    case ACKNOWLEDGED = 'acknowledged';
    case UNDER_REVIEW = 'under_review';
    case ACTION_REQUIRED = 'action_required';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case REJECTED = 'rejected';
    case ESCALATED = 'escalated';
}