<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskScheduler;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class TaskSchedulerTest extends TestCase
{
    public function test_sorts_by_overdue_before_priority(): void
    {
        $user = new User();
        $scheduler = new TaskScheduler();

        $taskA = $this->makeTask(1, 5, 'todo', '2026-01-11', 2, '2026-01-12');
        $taskB = $this->makeTask(2, 3, 'todo', '2026-01-01', 2, '2026-01-02');

        $user->setRelation('assignedTasks', new EloquentCollection([$taskA, $taskB]));

        $result = $scheduler->getNextTasks($user, Carbon::parse('2026-01-15'));

        $this->assertSame([2, 1], $result->pluck('id')->all());
    }

    public function test_priority_sorting_when_not_overdue(): void
    {
        $user = new User();
        $scheduler = new TaskScheduler();

        $taskA = $this->makeTask(1, 2, 'todo', '2026-01-10', 2, '2026-01-11');
        $taskB = $this->makeTask(2, 5, 'todo', '2026-01-10', 2, '2026-01-12');

        $user->setRelation('assignedTasks', new EloquentCollection([$taskA, $taskB]));

        $result = $scheduler->getNextTasks($user, Carbon::parse('2026-01-12'));

        $this->assertSame([2, 1], $result->pluck('id')->all());
    }

    public function test_due_today_tie_uses_project_importance(): void
    {
        $user = new User();
        $scheduler = new TaskScheduler();

        $today = Carbon::parse('2026-01-12');
        $taskA = $this->makeTask(1, 4, 'todo', '2026-01-10', 2, '2026-01-11', $today->toDateString());
        $taskB = $this->makeTask(2, 4, 'todo', '2026-01-10', 8, '2026-01-11', $today->toDateString());

        $user->setRelation('assignedTasks', new EloquentCollection([$taskA, $taskB]));

        $result = $scheduler->getNextTasks($user, $today);

        $this->assertSame([2, 1], $result->pluck('id')->all());
    }

    private function makeTask(
        int $id,
        int $priority,
        string $status,
        string $createdAt,
        int $projectTaskCount,
        string $projectCreatedAt,
        ?string $dueDate = null
    ): Task {
        $project = new Project([
            'id' => $id + 100,
            'name' => 'Project ' . $id,
        ]);
        $project->tasks_count = $projectTaskCount;
        $project->created_at = Carbon::parse($projectCreatedAt);

        $task = new Task([
            'id' => $id,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $dueDate,
        ]);

        $task->created_at = Carbon::parse($createdAt);
        $task->setRelation('project', $project);

        return $task;
    }
}
