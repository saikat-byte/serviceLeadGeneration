<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Verification;
use Illuminate\Auth\Access\HandlesAuthorization;

class VerificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        if (in_array($authUser->role?->value, ['customer', 'provider'])) {
            return true;
        }
        return $authUser->can('ViewAny:Verification');
    }

    public function view(AuthUser $authUser, Verification $verification): bool
    {
        if (in_array($authUser->role?->value, ['customer', 'provider'])) {
            return $authUser->id === $verification->user_id;
        }
        return $authUser->can('View:Verification');
    }

    public function create(AuthUser $authUser): bool
    {
        if (in_array($authUser->role?->value, ['customer', 'provider'])) {
            return true;
        }
        return $authUser->can('Create:Verification');
    }

    public function update(AuthUser $authUser, Verification $verification): bool
    {
        return $authUser->can('Update:Verification');
    }

    public function delete(AuthUser $authUser, Verification $verification): bool
    {
        return $authUser->can('Delete:Verification');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Verification');
    }

    public function restore(AuthUser $authUser, Verification $verification): bool
    {
        return $authUser->can('Restore:Verification');
    }

    public function forceDelete(AuthUser $authUser, Verification $verification): bool
    {
        return $authUser->can('ForceDelete:Verification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Verification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Verification');
    }

    public function replicate(AuthUser $authUser, Verification $verification): bool
    {
        return $authUser->can('Replicate:Verification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Verification');
    }
}