<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    protected $appends = [
        'item',
    ];

    public function getItemAttribute()
    {
        return $this->items()->get();
    }

    /**
     * Get the item for the Item post.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    /**
     * Get the Company for the Category post.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Item::class, 'company_id');
    }
}
