<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    protected $appends = [
        'service',
    ];

    public function getServiceAttribute()
    {
        return $this->services()->get();
    }

    /**
     * Get the Service for the Service Category.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }

    /**
     * Get the Company for the Service Category.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'company_id');
    }
}
