<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAccessSeeder extends Seeder
{
    public function run()
    {
        // Update existing admin user
        $admin = User::where('email', 'admin@greenhouse.com')->first();
        if ($admin) {
            $admin->update([
                'role' => 'admin',
                'can_access_admin' => true,
                'can_access_mobile' => true,
                'is_active' => true
            ]);
            $this->command->info('Admin user updated successfully.');
        } else {
            // Create admin if doesn't exist
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@greenhouse.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'can_access_admin' => true,
                'can_access_mobile' => true,
                'is_active' => true
            ]);
            $this->command->info('Admin user created successfully.');
        }

        // Update existing worker user
        $worker = User::where('email', 'worker@greenhouse.com')->first();
        if ($worker) {
            $worker->update([
                'role' => 'worker',
                'can_access_admin' => false,
                'can_access_mobile' => true,
                'is_active' => true
            ]);
            $this->command->info('Worker user updated successfully.');
        } else {
            // Create worker if doesn't exist
            User::create([
                'name' => 'Worker User',
                'email' => 'worker@greenhouse.com',
                'password' => Hash::make('password'),
                'role' => 'worker',
                'can_access_admin' => false,
                'can_access_mobile' => true,
                'is_active' => true
            ]);
            $this->command->info('Worker user created successfully.');
        }

        // Optionally create a supervisor user
        $supervisor = User::where('email', 'supervisor@greenhouse.com')->first();
        if (!$supervisor) {
            User::create([
                'name' => 'Supervisor User',
                'email' => 'supervisor@greenhouse.com',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
                'can_access_admin' => true,
                'can_access_mobile' => true,
                'is_active' => true
            ]);
            $this->command->info('Supervisor user created successfully.');
        }

        // Update any other users to have default permissions
        $otherUsers = User::whereNotIn('email', ['admin@greenhouse.com', 'worker@greenhouse.com', 'supervisor@greenhouse.com'])->get();
        foreach ($otherUsers as $user) {
            // Set default based on role
            $user->update([
                'can_access_admin' => in_array($user->role, ['admin', 'supervisor']),
                'can_access_mobile' => true,
                'is_active' => $user->is_active ?? true
            ]);
        }
        
        $this->command->info('All users updated with access permissions.');
    }
}