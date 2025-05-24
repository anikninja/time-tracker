<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => StatusEnum::PENDING,
            'deadline' => $this->faker->dateTimeBetween('now', '+1 year'),
            'client_id' => $this->faker->randomElement([3, 4, 5]), // Assuming you have clients with IDs 3, 4, and 5
        ];
    }
}
