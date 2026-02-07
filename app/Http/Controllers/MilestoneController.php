<?php

// Realizzato da Cosimo Mandrillo

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    private function authorizePi(Project $project)
    {
        abort_unless(
            auth()->user()->isPiOfProject($project),
            403
        );
    }

    public function index(Project $project)
    {
        return view('milestones.index', compact('project'));
    }

    public function create(Project $project)
    {
        $this->authorizePi($project);

        return view('milestones.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizePi($project);

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
        $this->authorizePi($project);

        return view('milestones.edit', compact('project', 'milestone'));
    }

    public function update(Request $request, Project $project, Milestone $milestone)
    {
        $this->authorizePi($project);

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
        $this->authorizePi($project);

        $milestone->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Milestone rimossa.');
    }
}
