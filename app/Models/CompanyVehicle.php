<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVehicle extends Model
{
    protected $fillable = [
        'plate_number',
        'make_model',
        'year',
        'mileage',
        'color',
        'company_id',
    ];

    /**
     * Get the company that owns the vehicle.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
