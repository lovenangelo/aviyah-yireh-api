<?php

namespace App\Http\Controllers\API\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\BulkDestroyUsersRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserAvatarRequest;
use App\Http\Resources\CustomPaginatedCollection;
use App\Traits\ApiResponse;
use Illuminate\Http\UploadedFile;

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

    public function index(Request $request): JsonResponse
    {

        $perPage = $request->query('per_page', null);

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
                    ->getFilter($filters, $perPage);
            } else {
                $users = $this->userRepository->getAll($perPage);
            }

            $response = new CustomPaginatedCollection($users, $request->query('include_links', false));

            return $this->formatSuccessResponse(
                data: $response,
                message: 'Users retrieved successfully.'
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Show Users');
        }
    }

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

    public function update(UpdateUserRequest $request, $id): JsonResponse
    {

        try {
            // Get user
            $user = $this->userRepository->find($id);

            // Check if user exists
            if (!$user) {
                return $this->formatErrorResponse(
                    code: "USER_NOT_FOUND",
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

    public function updateProfile(UpdateUserRequest $request, ?int $id = null): JsonResponse
    {
        try {
            // Get user
            $user = $id ? $this->userRepository->find($id) : Auth::user();

            // Check if user exists
            if (!$user) {
                return $this->formatErrorResponse(
                    code: "USER_NOT_FOUND",
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

    public function uploadAvatar(UpdateUserAvatarRequest $request): JsonResponse
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

    public function deleteAvatar(Request $request): JsonResponse
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Check if user is authenticated
            if (!$user) {
                return $this->formatErrorResponse(
                    code: "USER_NOT_AUTHENTICATED",
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

    public function me(Request $request): JsonResponse
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

            // Load related data
            $user->load('role');

            return $this->formatSuccessResponse(
                data: $user
            );
        } catch (\Throwable $th) {
            return $this->handleApiException($th, $request, 'Get Authenticated User');
        }
    }
}
