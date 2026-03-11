<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, int $projectId): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
            'priority' => ['required', 'integer', 'between:1,5'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['project_id'] = $projectId;

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    public function updateStatus(Request $request, int $taskId): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress,done'],
        ]);

        $task = Task::findOrFail($taskId);
        $task->update(['status' => $validated['status']]);

        return response()->json($task);
    }
}
