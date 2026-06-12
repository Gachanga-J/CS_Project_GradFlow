<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('name', 'Department of Computing')->first();

        User::factory()
            ->supervisor()
            ->count(3)
            ->create(['department_id' => $department->id])
            ->each(function (User $user, int $index) {
                Supervisor::create([
                    'user_id' => $user->id,
                    'staff_number' => 'STF' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'max_student_capacity' => 5,
                    'current_load' => 0,
                ]);
            });
    }
}