<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts (admin area access).
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can access admin area
        return auth()->check();
    }

    /**
     * Determine whether the user can view a specific post.
     */
    public function view(User $user, Post $post): bool
    {
        // Any authenticated user can view posts in admin
        return auth()->check();
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->canDo('post.create');
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // User can edit any post if they have edit_any permission
        if ($user->canDo('post.edit_any')) {
            return true;
        }

        // User can edit their own posts if they have edit_own permission
        if ($user->canDo('post.edit_own') && $post->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        // User can delete any post if they have delete_any permission
        if ($user->canDo('post.delete_any')) {
            return true;
        }

        // User can delete their own posts if they have delete_own permission
        if ($user->canDo('post.delete_own') && $post->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can publish posts.
     */
    public function publish(User $user, Post $post = null): bool
    {
        return $user->canDo('post.publish');
    }

    /**
     * Determine whether the user can perform bulk actions.
     */
    public function bulkAction(User $user): bool
    {
        return $user->canDo('post.bulk_action');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        // Same permissions as delete
        return $this->delete($user, $post);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // Only super admins can permanently delete
        return $user->hasRole('super_admin');
    }
}
