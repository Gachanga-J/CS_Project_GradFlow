<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Austin',
            'last_name' => "Ng'ang'a",
            'email' => User::generateEmail('Austin', "Ng'ang'a", 'adm'),
            'password' => 'password',
            'role' => 'admin',
            'department_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}