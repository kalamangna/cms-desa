<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BudgetRealization;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetRealizationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BudgetRealization');
    }

    public function view(AuthUser $authUser, BudgetRealization $budgetRealization): bool
    {
        return $authUser->can('View:BudgetRealization');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BudgetRealization');
    }

    public function update(AuthUser $authUser, BudgetRealization $budgetRealization): bool
    {
        return $authUser->can('Update:BudgetRealization');
    }

    public function delete(AuthUser $authUser, BudgetRealization $budgetRealization): bool
    {
        return $authUser->can('Delete:BudgetRealization');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BudgetRealization');
    }

    public function restore(AuthUser $authUser, BudgetRealization $budgetRealization): bool
    {
        return $authUser->can('Restore:BudgetRealization');
    }

    public function forceDelete(AuthUser $authUser, BudgetRealization $budgetRealization): bool
    {
        return $authUser->can('ForceDelete:BudgetRealization');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BudgetRealization');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BudgetRealization');
    }

    public function replicate(AuthUser $authUser, BudgetRealization $budgetRealization): bool
    {
        return $authUser->can('Replicate:BudgetRealization');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BudgetRealization');
    }

}