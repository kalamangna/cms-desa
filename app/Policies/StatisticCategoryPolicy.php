<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StatisticCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StatisticCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StatisticCategory');
    }

    public function view(AuthUser $authUser, StatisticCategory $statisticCategory): bool
    {
        return $authUser->can('View:StatisticCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StatisticCategory');
    }

    public function update(AuthUser $authUser, StatisticCategory $statisticCategory): bool
    {
        return $authUser->can('Update:StatisticCategory');
    }

    public function delete(AuthUser $authUser, StatisticCategory $statisticCategory): bool
    {
        return $authUser->can('Delete:StatisticCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StatisticCategory');
    }

    public function restore(AuthUser $authUser, StatisticCategory $statisticCategory): bool
    {
        return $authUser->can('Restore:StatisticCategory');
    }

    public function forceDelete(AuthUser $authUser, StatisticCategory $statisticCategory): bool
    {
        return $authUser->can('ForceDelete:StatisticCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StatisticCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StatisticCategory');
    }

    public function replicate(AuthUser $authUser, StatisticCategory $statisticCategory): bool
    {
        return $authUser->can('Replicate:StatisticCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StatisticCategory');
    }
}
