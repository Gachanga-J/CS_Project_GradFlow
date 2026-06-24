<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->unique()->firstName();
        $lastName = fake()->unique()->lastName();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => User::generateEmail($firstName, $lastName, 'stu'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('COMMON_USER_PASSWORD', '@GradFlow123')), // password
            'role' => 'student',
            'department_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * Configure the model factory to produce a supervisor.
     */
    public function supervisor(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'supervisor',
                'email' => User::generateEmail($attributes['first_name'], $attributes['last_name'], 'sup'),
            ];
        });
    }
}