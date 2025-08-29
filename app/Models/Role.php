<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'guard_name',
    ];

    protected $hidden = [
        'pivot',
    ];

    protected $appends = [
        'permissions_list',
        'users_count'
    ];

    // Filterable fields for your existing filter system
    public function scopeFilter($query, array $filters)
    {
        // Search functionality
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        if (!empty($filters['sort'])) {
            $sortField = $filters['sort'];
            $sortDirection = 'asc';

            if (str_starts_with($sortField, '-')) {
                $sortDirection = 'desc';
                $sortField = substr($sortField, 1);
            }

            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    // Accessor to get permissions as array
    public function getPermissionsListAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }

    // Accessor to get users count
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }
}
