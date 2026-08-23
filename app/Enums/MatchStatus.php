<?php

namespace App\Enums;

enum MatchStatus: string {
    case CREATED = 'created';
    case RANKED = 'ranked';
    case OFFERED = 'offered';
    case RESPONDED = 'responded';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case SELECTED = 'selected';
    case NOT_SELECTED = 'not_selected';
}