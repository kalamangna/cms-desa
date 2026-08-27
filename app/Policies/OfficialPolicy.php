<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Official;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficialPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Official');
    }

    public function view(AuthUser $authUser, Official $official): bool
    {
        return $authUser->can('View:Official');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Official');
    }

    public function update(AuthUser $authUser, Official $official): bool
    {
        return $authUser->can('Update:Official');
    }

    public function delete(AuthUser $authUser, Official $official): bool
    {
        return $authUser->can('Delete:Official');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Official');
    }

    public function restore(AuthUser $authUser, Official $official): bool
    {
        return $authUser->can('Restore:Official');
    }

    public function forceDelete(AuthUser $authUser, Official $official): bool
    {
        return $authUser->can('ForceDelete:Official');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Official');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Official');
    }

    public function replicate(AuthUser $authUser, Official $official): bool
    {
        return $authUser->can('Replicate:Official');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Official');
    }

}