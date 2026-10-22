<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo user
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        // Create sample tasks
        \App\Models\Task::factory()->count(5)->pending()->create([
            'user_id' => $user->id,
        ]);

        \App\Models\Task::factory()->count(3)->completed()->create([
            'user_id' => $user->id,
        ]);

        // Create an overdue task
        \App\Models\Task::factory()->pending()->create([
            'user_id' => $user->id,
            'title' => 'Review overdue project documentation',
            'due_date' => now()->subDays(2),
            'priority' => 'high',
        ]);
    }
}
