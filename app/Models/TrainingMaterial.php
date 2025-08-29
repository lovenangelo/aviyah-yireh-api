<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrainingMaterial extends Model
{
    public const SELECT_TRAINING_MATERIALS_ALL = 'training_materials.*';

    protected $hidden = ['created_at', 'category_id', 'language_id', 'user_id'];

    protected $fillable = [
        'user_id',
        'category_id',
        'language_id',
        'expiration_date',
        'title',
        'description',
        'duration',
        'files',
        'thumbnail_path',
        'views',
        'is_visible',
        'status',
        'is_featured',
    ];

    /**
     * Determine if the user is an administrator.
     */
    protected function getIsDraftAttribute()
    {
        return $this->status === 0;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_draft'];

    protected $casts = [
        'duration' => 'double',
        'is_visible' => 'boolean',
        'files' => 'array',
        'status' => 'integer',
    ];

    /**
     * Scope a query to filter the training materials.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query = $this->applyDefaultSorting($query, $filters);
        $query = $this->applyCustomSorting($query, $filters);
        $query = $this->applySearch($query, $filters);
        $query = $this->applyCategoryFilter($query, $filters);
        $query = $this->applyLanguageFilter($query, $filters);
        $query = $this->applyUserFilter($query, $filters);
        $query = $this->applyVisibilityFilter($query, $filters);
        $query = $this->applyFeaturedFilter($query, $filters);
        $query = $this->applyStatusFilter($query, $filters);
        $query = $this->applyDurationFilter($query, $filters);
        $query = $this->applyViewsFilter($query, $filters);
        $query = $this->applyExpirationFilter($query, $filters);
        $query = $this->applyDateRangeFilter($query, $filters);

        return $query;
    }

    private function applyDefaultSorting(Builder $query, array $filters): Builder
    {
        if (! array_key_exists('sort', $filters) ?? false) {
            return $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    private function applyCustomSorting(Builder $query, array $filters): Builder
    {
        if (isset($filters['sort']) && ! $filters['sort']) {
            $sortArr = explode('.', $filters['sort']);
            if ($filters['sort'] ?? false) {
                $query = $query->join('categories', 'training_materials.category_id', '=', 'categories.id')
                    ->select(self::SELECT_TRAINING_MATERIALS_ALL)
                    ->orderBy('categories.name', $sortArr[1] ?? 'asc');
            } elseif ($sortArr[0] === 'language') {
                $query = $query->join('languages', 'training_materials.language_id', '=', 'languages.id')
                    ->select(self::SELECT_TRAINING_MATERIALS_ALL)
                    ->orderBy('languages.name', $sortArr[1] ?? 'asc');
            } elseif ($sortArr[0] === 'user') {
                $query = $query->join('users', 'training_materials.user_id', '=', 'users.id')
                    ->select(self::SELECT_TRAINING_MATERIALS_ALL)
                    ->orderBy('users.name', $sortArr[1] ?? 'asc');
            } else {
                $query = $query->orderBy($sortArr[0], $sortArr[1] ?? 'asc');
            }
        }

        return $query;
    }

    private function applySearch(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('training_materials.title', 'LIKE', "%{$filters['search']}%")
                    ->orWhere('training_materials.description', 'LIKE', "%{$filters['search']}%");
            });
        }

        return $query;
    }

    private function applyCategoryFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['categories']) && $filters['categories']) {
            $query->whereIn('category_id', array_map('intval', explode(',', $filters['categories'])));
        }

        return $query;
    }

    private function applyLanguageFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['languages']) && $filters['languages']) {
            $query->whereIn('language_id', array_map('intval', explode(',', $filters['languages'])));
        }

        return $query;
    }

    private function applyUserFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['users']) && $filters['users']) {
            $query->whereIn('user_id', array_map('intval', explode(',', $filters['users'])));
        }

        return $query;
    }

    private function applyVisibilityFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['is_visible']) && $filters['is_visible'] !== '') {
            $query->where('is_visible', (bool) $filters['is_visible']);
        }

        return $query;
    }

    private function applyFeaturedFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }

        return $query;
    }

    private function applyStatusFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    private function applyDurationFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['min_duration']) && $filters['min_duration'] !== '') {
            $query->where('duration', '>=', $filters['min_duration']);
        }
        if (isset($filters['max_duration']) && $filters['max_duration'] !== '') {
            $query->where('duration', '<=', $filters['max_duration']);
        }

        return $query;
    }

    private function applyViewsFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['min_views']) && $filters['min_views'] !== '') {
            $query->where('views', '>=', $filters['min_views']);
        }
        if (isset($filters['max_views']) && $filters['max_views'] !== '') {
            $query->where('views', '<=', $filters['max_views']);
        }

        return $query;
    }

    private function applyExpirationFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['expiring_soon']) && $filters['expiring_soon']) {
            $query->where('expiration_date', '<=', now()->addDays(30));
        }
        if (isset($filters['expired']) && $filters['expired']) {
            $query->where('expiration_date', '<', now());
        }

        return $query;
    }

    private function applyDateRangeFilter(Builder $query, array $filters): Builder
    {
        if (isset($filters['created_from']) && $filters['created_from']) {
            $query->where('created_at', '>=', $filters['created_from']);
        }
        if (isset($filters['created_to']) && $filters['created_to']) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        return $query;
    }

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
