<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('name', 'Department of Computing')->first();

        User::factory()
            ->count(10)
            ->create(['department_id' => $department->id])
            ->each(function (User $user, int $index) {
                Student::create([
                    'user_id' => $user->id,
                    'reg_number' => (string) (190000 + $index + 1),
                    'year_of_study' => fake()->numberBetween(1, 4),
                ]);
            });
    }
}