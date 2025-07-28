<?php

namespace App\Http\Controllers\API\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UserStoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\BulkDestroyUsersRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserAvatarRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\UploadedFile;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Endpoints for managing users, including CRUD operations, bulk actions, and profile management."
 * )
 */
class UserAPIController extends Controller
{
    use ApiResponse;
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

        try {
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

            return $this->formatSuccessResponse(
                data: $users
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Show Users');
        }
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
        try {
            // Check if user has permission to create a new user
            $this->authorize('create', self::USER);

            $user = $this->userRepository->createNewUser($request->all());

            return $this->formatSuccessResponse(
                message: 'User created successfully. A password reset email has been sent. Please advice the user to reset their password.',
                data: [
                    'user' => $user
                ],
                statusCode: 201
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Create User');
        }
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
    public function show($id, Request $request): JsonResponse
    {
        try {
            // Get user
            $user = $this->userRepository->find($id);

            // Check if user exists
            if (!$user) {
                return $this->formatErrorResponse(
                    code: 'USER_NOT_FOUND',
                    message: self::USER_NOT_FOUND,
                    statusCode: 404
                );
            }

            // Check if user has permission to view the user
            $this->authorize('view', $user);

            // Load related data
            $user->load('role');

            return $this->formatSuccessResponse(
                data: $user
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Get User Details');
        }
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

        try {
            // Get user
            $user = $this->userRepository->find($id);

            // Check if user exists
            if (!$user) {
                return $this->formatErrorResponse(
                    code: 'USER_NOT_FOUND',
                    message: self::USER_NOT_FOUND,
                    statusCode: 404
                );
            }

            // Check if user has permission to update the user
            $this->authorize('update', $user);

            // Prepare update data
            $data = $request->only(['name', 'email', 'phone', 'role_id']);

            // Update user
            $this->userRepository->update($data, $user->id);

            return $this->formatSuccessResponse(
                message: "User updated successfully",
                data: [
                    'user' => $this->userRepository->find($user->id)
                ]
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Update User');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(UpdateUserRequest $request, ?int $id = null): JsonResponse
    {
        try {
            // Get user
            $user = $id ? $this->userRepository->find($id) : Auth::user();

            // Check if user exists
            if (!$user) {
                return $this->formatErrorResponse(
                    code: 'USER_NOT_FOUND',
                    message: self::USER_NOT_FOUND,
                    statusCode: 404
                );
            }

            // Check if user has permission to update the user
            $this->authorize('update', $user);

            // Prepare update data
            $data = $request->only(['name', 'email', 'phone']);

            // Update user
            $this->userRepository->update($data, $user->id);

            return $this->formatSuccessResponse(
                message: "User updated successfully",
                data: [
                    'user' => $this->userRepository->find($user->id)
                ]
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Update Profile');
        }
    }

    /**
     * Update the password of the specified user.
     */
    public function updatePassword(UpdateUserPasswordRequest $request): JsonResponse
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Check if user is authenticated
            if (!$user) {
                return $this->formatErrorResponse(
                    code: 'USER_NOT_AUTHENTICATED',
                    message: self::USER_NOT_AUTHENTICATED,
                    statusCode: 404
                );
            }

            // Check if user has permission to update the user
            $this->authorize('updatePassword', $user);

            // Update user password
            $this->userRepository->updateUserPassword($user->id, $request->password);

            return $this->formatSuccessResponse(
                message: "Password updated successfully"
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Update Password');
        }
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
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Get user to delete
            $userToDelete = $this->userRepository->find($id);

            if (!$userToDelete) {
                return $this->formatErrorResponse(
                    code: 'USER_NOT_FOUND',
                    message: self::USER_NOT_FOUND,
                    statusCode: 404
                );
            }

            // Check if user has permission to delete the user
            $this->authorize('delete', $userToDelete);

            // Delete user
            $result = $this->userRepository->destroyUser((int)$id, $user->id);

            return $this->formatSuccessResponse(
                message: $result['message'],
                statusCode: $result['status']
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Delete User');
        }
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
        try {
            // Get authenticated user
            $user = Auth::user();

            // Check if user has permission to bulk delete users
            $this->authorize('bulkDelete', self::USER);

            // Get validated data
            $ids = $request->validated('ids');

            // Delete multiple users
            $result = $this->userRepository->bulkDestroy($ids, $user->id);

            return $this->formatSuccessResponse(
                message: $result['deleted'] . ' users deleted successfully',
                data: [
                    'deleted' => $result['deleted'],
                    'failed' => $result['failed'],
                    'total_attempted' => $result['attempted'],
                    'self_delete_attempt' => $result['self_delete_attempt']
                        ? 'Self-deletion was attempted and skipped'
                        : null
                ]
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Delete Multiple Users');
        }
    }

    /**
     * Upload or update user avatar.
     */
    public function uploadAvatar(UpdateUserAvatarRequest $request): JsonResponse
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            $response = $this->formatErrorResponse(
                code: 'UNKNOWN_ERROR_ON_AVATAR_UPLOAD',
                message: "An unknown error occurred while uploading the avatar.",
                statusCode: 500
            );

            // Check if user is authenticated
            if (!$user) {
                $response = $this->formatErrorResponse(
                    code: 'USER_NOT_AUTHENTICATED',
                    message: self::USER_NOT_AUTHENTICATED,
                    statusCode: 401
                );
            } else {
                // Check if user has permission to update their avatar
                $this->authorize('update', $user);

                // Upload avatar
                /** @var UploadedFile $avatarFile */
                $avatarFile = $request->file('avatar');
                if (!$avatarFile || !($avatarFile instanceof UploadedFile)) {
                    $response = $this->formatErrorResponse(
                        code: 'AVATAR_FILE_REQUIRED',
                        message: "Avatar file is required",
                        statusCode: 400
                    );
                } else {
                    $avatar = $this->userRepository->updateAvatar($user->id, $avatarFile);

                    $response = $this->formatSuccessResponse(
                        message: $avatar['message'],
                        statusCode: $avatar['status'],
                        data: [
                            'avatar_url' =>  $avatar['avatar_url'] ?? null
                        ]
                    );
                }
            }

            return $response;
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Upload Avatar');
        }
    }

    /**
     * Delete user avatar.
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Check if user is authenticated
            if (!$user) {
                return $this->formatErrorResponse(
                    code: 'USER_NOT_AUTHENTICATED',
                    message: self::USER_NOT_AUTHENTICATED,
                    statusCode: 401
                );
            }

            // Check if user has permission to update their avatar
            $this->authorize('update', $user);

            // Delete avatar
            $result = $this->userRepository->deleteAvatar($user->id);


            return $this->formatSuccessResponse(
                message: $result['message']
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Delete Avatar');
        }
    }
}
