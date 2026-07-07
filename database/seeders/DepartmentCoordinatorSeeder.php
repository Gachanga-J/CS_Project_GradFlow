<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentCoordinatorSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('name', 'Department of Computing')->first();

        User::create([
            'first_name' => 'Austin',
            'last_name' => "Ng'ang'a",
            'email' => User::generateEmail('Austin', "Ng'ang'a", 'dc'),
            'password' => 'password',
            'role' => 'department_coordinator',
            'department_id' => $department->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}