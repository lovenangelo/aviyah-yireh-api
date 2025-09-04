<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];
}
