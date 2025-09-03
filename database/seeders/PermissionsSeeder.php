<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and their actions based on your screenshot
        $modules = [
            'user-management' => ['save', 'edit', 'delete', 'export'],
            'company-setup' => ['save', 'edit', 'delete', 'export'],
            'tax-vat-settings' => ['save', 'edit', 'delete', 'export'],
            'item-categories' => ['save', 'edit', 'delete', 'export'],
            'service-categories' => ['save', 'edit', 'delete', 'export'],
            'labor-categories' => ['save', 'edit', 'delete', 'export'],
            'item-setup' => ['save', 'edit', 'delete', 'export'],
            'service-setup' => ['save', 'edit', 'delete', 'export'],
            'company-vehicle-setup' => ['save', 'edit', 'delete', 'export'],
            'company-driver-setup' => ['save', 'edit', 'delete', 'export'],
            'job-type-setup' => ['save', 'edit', 'delete', 'export'],
            'location-setup' => ['save', 'edit', 'delete', 'export'],
            'employee-setup' => ['save', 'edit', 'delete', 'export'],
            'team-setup' => ['save', 'edit', 'delete', 'export'],
            'advertising' => ['save', 'edit', 'delete', 'export'],
            'client-management' => ['save', 'edit', 'delete', 'export'],
            'job-order-management' => ['save', 'edit', 'delete', 'export'],
            'invoice-and-billing' => ['save', 'edit', 'delete', 'export'],
            'reports' => ['save', 'edit', 'delete', 'export'],
            'disbursement' => ['save', 'edit', 'delete', 'export'],
        ];

        // Create permissions
        $permissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
                $permissions[] = $permission;

                $this->command->info("Created permission: {$permissionName}");
            }
        }

        // Create Super Admin role with all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
            'description' => 'Has full access to all system features and permissions',
        ]);

        $superAdminRole->syncPermissions($permissions);
        $this->command->info('Created Super Admin role with all permissions');

        // Create some example roles
        $this->createExampleRoles();
    }

    /**
     * Create some example roles with specific permissions
     */
    private function createExampleRoles()
    {
        // Manager role - has most permissions except system critical ones
        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web',
            'description' => 'Can manage most operations except system settings',
        ]);

        $managerPermissions = [
            'user-management.save',
            'user-management.edit',
            'user-management.export',
            'client-management.save',
            'client-management.edit',
            'client-management.delete',
            'client-management.export',
            'job-order-management.save',
            'job-order-management.edit',
            'job-order-management.delete',
            'job-order-management.export',
            'invoice-and-billing.save',
            'invoice-and-billing.edit',
            'invoice-and-billing.export',
            'reports.save',
            'reports.edit',
            'reports.export',
        ];

        $managerRole->syncPermissions($managerPermissions);
        $this->command->info('Created Manager role');

        // Employee role - limited permissions
        $employeeRole = Role::firstOrCreate([
            'name' => 'Employee',
            'guard_name' => 'web',
            'description' => 'Basic employee with limited access',
        ]);

        $employeePermissions = [
            'client-management.save',
            'client-management.edit',
            'client-management.export',
            'job-order-management.save',
            'job-order-management.edit',
            'job-order-management.export',
            'reports.export',
        ];

        $employeeRole->syncPermissions($employeePermissions);
        $this->command->info('Created Employee role');

        // Accountant role - finance related permissions
        $accountantRole = Role::firstOrCreate([
            'name' => 'Accountant',
            'guard_name' => 'web',
            'description' => 'Handles financial operations and reporting',
        ]);

        $accountantPermissions = [
            'invoice-and-billing.save',
            'invoice-and-billing.edit',
            'invoice-and-billing.delete',
            'invoice-and-billing.export',
            'disbursement.save',
            'disbursement.edit',
            'disbursement.delete',
            'disbursement.export',
            'tax-vat-settings.save',
            'tax-vat-settings.edit',
            'reports.save',
            'reports.edit',
            'reports.export',
        ];

        $accountantRole->syncPermissions($accountantPermissions);
        $this->command->info('Created Accountant role');

        // Viewer role - read-only access
        $viewerRole = Role::firstOrCreate([
            'name' => 'Viewer',
            'guard_name' => 'web',
            'description' => 'Read-only access to most modules',
        ]);

        $viewerPermissions = [
            'client-management.export',
            'job-order-management.export',
            'invoice-and-billing.export',
            'reports.export',
        ];

        $viewerRole->syncPermissions($viewerPermissions);
        $this->command->info('Created Viewer role');
    }
}
