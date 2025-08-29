<?php

namespace App\Http\Controllers\API\V1\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest as PostStorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest as PostUpdatePostRequest;
use App\Http\Resources\CustomPaginatedCollection;
use App\Repositories\PostRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponse;

    protected $postRepository;

    private const POST_NOT_FOUND_MESSAGE = 'Post not found';

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
        // Add middleware if needed
        // $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $params = $request->only([
                'search',
                'status',
                'user_id',
                'recent_days',
                'start_date',
                'end_date',
                'include',
                'sort',
                'direction',
                'per_page',
            ]);

            $posts = $this->postRepository->getFiltered($params);
            $response = new CustomPaginatedCollection($posts, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'Posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve posts');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStorePostRequest $request)
    {
        try {
            $validated = $request->validated();
            $post = $this->postRepository->storePosts($validated);

            // Load relationships if requested
            if ($request->filled('include')) {
                $includes = explode(',', $request->include);
                $allowedIncludes = ['user'];
                $validIncludes = array_intersect($includes, $allowedIncludes);
                if (! empty($validIncludes)) {
                    $post->load($validIncludes);
                }
            }

            return $this->formatSuccessResponse($post, 'Post created successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to create post');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $post = $this->postRepository->showUserPost($id);

            if (! $post) {
                return $this->formatErrorResponse(self::POST_NOT_FOUND_MESSAGE, 404);
            }

            if ($request->filled('include')) {
                $includes = explode(',', $request->include);
                $allowedIncludes = ['user'];
                $validIncludes = array_intersect($includes, $allowedIncludes);
                if (! empty($validIncludes)) {
                    $post->load($validIncludes);
                }
            }

            return $this->formatSuccessResponse($post, 'Post retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, self::POST_NOT_FOUND_MESSAGE);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdatePostRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $post = $this->postRepository->updatePost($id, $validated);

            if (! $post) {
                return $this->formatErrorResponse(self::POST_NOT_FOUND_MESSAGE, 404);
            }

            if ($request->filled('include')) {
                $includes = explode(',', $request->include);
                $allowedIncludes = ['user'];
                $validIncludes = array_intersect($includes, $allowedIncludes);
                if (! empty($validIncludes)) {
                    $post->load($validIncludes);
                }
            }

            return $this->formatSuccessResponse($post, 'Post updated successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to update post');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        try {
            $post = $this->postRepository->find($id);

            if (! $post) {
                return $this->formatErrorResponse(self::POST_NOT_FOUND_MESSAGE, 404);
            }

            $this->postRepository->delete($id);

            $this->formatSuccessResponse(null, 'Post deleted successfully', 204);
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to delete post');
        }
    }

    /**
     * Additional API endpoints
     */

    /**
     * Get published posts only
     */
    public function published(Request $request)
    {
        try {
            $params = $request->only(['search', 'per_page', 'include', 'sort', 'direction']);
            $posts = $this->postRepository->getPublishedPosts($params);
            $response = new CustomPaginatedCollection($posts, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'Published posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve published posts');
        }
    }

    /**
     * Get draft posts
     */
    public function drafts(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);
            $posts = $this->postRepository->getDraftPosts($perPage);
            $response = new CustomPaginatedCollection($posts, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'Draft posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve draft posts');
        }
    }

    /**
     * Get archived posts
     */
    public function archived(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);
            $posts = $this->postRepository->getArchivedPosts($perPage);
            $response = new CustomPaginatedCollection($posts, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'Archived posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve archived posts');
        }
    }

    /**
     * Get recent posts
     */
    public function recent(Request $request)
    {
        try {
            $days = $request->get('days', 7);
            $perPage = min($request->get('per_page', 15), 100);
            $posts = $this->postRepository->getRecentPosts($days, $perPage);
            $response = new CustomPaginatedCollection($posts, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'Recent posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve recent posts');
        }
    }

    /**
     * Get posts by user
     */
    public function byUser(Request $request, $userId)
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);
            $posts = $this->postRepository->getPostsByUser($userId, $perPage);
            $response = new CustomPaginatedCollection($posts, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'User posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve user posts');
        }
    }

    /**
     * Bulk operations
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:posts,id',
            ]);

            $result = $this->postRepository->bulkDestroy($request->ids);

            return $this->formatSuccessResponse([
                'message' => "Successfully deleted {$result['deleted']} posts",
                'details' => $result,
            ], 'Bulk delete successful');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to bulk delete posts');
        }
    }

    public function bulkUpdate(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:posts,id',
                'status' => 'required|in:draft,published,archived',
            ]);

            $updateData = ['status' => $request->status];

            // If changing to published, set published_at
            if ($request->status === 'published') {
                $updateData['published_at'] = now();
            }

            $result = $this->postRepository->bulkUpdate($request->ids, $updateData);

            return $this->formatSuccessResponse([
                'message' => "Successfully updated {$result['updated']} posts",
                'details' => $result,
            ], 'Bulk update successful');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to bulk update posts');
        }
    }

    /**
     * Get all users with posts
     */
    public function usersWithPosts(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);
            $users = $this->postRepository->allUserPosts($perPage);
            $response = new CustomPaginatedCollection($users, $request->get('include_links', false));

            return $this->formatSuccessResponse($response, 'Users with posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Failed to retrieve users with posts');
        }
    }

    /**
     * Show user with their posts
     */
    public function showUserPosts($userId)
    {
        try {
            $user = $this->postRepository->showUserPost($userId);
            if (! $user) {
                return $this->formatErrorResponse('User not found', 404);
            }

            return $this->formatSuccessResponse($user, 'User posts retrieved successfully');
        } catch (\Throwable $th) {
            return $this->handleApiException($th, request(), 'Failed to retrieve user posts');
        }
    }
}
