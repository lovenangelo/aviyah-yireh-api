<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cost',
        'selling_price',
        'company_id',
        'category_id',
    ];
}
