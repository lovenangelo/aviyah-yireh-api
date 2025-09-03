<?php

namespace App\Http\Controllers\API\V1\Permission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionAPIController extends Controller
{
    use ApiResponse;

    /**
     * Get all permissions grouped by modules
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $permissions = Permission::all();
            // Group permissions by module
            $groupedPermissions = $this->groupPermissionsByModule($permissions);

            return $this->formatSuccessResponse([
                'permissions' => $permissions,
                'grouped_permissions' => $groupedPermissions,
                'modules' => array_keys($groupedPermissions),
            ], 'Permissions retrieved successfully!', 200);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Permissions Fetching');
        }
    }

    /**
     * Get permissions matrix for frontend display
     */
    public function matrix(Request $request): JsonResponse
    {
        try {
            $permissions = Permission::all();
            $groupedPermissions = $this->groupPermissionsByModule($permissions);

            // Create matrix structure for frontend
            $matrix = [];
            $actions = ['save', 'edit', 'delete', 'export'];

            foreach ($groupedPermissions as $module => $modulePermissions) {
                $matrix[] = [
                    'module' => $module,
                    'permissions' => $this->buildModulePermissions($modulePermissions, $actions),
                ];
            }

            return $this->formatSuccessResponse([
                'matrix' => $matrix,
                'actions' => $actions,
            ], 'Permission matrix retrieved successfully!', 200);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, 'Permission Matrix Fetching');
        }
    }

    /**
     * Group permissions by module/category
     */
    private function groupPermissionsByModule($permissions): array
    {
        $modules = [];

        foreach ($permissions as $permission) {
            // Extract module name from permission name
            // e.g., 'user-management.save' -> 'User Management'
            $parts = explode('.', $permission->name);
            if (count($parts) >= 2) {
                $moduleName = str_replace('-', ' ', ucwords($parts[0], '-'));

                if (! isset($modules[$moduleName])) {
                    $modules[$moduleName] = [];
                }

                $modules[$moduleName][] = $permission;
            }
        }

        return $modules;
    }

    /**
     * Build module permissions structure for matrix
     */
    private function buildModulePermissions($modulePermissions, $actions): array
    {
        $permissions = [];

        foreach ($actions as $action) {
            $permission = collect($modulePermissions)->first(function ($perm) use ($action) {
                return str_ends_with($perm->name, '.'.$action);
            });

            $permissions[$action] = $permission ? $permission->name : null;
        }

        return $permissions;
    }
}
