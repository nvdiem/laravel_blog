<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    /**
     * Determine whether the user can view any media (admin area access).
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can access admin area
        return auth()->check();
    }

    /**
     * Determine whether the user can view specific media.
     */
    public function view(User $user, Media $media): bool
    {
        // Any authenticated user can view media in admin
        return auth()->check();
    }

    /**
     * Determine whether the user can upload media.
     */
    public function upload(User $user): bool
    {
        return $user->canDo('media.upload');
    }

    /**
     * Determine whether the user can create media (same as upload).
     */
    public function create(User $user): bool
    {
        return $this->upload($user);
    }

    /**
     * Determine whether the user can update the media.
     */
    public function update(User $user, Media $media): bool
    {
        // Users can update their own media, or admins can update any
        return $media->created_by === $user->id || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete the media.
     */
    public function delete(User $user, Media $media): bool
    {
        // Check if user has media.delete permission
        if ($user->canDo('media.delete')) {
            return true;
        }

        // Users can delete their own media
        return $media->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the media.
     */
    public function restore(User $user, Media $media): bool
    {
        // Same permissions as delete
        return $this->delete($user, $media);
    }

    /**
     * Determine whether the user can permanently delete the media.
     */
    public function forceDelete(User $user, Media $media): bool
    {
        // Only super admins can permanently delete media
        return $user->hasRole('super_admin');
    }
}
