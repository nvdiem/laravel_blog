<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Keep for backward compatibility
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * User's roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * User's permissions through roles
     */
    public function permissions()
    {
        return Permission::join('role_permission', 'permissions.id', '=', 'role_permission.permission_id')
            ->join('user_role', function ($join) {
                $join->on('role_permission.role_id', '=', 'user_role.role_id')
                     ->where('user_role.user_id', '=', $this->id);
            })
            ->select('permissions.*');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function canDo(string $permissionSlug): bool
    {
        // Check if user has permission through any of their roles
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role && !$this->hasRole($roleSlug)) {
            $this->roles()->attach($role);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $this->roles()->detach($role);
        }
    }

    /**
     * BACKWARD COMPATIBILITY: Check if user is admin
     * Maps to super_admin role in new system
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->role === 'admin';
    }

    /**
     * BACKWARD COMPATIBILITY: Check if user is editor
     * Maps to editor role in new system
     */
    public function isEditor(): bool
    {
        return $this->hasRole('editor') || $this->role === 'editor';
    }
}
