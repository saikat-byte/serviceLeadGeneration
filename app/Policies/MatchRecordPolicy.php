<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MatchRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatchRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MatchRecord');
    }

    public function view(AuthUser $authUser, MatchRecord $matchRecord): bool
    {
        return $authUser->can('View:MatchRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MatchRecord');
    }

    public function update(AuthUser $authUser, MatchRecord $matchRecord): bool
    {
        return $authUser->can('Update:MatchRecord');
    }

    public function delete(AuthUser $authUser, MatchRecord $matchRecord): bool
    {
        return $authUser->can('Delete:MatchRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MatchRecord');
    }

    public function restore(AuthUser $authUser, MatchRecord $matchRecord): bool
    {
        return $authUser->can('Restore:MatchRecord');
    }

    public function forceDelete(AuthUser $authUser, MatchRecord $matchRecord): bool
    {
        return $authUser->can('ForceDelete:MatchRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MatchRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MatchRecord');
    }

    public function replicate(AuthUser $authUser, MatchRecord $matchRecord): bool
    {
        return $authUser->can('Replicate:MatchRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MatchRecord');
    }

}