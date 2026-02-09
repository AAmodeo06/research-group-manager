<?php

//Realizzato da: Andrea Amodeo

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    private function authorizeProjectManagement(Project $project)
    {
        $isAuthorized = $project->users()
            ->where('users.id', auth()->id())
            ->whereIn('project_user.role', ['pi', 'manager'])
            ->exists();

        abort_unless($isAuthorized, 403);
    }

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
    public function create(Project $project)
    {
        $this->authorizeProjectManagement($project);

        $users = $project->users;

        return view('tasks.create', compact('project', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeProjectManagement($project);

        $data = $request->validate([
            'milestone_id' => 'nullable|exists:milestones,id',
            'assignee_id' => 'nullable|exists:users,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date|after_or_equal:today',
            'status'      => 'required|in:open,in_progress,completed',
            'priority'    => 'required|in:low,medium,high',
        ]);

        //se è assegnato un responsabile, deve essere membro del progetto
        if (!empty($data['assignee_id'])) {
            abort_unless($project->users()->whereKey($data['assignee_id'])->exists(),403);
        }

        $task = $project->tasks()->create($data);

        if ($task->assignee_id) {
            $task->assignee->notify(new TaskAssigned($task));
        }

        return redirect()
            ->route('projects.show', $project)
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
    public function edit(Project $project, Task $task)
    {
        $this->authorizeProjectManagement($project);

        abort_unless($task->project_id === $project->id, 403);

        $users = $project->users;

        return view('tasks.edit', compact('task', 'project', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorizeProjectManagement($project);

        abort_unless($task->project_id === $project->id, 403);

        $data = $request->validate([
            'milestone_id' => 'nullable|exists:milestones,id',
            'assignee_id' => 'nullable|exists:users,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:open,in_progress,completed',
            'priority'    => 'required|in:low,medium,high',
        ]);

        // se è indicata una milestone, deve appartenere allo stesso progetto
        if (!empty($data['milestone_id'])) {
            abort_unless($project->milestones()->whereKey($data['milestone_id'])->exists(),403);
        }

        // se è indicato un assegnatario, deve essere membro del progetto
        if (!empty($data['assignee_id'])) {
            abort_unless($project->users()->whereKey($data['assignee_id'])->exists(),403);
        }

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
    public function destroy(Project $project, Task $task)
    {
        $this->authorizeProjectManagement($project);

        abort_unless($task->project_id === $project->id, 403);
        
        $task->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Task eliminato.');
    }
}
