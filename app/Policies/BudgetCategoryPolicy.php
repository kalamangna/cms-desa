<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BudgetCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BudgetCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BudgetCategory');
    }

    public function view(AuthUser $authUser, BudgetCategory $budgetCategory): bool
    {
        return $authUser->can('View:BudgetCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BudgetCategory');
    }

    public function update(AuthUser $authUser, BudgetCategory $budgetCategory): bool
    {
        return $authUser->can('Update:BudgetCategory');
    }

    public function delete(AuthUser $authUser, BudgetCategory $budgetCategory): bool
    {
        return $authUser->can('Delete:BudgetCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BudgetCategory');
    }

    public function restore(AuthUser $authUser, BudgetCategory $budgetCategory): bool
    {
        return $authUser->can('Restore:BudgetCategory');
    }

    public function forceDelete(AuthUser $authUser, BudgetCategory $budgetCategory): bool
    {
        return $authUser->can('ForceDelete:BudgetCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BudgetCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BudgetCategory');
    }

    public function replicate(AuthUser $authUser, BudgetCategory $budgetCategory): bool
    {
        return $authUser->can('Replicate:BudgetCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BudgetCategory');
    }
}
