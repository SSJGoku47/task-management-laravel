<?php

namespace Tests\Unit;

use App\Helpers\TaskAlgorithm;
use PHPUnit\Framework\TestCase;

class TaskAlgorithmTest extends TestCase
{
    public function test_priority_sorting_descending(): void
    {
        $tasks = [
            ['id' => 1, 'priority' => 2, 'status' => 'todo', 'created_at' => '2026-01-01', 'project_importance' => 1],
            ['id' => 2, 'priority' => 5, 'status' => 'todo', 'created_at' => '2026-01-01', 'project_importance' => 1],
            ['id' => 3, 'priority' => 3, 'status' => 'todo', 'created_at' => '2026-01-01', 'project_importance' => 1],
        ];

        $result = TaskAlgorithm::topTasks($tasks, 3);

        $this->assertSame([2, 3, 1], array_column($result, 'id'));
    }

    public function test_overdue_tasks_first_even_with_lower_priority(): void
    {
        $tasks = [
            ['id' => 1, 'priority' => 5, 'status' => 'todo', 'created_at' => 'yesterday', 'project_importance' => 1],
            ['id' => 2, 'priority' => 3, 'status' => 'todo', 'created_at' => '10 days ago', 'project_importance' => 1],
        ];

        $result = TaskAlgorithm::topTasks($tasks, 2);

        $this->assertSame([2, 1], array_column($result, 'id'));
    }

    public function test_tie_breaking_by_project_importance(): void
    {
        $tasks = [
            ['id' => 1, 'priority' => 4, 'status' => 'todo', 'created_at' => 'today', 'project_importance' => 8],
            ['id' => 2, 'priority' => 4, 'status' => 'todo', 'created_at' => 'today', 'project_importance' => 14],
        ];

        $result = TaskAlgorithm::topTasks($tasks, 2);

        $this->assertSame([2, 1], array_column($result, 'id'));
    }

    public function test_in_progress_task_is_never_overdue(): void
    {
        $tasks = [
            ['id' => 1, 'priority' => 4, 'status' => 'in_progress', 'created_at' => '10 days ago', 'project_importance' => 10],
            ['id' => 2, 'priority' => 5, 'status' => 'todo', 'created_at' => 'yesterday', 'project_importance' => 1],
        ];

        $result = TaskAlgorithm::topTasks($tasks, 2);

        $this->assertSame([2, 1], array_column($result, 'id'));
    }

    public function test_returns_only_top_n_tasks(): void
    {
        $tasks = [
            ['id' => 1, 'priority' => 5, 'status' => 'todo', 'created_at' => 'today', 'project_importance' => 5],
            ['id' => 2, 'priority' => 4, 'status' => 'todo', 'created_at' => 'today', 'project_importance' => 4],
            ['id' => 3, 'priority' => 3, 'status' => 'todo', 'created_at' => 'today', 'project_importance' => 3],
        ];

        $result = TaskAlgorithm::topTasks($tasks, 2);

        $this->assertCount(2, $result);
        $this->assertSame([1, 2], array_column($result, 'id'));
    }
}
