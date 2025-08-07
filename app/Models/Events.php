<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
class Events extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_at',
        'end_at',
        'author_id',
        'image_url'
    ];

    public static array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location' => 'required|string',
        'start_at' => 'required|date',
        'end_at' => 'required|date',
        'image_url' => 'nullable|string'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, "author_id");
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (isset($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        if (isset($filters['start_at'])) {
            $query->where('start_at', '>=', $filters['start_at']);
        }

        if (isset($filters['end_at'])) {
            $query->where('end_at', '<=', $filters['end_at']);
        }

        if (isset($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }
        return $query;
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
        protected static function boot()
        {
            parent::boot();

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
}
