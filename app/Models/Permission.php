<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    // Check if you have any custom relationships or accessors
    // that might be causing the issue

    // Look for something like this that could cause problems:
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }
}
