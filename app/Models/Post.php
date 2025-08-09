<?php

namespace App\Models;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Optional
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;

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

        static::created(function ($event) {
            $event->logEventAction('created');
        });

        static::updated(function ($event) {
            $event->logEventAction('updated');
        });

        static::deleted(function ($event) {
            $event->logEventAction('deleted');
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

    public function logActivity(string $description, array $properties = []): void
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($this)
            ->withProperties(array_merge([
                'user_name' =>  Auth::user()?->name,
                'user_email' =>  Auth::user()?->email,
                'user_role' =>  Auth::user()?->role?->name,
            ], $properties))
            ->log($description);
    }


    public function logEventAction(string $actionType): void
    {
        $description = "Event \"{$this->title}\" was {$actionType}";

        $properties = [
            'action_type' => $actionType,
            'event_id' => $this->id,
            'event_title' => $this->title,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'action_time' => now()->toDateTimeString(),
        ];

        $this->logActivity($description, $properties);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'start_date']) // specify fields to log
            ->logOnlyDirty() // only log changed attributes
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Event \"{$this->title}\" was {$eventName}");
    }
}
