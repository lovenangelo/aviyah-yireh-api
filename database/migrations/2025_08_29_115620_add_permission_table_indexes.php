<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionTableIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        // Add additional indexes for better performance
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->index('name', 'permissions_name_index');
            $table->index('guard_name', 'permissions_guard_name_index');
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->index('name', 'roles_name_index');
            $table->index('guard_name', 'roles_guard_name_index');
            $table->index('created_at', 'roles_created_at_index');
        });

        // Add indexes to pivot tables for better query performance
        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->index('model_type', 'model_has_permissions_model_type_index');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->index('model_type', 'model_has_roles_model_type_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropIndex('permissions_name_index');
            $table->dropIndex('permissions_guard_name_index');
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropIndex('roles_name_index');
            $table->dropIndex('roles_guard_name_index');
            $table->dropIndex('roles_created_at_index');
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_model_type_index');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_type_index');
        });
    }
}
