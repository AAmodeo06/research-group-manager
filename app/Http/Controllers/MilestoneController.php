<?php

//Realizzato da: Cosimo Mandrillo

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    private function authorizeProjectManagement(Project $project)
    {
        $isAuthorized = $project->users()
            ->where('users.id', auth()->id())
            ->whereIn('project_user.role', ['pi', 'manager'])
            ->exists();

        abort_unless($isAuthorized, 403);
    }

    public function index(Project $project)
    {
        return view('milestones.index', compact('project'));
    }

    public function create(Project $project)
    {
        $this->authorizeProjectManagement($project);

        return view('milestones.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProjectManagement($project);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'status' => 'required|in:planned,in_progress,completed',
        ]);

        $project->milestones()->create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone creata.');
    }

    public function edit(Project $project, Milestone $milestone)
    {
        $this->authorizeProjectManagement($project);

        abort_unless($milestone->project_id === $project->id, 403);

        return view('milestones.edit', compact('project', 'milestone'));
    }

    public function update(Request $request, Project $project, Milestone $milestone)
    {
        $this->authorizeProjectManagement($project);

        abort_unless($milestone->project_id === $project->id, 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'status' => 'required|in:planned,in_progress,completed',
        ]);

        $milestone->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone aggiornata.');
    }

    public function destroy(Project $project, Milestone $milestone)
    {
        $this->authorizeProjectManagement($project);

        abort_unless($milestone->project_id === $project->id, 403);

        $milestone->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone rimossa.');
    }
}
