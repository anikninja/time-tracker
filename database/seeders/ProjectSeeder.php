<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectLogs;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use the factory to create projects
        Project::factory()->count(5)->create()->each(function ($project) {
            ProjectLogs::factory()->count(5)->create([
                'project_id' => $project->id,
            ]);
        });

    }
}
