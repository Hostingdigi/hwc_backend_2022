<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Back-office user (table: admins). User model of the "admin" auth guard.
 *
 * priority = 0 is a super admin. Any other value is the id of a role_rights row
 * whose comma-separated "rights" lists the admin module ids the user may open
 * (see App\Http\Middleware\AuthorizeAdminModule for the module map).
 */
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'designation',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    private $moduleRights = null;

    public function isSuperAdmin(): bool
    {
        return (int) $this->priority === 0;
    }

    /**
     * Module ids this admin's role grants (empty for super admins, who get everything).
     *
     * @return string[]
     */
    public function moduleRights(): array
    {
        if ($this->moduleRights === null) {
            $rights = $this->isSuperAdmin() ? null : RoleRights::where('id', $this->priority)->value('rights');
            $this->moduleRights = $rights ? array_map('trim', explode(',', $rights)) : [];
        }

        return $this->moduleRights;
    }

    public function canAccessModule(int $moduleId): bool
    {
        return $this->isSuperAdmin() || in_array((string) $moduleId, $this->moduleRights(), true);
    }
}
