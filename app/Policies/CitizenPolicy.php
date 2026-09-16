<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Citizen;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CitizenPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Citizen');
    }

    public function view(AuthUser $authUser, Citizen $citizen): bool
    {
        return $authUser->can('View:Citizen');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Citizen');
    }

    public function update(AuthUser $authUser, Citizen $citizen): bool
    {
        return $authUser->can('Update:Citizen');
    }

    public function delete(AuthUser $authUser, Citizen $citizen): bool
    {
        return $authUser->can('Delete:Citizen');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Citizen');
    }

    public function restore(AuthUser $authUser, Citizen $citizen): bool
    {
        return $authUser->can('Restore:Citizen');
    }

    public function forceDelete(AuthUser $authUser, Citizen $citizen): bool
    {
        return $authUser->can('ForceDelete:Citizen');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Citizen');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Citizen');
    }

    public function replicate(AuthUser $authUser, Citizen $citizen): bool
    {
        return $authUser->can('Replicate:Citizen');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Citizen');
    }
}
