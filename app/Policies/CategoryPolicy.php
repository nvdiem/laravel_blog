<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories (admin area access).
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can access admin area
        return auth()->check();
    }

    /**
     * Determine whether the user can view a specific category.
     */
    public function view(User $user, Category $category): bool
    {
        // Any authenticated user can view categories in admin
        return auth()->check();
    }

    /**
     * Determine whether the user can manage taxonomy (create, edit, delete categories).
     */
    public function manage(User $user): bool
    {
        return $user->canDo('taxonomy.manage');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can restore the category.
     */
    public function restore(User $user, Category $category): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can permanently delete the category.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        // Only super admins can permanently delete categories
        return $user->hasRole('super_admin');
    }
}
