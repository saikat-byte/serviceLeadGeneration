<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Booking;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\UserRole;

class BookingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        // Customers and Providers can view their own booking lists
        if (in_array($authUser->role, [UserRole::Customer, UserRole::Provider])) {
            return true;
        }
        return $authUser->can('ViewAny:Booking');
    }

    public function view(AuthUser $authUser, Booking $booking): bool
    {
        // Ownership check
        if ($authUser->role === UserRole::Customer) {
            return $authUser->id === $booking->customer_id;
        }
        if ($authUser->role === UserRole::Provider) {
            return $authUser->id === $booking->provider_id;
        }
        return $authUser->can('View:Booking');
    }

    public function create(AuthUser $authUser): bool
    {
        // Only customers can initiate bookings
        if ($authUser->role === UserRole::Customer) {
            return true;
        }
        return $authUser->can('Create:Booking');
    }

    public function update(AuthUser $authUser, Booking $booking): bool
    {
        // Users should NOT update booking fields manually. 
        // State transitions handle this via services.
        return $authUser->can('Update:Booking');
    }

    public function delete(AuthUser $authUser, Booking $booking): bool
    {
        // Users cannot hard delete bookings. They must cancel them.
        return $authUser->can('Delete:Booking');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Booking');
    }

    public function restore(AuthUser $authUser, Booking $booking): bool
    {
        return $authUser->can('Restore:Booking');
    }

    public function forceDelete(AuthUser $authUser, Booking $booking): bool
    {
        return $authUser->can('ForceDelete:Booking');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Booking');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Booking');
    }

    public function replicate(AuthUser $authUser, Booking $booking): bool
    {
        return $authUser->can('Replicate:Booking');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Booking');
    }
}