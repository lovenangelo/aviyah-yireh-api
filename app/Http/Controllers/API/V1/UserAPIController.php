<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\BulkDestroyUsersRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use Illuminate\Http\UploadedFile;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Endpoints for managing users, including CRUD operations, bulk actions, and profile management."
 * )
 */
class UserAPIController extends Controller
{
    private UserRepository $userRepository;
    private const USER = 'App\Models\User';
    private const USER_NOT_AUTHENTICATED = 'User not authenticated.';
    private const USER_NOT_FOUND = 'User not found.';

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Get list of users",
     *     tags={"Users"},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="roles", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of users")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user has permission to view all users
        $this->authorize('viewAny', self::USER);

        $filters = $request->only([
            'page',
            'per_page',
            'sort',
            'search',
            'roles',
        ]);

        if ($filters) {
            $users = $this->userRepository
                ->getFilter($filters)
                ->with('role')
                ->paginate($filters['per_page'] ?? 10)
                ->withQueryString();
        } else {
            $users = $this->userRepository->getAll();
        }

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "role_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="role_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully")
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        // Check if user has permission to create a new user
        $this->authorize('create', self::USER);

        $user = $this->userRepository->createNewUser($request->all());

        return response()->json([
            'message' => 'User created successfully. A password reset email has been sent. Please advice the user to reset their password.',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Get a user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User details"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id): JsonResponse
    {
        // Get user
        $user = $this->userRepository->find($id);

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => self::USER_NOT_FOUND,
            ], 404);
        }

        // Check if user has permission to view the user
        $this->authorize('view', $user);

        // Load related data
        $user->load('role');

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "role_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="role_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        // Get user
        $user = $this->userRepository->find($id);

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => self::USER_NOT_FOUND,
            ], 404);
        }

        // Check if user has permission to update the user
        $this->authorize('update', $user);

        // Prepare update data
        $data = $request->only(['name', 'email', 'phone', 'role_id']);

        // Update user
        $this->userRepository->update($data, $user->id);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $this->userRepository->find($user->id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(UpdateUserRequest $request, ?int $id = null): JsonResponse
    {
        // Get user
        $user = $id ? $this->userRepository->find($id) : Auth::user();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        // Check if user has permission to update the user
        $this->authorize('update', $user);

        // Prepare update data
        $data = $request->only(['name', 'email', 'phone']);

        // Update user
        $this->userRepository->update($data, $user->id);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $this->userRepository->find($user->id),
        ]);
    }

    /**
     * Update the password of the specified user.
     */
    public function updatePassword(UpdateUserPasswordRequest $request): JsonResponse
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'message' => self::USER_NOT_AUTHENTICATED,
            ], 404);
        }

        // Check if user has permission to update the user
        $this->authorize('updatePassword', $user);

        // Update user password
        $this->userRepository->updateUserPassword($user->id, $request->password);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy($id): JsonResponse
    {
        // Get authenticated user
        $user = Auth::user();

        // Get user to delete
        $userToDelete = $this->userRepository->find($id);

        if (!$userToDelete) {
            return response()->json([
                'message' => self::USER_NOT_FOUND,
            ], 404);
        }

        // Check if user has permission to delete the user
        $this->authorize('delete', $userToDelete);

        // Delete user
        $result = $this->userRepository->destroyUser((int)$id, $user->id);

        // Return appropriate response based on result
        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    /**
     * Remove multiple users from storage.
     *
     * @OA\Post(
     *     path="/api/v1/users/bulk-destroy",
     *     summary="Bulk delete users",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Users deleted successfully")
     * )
     */
    public function bulkDestroy(BulkDestroyUsersRequest $request): JsonResponse
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if user has permission to bulk delete users
        $this->authorize('bulkDelete', self::USER);

        // Get validated data
        $ids = $request->validated('ids');

        // Delete multiple users
        $result = $this->userRepository->bulkDestroy($ids, $user->id);

        return response()->json([
            'message' => $result['deleted'] . ' users deleted successfully',
            'details' => [
                'deleted' => $result['deleted'],
                'failed' => $result['failed'],
                'total_attempted' => $result['attempted'],
                'self_delete_attempt' => $result['self_delete_attempt']
                    ? 'Self-deletion was attempted and skipped'
                    : null,
            ]
        ]);
    }

    /**
     * Upload or update user avatar.
     */
    public function uploadAvatar(UpdateUserAvatarRequest $request): JsonResponse
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'message' => self::USER_NOT_AUTHENTICATED,
            ], 401);
        }

        // Check if user has permission to update their avatar
        $this->authorize('update', $user);

        // Upload avatar
        /** @var UploadedFile $avatarFile */
        $avatarFile = $request->file('avatar');
        if (!$avatarFile || !($avatarFile instanceof UploadedFile)) {
            return response()->json([
                'message' => 'Avatar file is required.',
            ], 400);
        }

        $result = $this->userRepository->updateAvatar($user->id, $avatarFile);

        return response()->json([
            'message' => $result['message'],
            'avatar_url' => $result['avatar_url'] ?? null,
        ], $result['status']);
    }

    /**
     * Delete user avatar.
     */
    public function deleteAvatar(): JsonResponse
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Check if user has permission to update their avatar
        $this->authorize('update', $user);

        // Delete avatar
        $result = $this->userRepository->deleteAvatar($user->id);

        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }
}
