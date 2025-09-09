<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaborCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    /**
     * Get the company that owns the Labor.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
