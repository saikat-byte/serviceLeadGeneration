<?php

namespace App\Enums;

enum ComplaintType: string
{
    case SERVICE = 'service';
    case PROVIDER_NO_SHOW = 'provider_no_show';
    case POOR_SERVICE = 'poor_service';
    case LATE_ARRIVAL = 'late_arrival';
    case MISBEHAVIOUR = 'misbehaviour';
    case CUSTOMER_MISBEHAVIOUR = 'customer_misbehaviour';
    case CUSTOMER_NO_SHOW = 'customer_no_show';
    case PAYMENT_ISSUE = 'payment_issue';
    case REFUND_REQUEST = 'refund_request';
    case FRAUD_SUSPECTED = 'fraud_suspected';
    case FAKE_REVIEW = 'fake_review';
    case OTHER = 'other';
}