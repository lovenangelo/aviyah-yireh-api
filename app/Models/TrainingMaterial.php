<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
class TrainingMaterial extends Model
{

    protected $hidden = ['created_at', 'category_id', 'language_id', 'user_id'];
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

    protected $casts = [
        'duration' => 'double',
        'is_visible' => "boolean"
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

      public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable) // log only fillable attributes
            ->logOnlyDirty()           // log only changed values
            ->useLogName('training_material') // custom log name
            ->setDescriptionForEvent(function (string $eventName) {
                return "Training Material was {$eventName}";
            });
    }
}
