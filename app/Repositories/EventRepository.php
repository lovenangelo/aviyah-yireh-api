<?php

namespace App\Repositories;

use App\Models\Events;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class EventRepository extends BaseRepository
{
    public function model(): string
    {
        return Events::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'title',
            'description',
            'location',
            'start_at',
            'end_at',
        ];
    }

    private function baseQuery(): Builder
    {
        return $this->model->newQuery();
    }

    private function executeQuery(Builder $query, $perPage = null)
    {
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getEvents($perPage = null)
    {
        return $this->executeQuery($this->baseQuery(), $perPage);
    }

    public function getFilter($filters, $perPage = null)
    {
        $query = $this->baseQuery()->filter($filters);

        return $this->executeQuery($query, $perPage);
    }

    public function allUserEvents($perPage = null)
    {
        $query = User::with('events')->select('id', 'name')->has('events');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getFiltered(array $params = [])
    {
        $perPage = $params['per_page'] ?? null;
        $query = $this->baseQuery();

        if (isset($params['filters'])) {
            $query->filter($params['filters']);
        }

        if (isset($params['start_date'])) {
            $query->where('start_at', '>=', $params['start_date']);
        }

        if (isset($params['end_date'])) {
            $query->where('end_at', '<=', $params['end_date']);
        }

        if (isset($params['location'])) {
            $query->where('location', 'like', "%{$params['location']}%");
        }

        if (isset($params['author_id'])) {
            $query->where('author_id', $params['author_id']);
        }

        if (isset($params['search'])) {
            $searchTerm = $params['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        if (isset($params['sort_by'])) {
            $direction = $params['sort_direction'] ?? 'desc';
            $query->orderBy($params['sort_by'], $direction);
        } else {
            $query->orderBy('start_at', 'asc'); // Default sort by event start time
        }

        if ($params['with_author'] ?? false) {
            $query->with('author');
        }

        return $this->executeQuery($query, $perPage);
    }

    public function getUpcomingEvents($perPage = null)
    {
        $query = $this->baseQuery()
            ->where('start_at', '>', now())
            ->orderBy('start_at', 'asc');

        return $this->executeQuery($query, $perPage);
    }

    public function getPastEvents($perPage = null)
    {
        $query = $this->baseQuery()
            ->where('end_at', '<', now())
            ->orderBy('start_at', 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function getCurrentEvents($perPage = null)
    {
        $now = now();
        $query = $this->baseQuery()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->orderBy('start_at', 'asc');

        return $this->executeQuery($query, $perPage);
    }

    public function getEventsByLocation($location, $perPage = null)
    {
        $query = $this->baseQuery()
            ->where('location', 'like', "%{$location}%")
            ->orderBy('start_at', 'asc');

        return $this->executeQuery($query, $perPage);
    }

    public function getEventsByAuthor($authorId, $perPage = null)
    {
        $query = $this->baseQuery()
            ->where('author_id', $authorId)
            ->orderBy('start_at', 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function storeEvents($input): Events
    {
        return $this->model->create([
            'title' => $input->title,
            'description' => $input->description,
            'location' => $input->location,
            'start_at' => $input->start_at,
            'end_at' => $input->end_at,
            'image_url' => $input->image_url,
            'author_id' => auth()->id(),
        ]);
    }

    public function showUserEvent($id)
    {
        return User::with('events')->select('id', 'name')->where('id', $id)->first();
    }

    public function bulkDestroy(array $eventIds)
    {
        $result = [
            'deleted' => 0,
            'failed' => 0,
            'attempted' => count($eventIds),
            'failed_details' => [],
        ];

        foreach ($eventIds as $eventId) {
            try {
                $this->delete($eventId);
                $result['deleted']++;
            } catch (\Exception $e) {
                $result['failed']++;
                $result['failed_details'][] = [
                    'id' => $eventId,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $result;
    }
}
