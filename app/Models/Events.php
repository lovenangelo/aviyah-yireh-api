<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('event')         
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Event {$this->title} was {$eventName}");
    }
}
