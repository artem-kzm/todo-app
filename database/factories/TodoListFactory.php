<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TodoListFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'deleted_at' => null
        ];
    }

    public function deleted(): static
    {
        return $this->state(function () {
            return [
                'deleted_at' => now(),
            ];
        });
    }
}
