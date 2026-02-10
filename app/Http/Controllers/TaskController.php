<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['project', 'assignee'])
            ->where('assignee_id', auth()->id())
            ->latest()
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(in_array(auth()->user()->global_role, ['pi', 'manager']), 403);

        $projects = auth()->user()->projects()->with('users')->get();
        $users = User::all();

        return view('tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(in_array(auth()->user()->global_role, ['pi', 'manager']), 403);

        $projectIds = auth()->user()->projects()->pluck('projects.id');

        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'assignee_id' => 'nullable|exists:users,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date|after_or_equal:today',
            'status'      => 'required|in:open,in_progress,done',
            'priority'    => 'required|in:low,medium,high',
        ]);

        abort_unless($projectIds->contains((int) $data['project_id']), 403);

        if (!empty($data['assignee_id'])) {
            $isMember = Project::find($data['project_id'])
                ->users()
                ->whereKey($data['assignee_id'])
                ->exists();

            abort_unless($isMember, 403);
        }

        $task = Task::create($data);

        if ($task->assignee_id) {
            $task->assignee->notify(new TaskAssigned($task));
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load([
            'project',
            'assignee',
            'milestone',
            'tags',
            'attachments',
            'comments.user',
        ]);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        abort_unless(in_array(auth()->user()->global_role, ['pi', 'manager']), 403);

        $projectIds = auth()->user()->projects()->pluck('projects.id');
        abort_unless($projectIds->contains((int) $task->project_id), 403);

        $projects = auth()->user()->projects()->get();
        $users = User::all();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        abort_unless(in_array(auth()->user()->global_role, ['pi', 'manager']), 403);

        $projectIds = auth()->user()->projects()->pluck('projects.id');
        abort_unless($projectIds->contains((int) $task->project_id), 403);

        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'assignee_id' => 'nullable|exists:users,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:open,in_progress,done',
            'priority'    => 'required|in:low,medium,high',
        ]);

        abort_unless($projectIds->contains((int) $data['project_id']), 403);

        $oldAssignee = $task->assignee_id;

        $task->update($data);

        if ($task->assignee_id && $task->assignee_id !== $oldAssignee) {
            $task->assignee->notify(new TaskAssigned($task));
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        abort_unless(auth()->user()->global_role === 'pi', 403);

        $projectIds = auth()->user()->projects()->pluck('projects.id');
        abort_unless($projectIds->contains((int) $task->project_id), 403);
        
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task eliminato.');
    }
}