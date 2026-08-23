<?php

namespace App\Enums;

enum ReviewStatus: string {
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case PUBLISHED = 'published';
    case FLAGGED = 'flagged';
    case REMOVED = 'removed';
}