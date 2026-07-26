<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Dataset;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatasetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Dataset');
    }

    public function view(AuthUser $authUser, Dataset $dataset): bool
    {
        return $authUser->can('View:Dataset');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Dataset');
    }

    public function update(AuthUser $authUser, Dataset $dataset): bool
    {
        return $authUser->can('Update:Dataset');
    }

    public function delete(AuthUser $authUser, Dataset $dataset): bool
    {
        return $authUser->can('Delete:Dataset');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Dataset');
    }

    public function restore(AuthUser $authUser, Dataset $dataset): bool
    {
        return $authUser->can('Restore:Dataset');
    }

    public function forceDelete(AuthUser $authUser, Dataset $dataset): bool
    {
        return $authUser->can('ForceDelete:Dataset');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Dataset');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Dataset');
    }

    public function replicate(AuthUser $authUser, Dataset $dataset): bool
    {
        return $authUser->can('Replicate:Dataset');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Dataset');
    }

}