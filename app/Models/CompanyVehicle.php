<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
