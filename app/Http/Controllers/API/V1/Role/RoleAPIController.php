<?php

namespace App\Http\Controllers\API\V1\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\BulkDestroyRolesRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\CustomPaginatedCollection;
use App\Repositories\RoleRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleAPIController extends Controller
{
    use ApiResponse;

    private RoleRepository $roleRepository;

    private const ROLE = 'App\Models\Role';

    private const ROLE_NOT_FOUND = 'Role not found.';

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', null);
            // Check if user has permission to view all roles
            $this->authorize('viewAny', self::ROLE);

            $filters = $request->only([
                'page',
                'per_page',
                'sort',
                'search',
            ]);

            if ($filters) {
                $roles = $this->roleRepository
                    ->getFilter($filters, $perPage);
            } else {
                $roles = $this->roleRepository->getAll($perPage);
            }
            $response = new CustomPaginatedCollection($roles, $request->query('include_links', false));

            return $this->formatSuccessResponse($response, 'Roles retrieved successfuly!', 200);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Roles Fetching');
        }
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            // Check if user has permission to create a new role
            $this->authorize('create', self::ROLE);
            $role = $this->roleRepository->createNewRole($request->all());

            return $this->formatSuccessResponse($role, 'Role created successfully.', 201, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Role Creation');
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Get role
            $role = $this->roleRepository->find($id);

            // Check if role exists
            if (!$role) {
                return $this->formatErrorResponse('404', self::ROLE_NOT_FOUND, [], 404);
            }

            $user = $request->user();

            // Check if user has permission to view the role
            $this->authorize('view', $user);

            // Load related data
            $role->loadCount('users');

            return $this->formatSuccessResponse($role, 'Role with associated users retrieved successfuly.', 200);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Role retrieval');
        }
    }

    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        try {
            // Get role
            $role = $this->roleRepository->find($id);

            // Check if role exists
            if (! $role) {
                return $this->formatErrorResponse('404', self::ROLE_NOT_FOUND, [], 404);
            }

            // Check if user has permission to update the role
            $this->authorize('update', $role);

            // Update role with permissions
            $updatedRole = $this->roleRepository->updateRoleWithPermissions($request->validated(), $role->id);

            return $this->formatSuccessResponse($updatedRole, 'Role updated successfully.', 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Role update');
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Get role to delete
            $roleToDelete = $this->roleRepository->find($id);

            if (! $roleToDelete) {
                return $this->formatErrorResponse('404', self::ROLE_NOT_FOUND, [], 404);
            }

            // Check if user has permission to delete the role
            $this->authorize('delete', $roleToDelete);

            // Delete role
            $result = $this->roleRepository->destroyRole((int) $id);

            return $this->formatSuccessResponse(null, $result['message'], $result['status'], $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Role delete');
        }
    }

    public function bulkDestroy(BulkDestroyRolesRequest $request): JsonResponse
    {
        try {
            // Check if user has permission to bulk delete roles
            $this->authorize('bulkDelete', self::ROLE);

            // Get validated data
            $ids = $request->validated('ids');

            // Delete multiple roles
            $result = $this->roleRepository->bulkDestroy($ids);

            $message = $result['deleted'] . ' roles deleted successfully';
            $data = [
                'deleted' => $result['deleted'],
                'failed' => $result['failed'],
                'total_attempted' => $result['attempted'],
                'roles_with_users' => $result['has_users'],
            ];

            return $this->formatSuccessResponse($data, $message, 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Role delete many');
        }
    }
}
