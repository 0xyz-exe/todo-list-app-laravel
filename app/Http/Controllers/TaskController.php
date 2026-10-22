<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query()->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'due_date', 'priority', 'status'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'priority') {
                $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low') " . ($sortDir === 'asc' ? 'ASC' : 'DESC'));
            } else {
                $query->orderBy($sortBy, $sortDir);
            }
        }

        $tasks = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Task::where('user_id', auth()->id())->count(),
            'pending' => Task::where('user_id', auth()->id())->pending()->count(),
            'completed' => Task::where('user_id', auth()->id())->completed()->count(),
            'overdue' => Task::where('user_id', auth()->id())->overdue()->count(),
        ];

        return view('tasks.index', compact('tasks', 'stats'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request)
    {
        auth()->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function complete(Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Task marked as completed.');
    }

    public function uncomplete(Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        return back()->with('success', 'Task marked as pending.');
    }
}
