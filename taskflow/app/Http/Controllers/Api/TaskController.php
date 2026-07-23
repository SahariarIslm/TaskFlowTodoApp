<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:todo,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'sort' => 'nullable|in:due_date,priority,created_at,title',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $tasks = $request->user()->tasks()
            ->status($request->status)
            ->priority($request->priority)
            ->when($request->sort, fn ($q) => $q->orderBy($request->sort, $request->get('direction', 'asc')))
            ->when(!$request->sort, fn ($q) => $q->latest())
            ->paginate(10);

        return TaskResource::collection($tasks);
    }

    public function dashboardStats(Request $request)
    {
        $tasks = $request->user()->tasks();

        return response()->json([
            'total_tasks' => (clone $tasks)->count(),
            'todo' => (clone $tasks)->where('status', 'todo')->count(),
            'in_progress' => (clone $tasks)->where('status', 'in_progress')->count(),
            'completed' => (clone $tasks)->where('status', 'completed')->count(),
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create($request->validated());
        return new TaskResource($task);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        return new TaskResource($task);
    }

    public function update(StoreTaskRequest $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->update($request->validated());
        return new TaskResource($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully.']);
    }

    private function authorizeTask(Request $request, Task $task): void
    {
        abort_if($task->user_id !== $request->user()->id, 403, 'Unauthorized action.');
    }
}
