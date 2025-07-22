<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\BulkDestroyRolesRequest;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Endpoints for managing roles in the application",
 * )
 */
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

    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/v1/roles",
     *     summary="Get list of roles",
     *     tags={"Roles"},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of roles")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
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
                    ->getFilter($filters)
                    ->withCount('users')
                    ->paginate($filters['per_page'] ?? 10)
                    ->withQueryString();
            } else {
                $roles = $this->roleRepository->getAll();
            }

            return $this->formatSuccessResponse($roles, "Roles retrieved successfuly!", 200);
        } catch (AuthorizationException $e) {
            return $this->handleApiException($e, $request, 'Fetch Roles');
        }
    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     *     path="/api/v1/roles",
     *     summary="Create a new role",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Role created successfully")
     * )
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            // Check if user has permission to create a new role
            $this->authorize('create', self::ROLE);
            $role = $this->roleRepository->createNewRole($request->all());

            return $this->formatSuccessResponse($role, "Role created successfully.", 201, $request);
        } catch (ValidationException $e) {
            return $this->handleApiException($e, $request, 'Create New Role');
        } catch (AuthorizationException $e) {
            return $this->handleApiException($e, $request, 'Create New Role');
        }
    }

    /**
     * Display the specified resource.
     * @OA\Get(
     *     path="/api/v1/roles/{id}",
     *     summary="Get a role by ID",
     *     tags={"Roles"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Role details"),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function show($id): JsonResponse
    {
        // Get role
        $role = $this->roleRepository->find($id);

        // Check if role exists
        if (!$role) {
            return response()->json([
                'message' => self::ROLE_NOT_FOUND,
            ], 404);
        }

        // Check if user has permission to view the role
        $this->authorize('view', $role);

        // Load related data
        $role->loadCount('users');

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     * @OA\Put(
     *     path="/api/v1/roles/{id}",
     *     summary="Update a role",
     *     tags={"Roles"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role updated successfully"),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        // Get role
        $role = $this->roleRepository->find($id);

        // Check if role exists
        if (!$role) {
            return response()->json([
                'message' => self::ROLE_NOT_FOUND,
            ], 404);
        }

        // Check if user has permission to update the role
        $this->authorize('update', $role);

        // Prepare update data
        $data = $request->only(['name', 'description']);

        // Update role
        $this->roleRepository->update($data, $role->id);

        return response()->json([
            'message' => 'Role updated successfully.',
            'role' => $this->roleRepository->find($role->id),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @OA\Delete(
     *     path="/api/v1/roles/{id}",
     *     summary="Delete a role",
     *     tags={"Roles"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Role deleted successfully"),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function destroy($id): JsonResponse
    {
        // Get role to delete
        $roleToDelete = $this->roleRepository->find($id);

        if (!$roleToDelete) {
            return response()->json([
                'message' => self::ROLE_NOT_FOUND,
            ], 404);
        }

        // Check if user has permission to delete the role
        $this->authorize('delete', $roleToDelete);

        // Delete role
        $result = $this->roleRepository->destroyRole((int)$id);

        // Return appropriate response based on result
        return response()->json([
            'message' => $result['message']
        ], $result['status']);
    }

    /**
     * Remove multiple roles from storage.
     * @OA\Post(
     *     path="/api/v1/roles/bulk-destroy",
     *     summary="Bulk delete roles",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Roles deleted successfully")
     * )
     */
    public function bulkDestroy(BulkDestroyRolesRequest $request): JsonResponse
    {
        // Check if user has permission to bulk delete roles
        $this->authorize('bulkDelete', self::ROLE);

        // Get validated data
        $ids = $request->validated('ids');

        // Delete multiple roles
        $result = $this->roleRepository->bulkDestroy($ids);

        return response()->json([
            'message' => $result['deleted'] . ' roles deleted successfully',
            'details' => [
                'deleted' => $result['deleted'],
                'failed' => $result['failed'],
                'total_attempted' => $result['attempted'],
                'roles_with_users' => $result['has_users'],
            ]
        ]);
    }
}
