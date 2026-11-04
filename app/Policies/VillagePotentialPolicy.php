<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\VillagePotential;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VillagePotentialPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VillagePotential');
    }

    public function view(AuthUser $authUser, VillagePotential $villagePotential): bool
    {
        return $authUser->can('View:VillagePotential');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VillagePotential');
    }

    public function update(AuthUser $authUser, VillagePotential $villagePotential): bool
    {
        return $authUser->can('Update:VillagePotential');
    }

    public function delete(AuthUser $authUser, VillagePotential $villagePotential): bool
    {
        return $authUser->can('Delete:VillagePotential');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VillagePotential');
    }

    public function restore(AuthUser $authUser, VillagePotential $villagePotential): bool
    {
        return $authUser->can('Restore:VillagePotential');
    }

    public function forceDelete(AuthUser $authUser, VillagePotential $villagePotential): bool
    {
        return $authUser->can('ForceDelete:VillagePotential');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VillagePotential');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VillagePotential');
    }

    public function replicate(AuthUser $authUser, VillagePotential $villagePotential): bool
    {
        return $authUser->can('Replicate:VillagePotential');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VillagePotential');
    }
}
