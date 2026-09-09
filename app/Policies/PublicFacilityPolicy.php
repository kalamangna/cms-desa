<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PublicFacility;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicFacilityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PublicFacility');
    }

    public function view(AuthUser $authUser, PublicFacility $publicFacility): bool
    {
        return $authUser->can('View:PublicFacility');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PublicFacility');
    }

    public function update(AuthUser $authUser, PublicFacility $publicFacility): bool
    {
        return $authUser->can('Update:PublicFacility');
    }

    public function delete(AuthUser $authUser, PublicFacility $publicFacility): bool
    {
        return $authUser->can('Delete:PublicFacility');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PublicFacility');
    }

    public function restore(AuthUser $authUser, PublicFacility $publicFacility): bool
    {
        return $authUser->can('Restore:PublicFacility');
    }

    public function forceDelete(AuthUser $authUser, PublicFacility $publicFacility): bool
    {
        return $authUser->can('ForceDelete:PublicFacility');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PublicFacility');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PublicFacility');
    }

    public function replicate(AuthUser $authUser, PublicFacility $publicFacility): bool
    {
        return $authUser->can('Replicate:PublicFacility');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PublicFacility');
    }

}