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

    /**
     * Get the Company for the Category post.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Item::class, 'company_id');
    }
}
