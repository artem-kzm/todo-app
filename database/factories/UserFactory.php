<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => Role::inRandomOrder()->first()->name
        ];
    }

    public function administrator(): static
    {
        return $this->state(function () {
            return [
                'role' => Role::ADMINISTRATOR_ROLE,
            ];
        });
    }

    public function userRole(): static
    {
        return $this->state(function () {
            return [
                'role' => Role::USER_ROLE,
            ];
        });
    }
}
