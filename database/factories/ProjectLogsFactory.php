<?php

namespace Database\Factories;

use App\Enums\TagEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectLogs>
 */
class ProjectLogsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'start_time' => $startTime = $this->faker->dateTimeBetween('now', '+1 year'),
            'end_time' => $startTime,
            'description' => $this->faker->sentence(),
            'duration' => function (array $attributes) {
                return $attributes['end_time']->getTimestamp() - $attributes['start_time']->getTimestamp();
            },
            'tag' => $this->faker->randomElement(TagEnum::cases()),
        ];
    }
}
