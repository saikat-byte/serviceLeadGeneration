# Local Service Lead Generation Platform

## Stack
Laravel 13
PHP 8.3+
MySQL
Filament
Blade/Livewire
REST API

## Business Lifecycle

Request
→ Lead
→ Match
→ Interest
→ Connection
→ Booking
→ Job
→ Payment
→ Commission
→ Settlement
→ Review
→ Complaint

## Completed

Batch 1 — Service Request & Lead
Batch 2 — Match / Interest / Connection
Batch 3 — Booking / Job
Batch 4 — Payment / Transaction / Commission
Batch 5 — Settlement / Payout
Batch 6 — Review / Rating
Batch 7 — Complaint / Dispute

## Current Phase

Batch 8 — Admin Operations Dashboard

## Architecture Rule

Business logic belongs in Services.

Controllers are thin.

Filament is an interface layer.

API is versioned under /api/v1/.

Do not duplicate business logic.

## Current Admin Status

Filament Admin Panel is working.

Roles section is visible.

Next task:
Audit and build Admin Operations Dashboard.