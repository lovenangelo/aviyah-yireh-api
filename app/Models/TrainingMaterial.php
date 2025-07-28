<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingMaterial extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'language_id',
        'expiration_date',
        'title',
        'description',
        'duration',
        'path',
        'thumbnail_path',
        'is_visible',
    ];

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
