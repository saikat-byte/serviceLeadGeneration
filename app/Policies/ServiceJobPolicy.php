<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ServiceJob;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceJobPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ServiceJob');
    }

    public function view(AuthUser $authUser, ServiceJob $serviceJob): bool
    {
        return $authUser->can('View:ServiceJob');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ServiceJob');
    }

    public function update(AuthUser $authUser, ServiceJob $serviceJob): bool
    {
        return $authUser->can('Update:ServiceJob');
    }

    public function delete(AuthUser $authUser, ServiceJob $serviceJob): bool
    {
        return $authUser->can('Delete:ServiceJob');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ServiceJob');
    }

    public function restore(AuthUser $authUser, ServiceJob $serviceJob): bool
    {
        return $authUser->can('Restore:ServiceJob');
    }

    public function forceDelete(AuthUser $authUser, ServiceJob $serviceJob): bool
    {
        return $authUser->can('ForceDelete:ServiceJob');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ServiceJob');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ServiceJob');
    }

    public function replicate(AuthUser $authUser, ServiceJob $serviceJob): bool
    {
        return $authUser->can('Replicate:ServiceJob');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ServiceJob');
    }

}