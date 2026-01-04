<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\MediaPolicy;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        Category::class => CategoryPolicy::class,
        Media::class => MediaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define Gates for system-level permissions
        Gate::define('user.manage', function (User $user) {
            return $user->canDo('user.manage');
        });

        Gate::define('role.manage', function (User $user) {
            return $user->canDo('role.manage');
        });

        Gate::define('system.configure', function (User $user) {
            return $user->canDo('system.configure');
        });
    }
}
