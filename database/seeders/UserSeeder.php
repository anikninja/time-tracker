<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Anik Ninja',
            'email' => 'anik89bd@gmail.com',
        ])->assignRole( RolesEnum::Freelancer->value );

        User::factory()->create([
            'name' => 'Anik',
            'email' => 'anik00bd@gmail.com',
        ])->assignRole( RolesEnum::Freelancer->value );

        User::factory()
            ->count(3)
            ->sequence(fn ($sequence) => [
            'name' => 'Client ' . ($sequence->index + 1),
            'email' => 'client' . ($sequence->index + 1) . '@example.com',
            ])
            ->create()
            ->each(function ($user) {
            $user->assignRole( RolesEnum::Client->value );
            });
    }
}