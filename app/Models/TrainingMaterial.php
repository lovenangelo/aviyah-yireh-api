<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingMaterial extends Model
{
    //
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
