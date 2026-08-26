<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ServiceRequest;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\UserRole;

class ServiceRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        // Customers can view their own list
        if ($authUser->role === UserRole::Customer) {
            return true;
        }
        return $authUser->can('ViewAny:ServiceRequest');
    }

    public function view(AuthUser $authUser, ServiceRequest $serviceRequest): bool
    {
        // Ownership check for customers
        if ($authUser->role === UserRole::Customer) {
            return $authUser->id === $serviceRequest->customer_id;
        }
        return $authUser->can('View:ServiceRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        // Only customers create service requests via API
        if ($authUser->role === UserRole::Customer) {
            return true;
        }
        return $authUser->can('Create:ServiceRequest');
    }

    public function update(AuthUser $authUser, ServiceRequest $serviceRequest): bool
    {
        // Customer can update only if it's draft or submitted (before processing)
        if ($authUser->role === UserRole::Customer) {
            return $authUser->id === $serviceRequest->customer_id 
                && in_array($serviceRequest->status, ['draft', 'submitted']);
        }
        return $authUser->can('Update:ServiceRequest');
    }

    public function delete(AuthUser $authUser, ServiceRequest $serviceRequest): bool
    {
        // Prevent deletion if already processed, even for owners
        if ($authUser->role === UserRole::Customer) {
            return $authUser->id === $serviceRequest->customer_id 
                && in_array($serviceRequest->status, ['draft', 'submitted']);
        }
        return $authUser->can('Delete:ServiceRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ServiceRequest');
    }

    public function restore(AuthUser $authUser, ServiceRequest $serviceRequest): bool
    {
        return $authUser->can('Restore:ServiceRequest');
    }

    public function forceDelete(AuthUser $authUser, ServiceRequest $serviceRequest): bool
    {
        return $authUser->can('ForceDelete:ServiceRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ServiceRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ServiceRequest');
    }

    public function replicate(AuthUser $authUser, ServiceRequest $serviceRequest): bool
    {
        return $authUser->can('Replicate:ServiceRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ServiceRequest');
    }
}