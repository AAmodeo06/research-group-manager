<?php

//Realizzato da: Cosimo Mandrillo

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Http\Resources\PublicationResource;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return Project::whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with([
                'users:id,name',
                'publications:id,title'
            ])
            ->orderBy('title')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|string',
        ]);

        $project = Project::create($data);

        // Associa automaticamente il PI creatore
        $project->users()->attach(
            $request->user()->id,
            ['role' => 'pi']
        );

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        $user = $request->user();

        abort_unless(
            $project->users()->where('users.id', $user->id)->exists(),
            403
        );

        return $project->load([
            'users:id,name',
            'milestones',
            'tasks',
            'publications:id,title'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = $request->user();

        abort_unless(
            $project->users()
                ->where('users.id', $user->id)
                ->where('project_user.role', 'pi')
                ->exists(),
            403
        );

        $data = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|string',
        ]);

        $project->update($data);

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $user = $request->user();

        abort_unless(
            $project->users()
                ->where('users.id', $user->id)
                ->where('project_user.role', 'pi')
                ->exists(),
            403
        );

        $project->delete();

        return response()->json(null, 204);
    }
}
