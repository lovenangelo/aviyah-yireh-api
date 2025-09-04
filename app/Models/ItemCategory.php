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
        return $this->item_setup()->get();
    }

    /**
     * Get the item_setup for the Item SetUp post.
     */
    public function item(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

     /**
      * Get the Company for the Category post.
      */
    public function company(): HasMany
    {
        return $this->hasMany(Item::class, 'company_id');
    }
}
