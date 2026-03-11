<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'owner_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    public function index(): JsonResponse
    {
        $projects = Project::query()
            ->withCount([
                'tasks as total_tasks',
                'tasks as open_tasks' => fn ($q) => $q->whereIn('status', ['todo', 'in_progress']),
                'tasks as completed_tasks' => fn ($q) => $q->where('status', 'done'),
            ])
            ->with(['tasks' => fn ($q) => $q->orderByDesc('priority')->limit(1)])
            ->get()
            ->map(function (Project $project) {
                $highestPriority = $project->tasks->first();

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'owner_id' => $project->owner_id,
                    'total_tasks' => $project->total_tasks,
                    'open_tasks' => $project->open_tasks,
                    'completed_tasks' => $project->completed_tasks,
                    'highest_priority_task' => $highestPriority ? [
                        'id' => $highestPriority->id,
                        'title' => $highestPriority->title,
                        'priority' => $highestPriority->priority,
                        'status' => $highestPriority->status,
                    ] : null,
                ];
            });

        return response()->json($projects);
    }
}
