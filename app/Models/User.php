<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'phone_number',
        'password',
        'role_id',
        'company_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function primaryRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function primaryCompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_companies');
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Check if user is system administrator
     */
    public function isAdmin(): bool
    {
        if (strtoupper($this->username) === 'ADMIN') {
            return true;
        }

        if ($this->primaryRole && strtoupper($this->primaryRole->name) === 'ADMIN') {
            return true;
        }

        return $this->roles->contains(function ($r) {
            return strtoupper($r->name) === 'ADMIN';
        });
    }

    /**
     * Check if user is authorized to access a specific company
     */
    public function canAccessCompany($companyId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->company_id && (int)$this->company_id === (int)$companyId) {
            return true;
        }

        return $this->companies->contains('id', (int)$companyId);
    }

    /**
     * Helper to check whether user can be safely deleted.
     */
    public function isDeletable(): bool
    {
        return strtoupper($this->username) !== 'ADMIN' && $this->id !== auth()->id();
    }

    /**
     * Core permission evaluation logic:
     * 1. Superuser ADMIN check -> true
     * 2. Direct User Permission Overrides check:
     *    - If is_granted === false -> false (Explicit Restrict)
     *    - If is_granted === true  -> true  (Explicit Allow)
     * 3. Roles Permission Check:
     *    - Check if ANY assigned role has the permission -> true
     */
    public function hasPermission(string $permissionKey): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // 1. Direct User Override check
        $userOverride = $this->userPermissions()
            ->whereHas('permission', function ($query) use ($permissionKey) {
                $query->where('name', $permissionKey);
            })->first();

        if ($userOverride !== null) {
            return (bool)$userOverride->is_granted;
        }

        // 2. Roles Permission Check (Union across primary role and assigned roles)
        $roleIds = $this->roles()->pluck('roles.id')->toArray();
        if ($this->role_id && !in_array($this->role_id, $roleIds)) {
            $roleIds[] = $this->role_id;
        }

        if (empty($roleIds)) {
            return false;
        }

        return Role::whereIn('id', $roleIds)
            ->whereHas('permissions', function ($query) use ($permissionKey) {
                $query->where('name', $permissionKey);
            })->exists();
    }
}
