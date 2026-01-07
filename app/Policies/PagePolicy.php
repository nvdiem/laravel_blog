<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    /**
     * Determine whether the user can view any pages (admin area access).
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can access admin area
        return auth()->check();
    }

    /**
     * Determine whether the user can view a specific page.
     */
    public function view(User $user, Page $page): bool
    {
        // Any authenticated user can view pages in admin
        return auth()->check();
    }

    /**
     * Determine whether the user can create pages.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create pages for now
        return auth()->check();
    }

    /**
     * Determine whether the user can update the page.
     */
    public function update(User $user, Page $page): bool
    {
        // Any authenticated user can update pages for now
        return auth()->check();
    }

    /**
     * Determine whether the user can delete the page.
     */
    public function delete(User $user, Page $page): bool
    {
        // Any authenticated user can delete pages for now
        return auth()->check();
    }

    /**
     * Determine whether the user can upload bundles.
     */
    public function uploadBundle(User $user, Page $page): bool
    {
        // Any authenticated user can upload bundles for now
        return auth()->check();
    }
}
