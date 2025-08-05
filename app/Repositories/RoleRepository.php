<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RoleRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'description'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Role::class;
    }

    private function baseQuery(): Builder
    {
        return $this->model->newQuery();
    }

    private function executeQuery(Builder $query, $perPage = null)
    {
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getFilter($filters, $perPage = null)
    {
        $query = $this->baseQuery()->filter($filters);
        return $this->executeQuery($query, $perPage);
    }

    /**
     * Get all roles with their associated users count.
     *
     * @param int|null $perPage Number of items per page for pagination
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll($perPage = null)
    {
        $query = $this->baseQuery()->withCount('users');
        return $this->executeQuery($query, $perPage);
    }

    /**
     * Get roles with users relationship loaded.
     *
     * @param int|null $perPage Number of items per page for pagination
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllWithUsers($perPage = null)
    {
        $query = $this->baseQuery()->with('users')->withCount('users');
        return $this->executeQuery($query, $perPage);
    }

    /**
     * Get roles that have users assigned.
     *
     * @param int|null $perPage Number of items per page for pagination
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRolesWithUsers($perPage = null)
    {
        $query = $this->baseQuery()->has('users')->withCount('users');
        return $this->executeQuery($query, $perPage);
    }

    /**
     * Get roles that have no users assigned.
     *
     * @param int|null $perPage Number of items per page for pagination
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getEmptyRoles($perPage = null)
    {
        $query = $this->baseQuery()->doesntHave('users')->withCount('users');
        return $this->executeQuery($query, $perPage);
    }

    /**
     * Search roles by name or description.
     *
     * @param string $searchTerm
     * @param int|null $perPage Number of items per page for pagination
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchRoles($searchTerm, $perPage = null)
    {
        $query = $this->baseQuery()
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->withCount('users');

        return $this->executeQuery($query, $perPage);
    }

    /**
     * Create a new role with the given inputs.
     *
     * @param array $inputs Array containing role data with keys:
     *                     - name: string Role's name
     *                     - description: string Role's description
     * @return Role The newly created role model
     */
    public function createNewRole(array $inputs): Role
    {
        // Create role
        return $this->model->create([
            'name' => $inputs['name'],
            'description' => $inputs['description'],
        ]);
    }

    /**
     * Delete a role with validation.
     *
     * @param int $id ID of the role to delete
     * @return array Associative array with result and message
     */
    public function destroyRole(int $id): array
    {
        // Get role to delete
        $roleToDelete = $this->find($id);

        // Check if role exists
        if (!$roleToDelete) {
            return [
                'success' => false,
                'message' => 'Role not found.',
                'status' => 404
            ];
        }

        // Check if role has associated users
        if ($roleToDelete->users()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Role cannot be deleted because it has associated users.',
                'status' => 400
            ];
        }

        // Delete role
        $this->delete($id);

        return [
            'success' => true,
            'message' => 'Role deleted successfully',
            'status' => 200
        ];
    }

    /**
     * Delete multiple roles by IDs.
     *
     * @param array $ids Array of role IDs to delete
     * @return array Associative array with results
     */
    public function bulkDestroy(array $ids): array
    {
        $result = [
            'deleted' => 0,
            'failed' => 0,
            'attempted' => count($ids),
            'has_users' => [],
            'failed_details' => []
        ];

        foreach ($ids as $id) {
            $role = $this->find($id);

            if (!$role) {
                $result['failed']++;
                $result['failed_details'][] = [
                    'id' => $id,
                    'reason' => 'Role not found'
                ];
                continue;
            }

            // Skip if role has associated users
            if ($role->users()->count() > 0) {
                $result['failed']++;
                $result['has_users'][] = [
                    'id' => $role->id,
                    'name' => $role->name,
                    'users_count' => $role->users()->count()
                ];
                continue;
            }

            try {
                $this->delete($id);
                $result['deleted']++;
            } catch (\Exception $e) {
                $result['failed']++;
                $result['failed_details'][] = [
                    'id' => $id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return $result;
    }
}
