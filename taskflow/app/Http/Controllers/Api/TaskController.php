<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Task;
use App\Models\User;
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
            'search' => 'nullable|string|max:255',
        ]);

        $tasks = $request->user()->tasks()
            ->withCount('comments')
            ->status($request->status)
            ->priority($request->priority)
            ->search($request->search)
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

    public function projects(Request $request)
    {
        return response()->json(
            $request->user()->tasks()
                ->selectRaw('project, count(*) as count')
                ->groupBy('project')
                ->orderBy('project')
                ->get()
                ->map(fn ($project) => [
                    'name' => $project->project ?: 'Unassigned',
                    'count' => $project->count,
                ])
        );
    }

    public function users()
    {
        return UserResource::collection(User::orderBy('name')->get());
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create($request->validated());
        return new TaskResource($task->loadCount('comments'));
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->load(['comments.user'])->loadCount('comments');
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->update($request->validated());
        return new TaskResource($task->loadCount('comments'));
    }

    public function storeComment(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return new TaskResource($task->load(['comments.user'])->loadCount('comments'));
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
