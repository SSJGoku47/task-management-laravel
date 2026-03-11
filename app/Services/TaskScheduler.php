<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TaskScheduler
{
    /**
     * @return Collection<int, Task>
     */
    public function getNextTasks(User $user, ?Carbon $today = null): Collection
    {
        $today ??= now()->startOfDay();

        $tasks = $user->relationLoaded('assignedTasks')
            ? $user->assignedTasks
            : $user->assignedTasks()->with('project')->get();

        return $tasks
            ->sort(function (Task $a, Task $b) use ($today) {
                $aOverdue = $a->isOverdue($today);
                $bOverdue = $b->isOverdue($today);

                if ($aOverdue !== $bOverdue) {
                    return $aOverdue ? -1 : 1;
                }

                if ($a->priority !== $b->priority) {
                    return $b->priority <=> $a->priority;
                }

                $aDueToday = $a->due_date?->isSameDay($today) ?? false;
                $bDueToday = $b->due_date?->isSameDay($today) ?? false;

                if ($aDueToday && $bDueToday) {
                    $aImportance = (int) ($a->project?->tasks_count ?? $a->project?->tasks()->count() ?? 0);
                    $bImportance = (int) ($b->project?->tasks_count ?? $b->project?->tasks()->count() ?? 0);

                    if ($aImportance !== $bImportance) {
                        return $bImportance <=> $aImportance;
                    }
                }

                return $a->created_at <=> $b->created_at;
            })
            ->values();
    }
}
