<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            User::create(['name' => 'Alice', 'email' => 'alice@example.com']),
            User::create(['name' => 'Bob', 'email' => 'bob@example.com']),
            User::create(['name' => 'Carol', 'email' => 'carol@example.com']),
        ]);

        $projects = collect([
            Project::create(['name' => 'Platform Revamp', 'owner_id' => $users[0]->id]),
            Project::create(['name' => 'Mobile App', 'owner_id' => $users[1]->id]),
            Project::create(['name' => 'Marketing Site', 'owner_id' => $users[2]->id]),
        ]);

        for ($i = 1; $i <= 20; $i++) {
            Task::create([
                'title' => "Seeded Task {$i}",
                'description' => "Generated task {$i}",
                'status' => ['todo', 'in_progress', 'done'][$i % 3],
                'project_id' => $projects[$i % 3]->id,
                'assigned_to' => $users[$i % 3]->id,
                'priority' => ($i % 5) + 1,
                'due_date' => now()->addDays(($i % 6) - 2)->toDateString(),
                'created_at' => now()->subDays($i),
                'updated_at' => now()->subDays($i),
            ]);
        }
    }
}
