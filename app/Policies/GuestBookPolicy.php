<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GuestBook;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuestBookPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GuestBook');
    }

    public function view(AuthUser $authUser, GuestBook $guestBook): bool
    {
        return $authUser->can('View:GuestBook');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GuestBook');
    }

    public function update(AuthUser $authUser, GuestBook $guestBook): bool
    {
        return $authUser->can('Update:GuestBook');
    }

    public function delete(AuthUser $authUser, GuestBook $guestBook): bool
    {
        return $authUser->can('Delete:GuestBook');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GuestBook');
    }

    public function restore(AuthUser $authUser, GuestBook $guestBook): bool
    {
        return $authUser->can('Restore:GuestBook');
    }

    public function forceDelete(AuthUser $authUser, GuestBook $guestBook): bool
    {
        return $authUser->can('ForceDelete:GuestBook');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GuestBook');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GuestBook');
    }

    public function replicate(AuthUser $authUser, GuestBook $guestBook): bool
    {
        return $authUser->can('Replicate:GuestBook');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GuestBook');
    }

}