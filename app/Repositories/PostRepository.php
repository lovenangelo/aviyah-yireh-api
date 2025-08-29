<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PostRepository extends BaseRepository
{
    public function model(): string
    {
        return Post::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'title',
            'content',
            'status',
            'published_at',
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

    public function getPosts($perPage = null)
    {
        return $this->executeQuery($this->baseQuery(), $perPage);
    }

    public function getFilter($filters, $perPage = null)
    {
        $query = $this->baseQuery()->filter($filters);

        return $this->executeQuery($query, $perPage);
    }

    public function allUserPosts($perPage = null)
    {
        $query = User::with('posts')->select('id', 'name')->has('posts');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getFiltered(array $params = [])
    {
        $perPage = min($params['per_page'] ?? 15, 100); // Max 100 items per page
        $query = $this->baseQuery();

        // Search functionality
        if (isset($params['search'])) {
            $searchTerm = $params['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        // Filter by user
        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }

        // Recent posts filter
        if (isset($params['recent_days'])) {
            $query->where('created_at', '>=', now()->subDays($params['recent_days']));
        }

        // Date range filter
        if (isset($params['start_date'])) {
            $query->whereDate('created_at', '>=', $params['start_date']);
        }

        if (isset($params['end_date'])) {
            $query->whereDate('created_at', '<=', $params['end_date']);
        }

        // Include relationships
        if (isset($params['include'])) {
            $includes = explode(',', $params['include']);
            $allowedIncludes = ['user']; // Define allowed relationships
            $validIncludes = array_intersect($includes, $allowedIncludes);
            if (! empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        // Sorting
        $sortField = $params['sort'] ?? 'created_at';
        $sortDirection = $params['direction'] ?? 'desc';

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'title', 'status', 'created_at', 'updated_at', 'published_at'];
        if (! in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function getPublishedPosts(array $params = [])
    {
        $params['status'] = 'published';

        return $this->getFiltered($params);
    }

    public function getDraftPosts($perPage = null)
    {
        $query = $this->baseQuery()
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function getArchivedPosts($perPage = null)
    {
        $query = $this->baseQuery()
            ->where('status', 'archived')
            ->orderBy('created_at', 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function getPostsByUser($userId, $perPage = null)
    {
        $query = $this->baseQuery()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function getRecentPosts($days = 7, $perPage = null)
    {
        $query = $this->baseQuery()
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc');

        return $this->executeQuery($query, $perPage);
    }

    public function storePosts($input): Post
    {
        $data = [
            'title' => $input['title'],
            'content' => $input['content'],
            'status' => $input['status'] ?? 'draft',
        ];

        // Add authenticated user if available
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        } elseif (isset($input['user_id'])) {
            $data['user_id'] = $input['user_id'];
        }

        // Set published_at if status is published and no date provided
        if ($data['status'] === 'published' && empty($input['published_at'])) {
            $data['published_at'] = now();
        } elseif (isset($input['published_at'])) {
            $data['published_at'] = $input['published_at'];
        }

        return $this->model->create($data);
    }

    public function updatePost($id, $input): ?Post
    {
        $post = $this->find($id);

        if (! $post) {
            return null;
        }

        $data = array_filter([
            'title' => $input['title'] ?? null,
            'content' => $input['content'] ?? null,
            'status' => $input['status'] ?? null,
            'published_at' => $input['published_at'] ?? null,
        ]);

        // Set published_at if status is changing to published and no date provided
        if (
            isset($data['status']) &&
            $data['status'] === 'published' &&
            $post->status !== 'published' &&
            empty($data['published_at'])
        ) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return $post;
    }

    public function showUserPost($id)
    {
        return User::with('posts')->select('id', 'name')->where('id', $id)->first();
    }

    public function bulkDestroy(array $postIds)
    {
        $result = [
            'deleted' => 0,
            'failed' => 0,
            'attempted' => count($postIds),
            'failed_details' => [],
        ];

        foreach ($postIds as $postId) {
            try {
                $this->delete($postId);
                $result['deleted']++;
            } catch (\Exception $e) {
                $result['failed']++;
                $result['failed_details'][] = [
                    'id' => $postId,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $result;
    }

    public function bulkUpdate(array $postIds, array $data)
    {
        $result = [
            'updated' => 0,
            'failed' => 0,
            'attempted' => count($postIds),
            'failed_details' => [],
        ];

        foreach ($postIds as $postId) {
            try {
                $post = $this->find($postId);
                if ($post) {
                    $post->update($data);
                    $result['updated']++;
                } else {
                    $result['failed']++;
                    $result['failed_details'][] = [
                        'id' => $postId,
                        'reason' => 'Post not found',
                    ];
                }
            } catch (\Exception $e) {
                $result['failed']++;
                $result['failed_details'][] = [
                    'id' => $postId,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $result;
    }
}
