<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PopupInfographic;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopupInfographicPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PopupInfographic');
    }

    public function view(AuthUser $authUser, PopupInfographic $popupInfographic): bool
    {
        return $authUser->can('View:PopupInfographic');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PopupInfographic');
    }

    public function update(AuthUser $authUser, PopupInfographic $popupInfographic): bool
    {
        return $authUser->can('Update:PopupInfographic');
    }

    public function delete(AuthUser $authUser, PopupInfographic $popupInfographic): bool
    {
        return $authUser->can('Delete:PopupInfographic');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PopupInfographic');
    }

    public function restore(AuthUser $authUser, PopupInfographic $popupInfographic): bool
    {
        return $authUser->can('Restore:PopupInfographic');
    }

    public function forceDelete(AuthUser $authUser, PopupInfographic $popupInfographic): bool
    {
        return $authUser->can('ForceDelete:PopupInfographic');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PopupInfographic');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PopupInfographic');
    }

    public function replicate(AuthUser $authUser, PopupInfographic $popupInfographic): bool
    {
        return $authUser->can('Replicate:PopupInfographic');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PopupInfographic');
    }

}