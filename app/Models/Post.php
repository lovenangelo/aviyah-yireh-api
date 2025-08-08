<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Optional
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    // use SoftDeletes; // Uncomment if you want soft deletes

    protected $fillable = [
        'title',
        'content',
        'status',
        'slug',
        'meta_data',
        'user_id',
        'published_at',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        // Add any fields you want to hide from API responses
    ];


    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
                
         
                $originalSlug = $model->slug;
                $count = 1;
                while (static::where('slug', $model->slug)->exists()) {
                    $model->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->getOriginal('slug'))) {
                $model->slug = Str::slug($model->title);
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at->isPast();
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        
        // Only auto-generate slug if it's empty
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
