<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Dusun;
use Illuminate\Auth\Access\HandlesAuthorization;

class DusunPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Dusun');
    }

    public function view(AuthUser $authUser, Dusun $dusun): bool
    {
        return $authUser->can('View:Dusun');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Dusun');
    }

    public function update(AuthUser $authUser, Dusun $dusun): bool
    {
        return $authUser->can('Update:Dusun');
    }

    public function delete(AuthUser $authUser, Dusun $dusun): bool
    {
        return $authUser->can('Delete:Dusun');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Dusun');
    }

    public function restore(AuthUser $authUser, Dusun $dusun): bool
    {
        return $authUser->can('Restore:Dusun');
    }

    public function forceDelete(AuthUser $authUser, Dusun $dusun): bool
    {
        return $authUser->can('ForceDelete:Dusun');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Dusun');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Dusun');
    }

    public function replicate(AuthUser $authUser, Dusun $dusun): bool
    {
        return $authUser->can('Replicate:Dusun');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Dusun');
    }

}