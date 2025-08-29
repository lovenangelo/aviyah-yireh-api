<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the role seeder first
        $this->call(RoleSeeder::class);

        // Get role IDs
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        $password = 'P@$$w0rd';

        // Create users with roles
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'suffix' => 'Sr.',
            'email' => 'johndoe@example.com',
            'password' => Hash::make($password),
            'role_id' => $adminRole->id,
        ]);

        User::factory()->create([
            'first_name' => 'Jane',
            'middle_name' => 'Taylor',
            'last_name' => 'Doe',
            'suffix' => null,
            'email' => 'janedoe@example.com',
            'password' => Hash::make($password),
            'role_id' => $adminRole->id,
        ]);

        User::factory()->create([
            'first_name' => 'Jack',
            'last_name' => 'Doe',
            'middle_name' => null,
            'suffix' => null,
            'email' => 'jackdoe@example.com',
            'password' => Hash::make($password),
            'role_id' => $userRole->id,
        ]);

        $this->call(CategorySeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(EventsSeeder::class);
        $this->call(TrainingMaterialSeeder::class);
    }
}
