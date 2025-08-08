<?php

namespace App\Http\Controllers\Api\V1\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class PostController extends Controller
{
    public function __construct()
    {
        // Add middleware if needed
        // $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Recent posts filter
        if ($request->filled('recent_days')) {
            $query->recent($request->recent_days);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Include relationships
        if ($request->filled('include')) {
            $includes = explode(',', $request->include);
            $allowedIncludes = ['user']; // Define allowed relationships
            $validIncludes = array_intersect($includes, $allowedIncludes);
            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'title', 'status', 'created_at', 'updated_at', 'published_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        
        $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');

        // Pagination
        $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
        $posts = $query->paginate($perPage);

        return new PostCollection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        // Add authenticated user if available
            if (Auth::check()) {
                $validated['user_id'] = Auth::id();
            }

        // Set published_at if status is published and no date provided
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        // Load relationships if requested
        if ($request->filled('include')) {
            $includes = explode(',', $request->include);
            $allowedIncludes = ['user'];
            $validIncludes = array_intersect($includes, $allowedIncludes);
            if (!empty($validIncludes)) {
                $post->load($validIncludes);
            }
        }

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
        public function show(Request $request, $id)
        {
            $post = Post::find($id);

            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            // Load relationships if requested
            if ($request->filled('include')) {
                $includes = explode(',', $request->include);
                $allowedIncludes = ['user'];
                $validIncludes = array_intersect($includes, $allowedIncludes);
                if (!empty($validIncludes)) {
                    $post->load($validIncludes);
                }
            }

            return new PostResource($post);
        }


    /**
     * Update the specified resource in storage.
     */
public function update(UpdatePostRequest $request, $id)
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post not found'], 404);
    }

    $validated = $request->validated();

    if ($validated['status'] === 'published' &&
        $post->status !== 'published' &&
        empty($validated['published_at'])) {
        $validated['published_at'] = now();
    }

    $post->update($validated);

    if ($request->filled('include')) {
        $includes = explode(',', $request->include);
        $allowedIncludes = ['user'];
        $validIncludes = array_intersect($includes, $allowedIncludes);
        if (!empty($validIncludes)) {
            $post->load($validIncludes);
        }
    }

    return new PostResource($post);
}


    /**
     * Remove the specified resource from storage.
     */




    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }


    /**
     * Additional API endpoints
     */
    
    /**
     * Get published posts only
     */
    public function published(Request $request)
    {
        $query = Post::published();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $posts = $query->paginate($perPage);

        return new PostCollection($posts);
    }

    /**
     * Bulk operations
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:posts,id'
        ]);

        $deletedCount = Post::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => "Successfully deleted {$deletedCount} posts"
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:posts,id',
            'status' => 'required|in:draft,published,archived'
        ]);

        $updatedCount = Post::whereIn('id', $request->ids)
                           ->update(['status' => $request->status]);

        return response()->json([
            'message' => "Successfully updated {$updatedCount} posts"
        ]);
    }
}
