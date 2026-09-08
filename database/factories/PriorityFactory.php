<?php

namespace Database\Factories;

use App\Models\Priority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Priority>
 */
class PriorityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priority = fake()->randomElement([
            ['name' => 'düşük', 'level' => 1],
            ['name' => 'normal', 'level' => 2],
            ['name' => 'acil', 'level' => 3],
        ]);

        return [
            'name' => $priority['name'],
            'level' => $priority['level'],
        ];
    }
}
