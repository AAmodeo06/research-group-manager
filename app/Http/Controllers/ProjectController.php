<?php

//Realizzato da: Cosimo Mandrillo

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Attachment;

class ProjectController extends Controller
{
    private function authorizeProjectPi(Project $project): void
    {
        $isPi = $project->users()
            ->where('users.id', auth()->id())
            ->where('project_user.role', 'pi')
            ->exists();

        abort_unless($isPi, 403);
    }

    /**
     * Blocca l’accesso se il progetto non appartiene
     * al gruppo dell’utente autenticato
     */
    private function abortIfProjectNotInMyGroup(Project $project): void
    {
        abort_unless(
            auth()->check() && $project->group_id === auth()->user()->group_id,
            403
        );
    }

    public function index()
    {
        Log::info('Pagina progetti visualizzata', [
            'user_id' => auth()->id()
        ]);

        $user = auth()->user();

        $query = Project::where('group_id', $user->group_id)
            ->with(['milestones', 'tags', 'publications', 'users', 'attachments']);

        // Se non è PI, vede solo i progetti del gruppo a cui partecipa
        if ($user->global_role !== 'pi') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $projects = $query->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        abort_unless(auth()->user()->global_role === 'pi', 403);
        abort_unless(auth()->user()->group_id !== null, 403);

        return view('projects.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->global_role === 'pi', 403);
        abort_unless(auth()->user()->group_id !== null, 403);

        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'code' => 'nullable|string|max:100',
            'funder' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:open,in_progress,completed',
            'description' => 'nullable|string',
            'file' => 'nullable|mimes:pdf|max:2048',
        ]);

        // Se il progetto è completato deve avere una data di fine
        if ($validated['status'] === 'completed' && empty($validated['end_date'])) {
            return back()
                ->withErrors([
                    'end_date' => 'Un progetto completato deve avere una data di fine.'
                ])
                ->withInput();
        }

        // Il progetto nasce nel gruppo del PI
        $validated['group_id'] = auth()->user()->group_id;

        $project = Project::create($validated);

        // Il PI creatore viene automaticamente associato
        $project->users()->attach(auth()->id(), [
            'role' => 'pi',
            'effort' => null,
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('projects', 'public');
            $originalName = $uploadedFile->getClientOriginalName();

            $nextVersion = ($project->attachments()
                ->where('type', 'project_file')
                ->max('version') ?? 0) + 1;

            $project->attachments()->create([
                'path' => $path,
                'title' => $originalName,
                'uploaded_by' => auth()->id(),
                'type' => 'project_file',
                'version' => $nextVersion,
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Progetto creato con successo.');
    }

    public function show(Project $project)
    {
        abort_unless(auth()->check(), 403);
        $this->abortIfProjectNotInMyGroup($project);

        $project->load([
            'users',
            'milestones',
            'publications',
            'tags',
            'attachments',
            'tasks.assignee'
        ]);

        $user = auth()->user();

        // Se non PI, deve essere membro del progetto
        if (auth()->user()->global_role !== 'pi') {
            $isMember = $project->users()
                ->where('users.id', auth()->id())
                ->exists();

            abort_unless($isMember, 403);
        }

        $canViewProjectTasks = $project->users()
            ->where('users.id', $user->id)
            ->whereIn('project_user.role', ['pi', 'manager'])
            ->exists();

        return view('projects.show', compact('project', 'canViewProjectTasks'));
    }

    public function edit(Project $project)
    {
        $this->abortIfProjectNotInMyGroup($project);

        $this->authorizeProjectPi($project);

        $project->load(['milestones', 'publications', 'users', 'attachments']);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->abortIfProjectNotInMyGroup($project);

        $this->authorizeProjectPi($project);

        $data = $request->validate([
            'title' => 'required|min:3|max:255',
            'code' => 'nullable|string|max:100',
            'funder' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:open,in_progress,completed',
            'description' => 'nullable|string',
            'file' => 'nullable|mimes:pdf|max:2048',
        ]);

        // Se il progetto è completato deve avere una data di fine
        if ($data['status'] === 'completed' && empty($data['end_date'])) {
            return back()
                ->withErrors([
                    'end_date' => 'Un progetto completato deve avere una data di fine.'
                ])
                ->withInput();
        }

        unset($data['group_id']); // il gruppo non si cambia

        $project->update($data);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('projects', 'public');
            $originalName = $uploadedFile->getClientOriginalName();

            $nextVersion = ($project->attachments()
                ->where('type', 'project_file')
                ->max('version') ?? 0) + 1;

            $project->attachments()->create([
                'path' => $path,
                'title' => $originalName,
                'uploaded_by' => auth()->id(),
                'type' => 'project_file',
                'version' => $nextVersion,
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Progetto aggiornato.');
    }

    public function destroy(Project $project)
    {
        $this->abortIfProjectNotInMyGroup($project);

        $this->authorizeProjectPi($project);

        foreach ($project->attachments()->where('type', 'project_file')->get() as $attachment) {
            Storage::disk('public')->delete($attachment->path);
            $attachment->delete();
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Progetto rimosso.');
    }

    public function members(Project $project)
    {
        $this->abortIfProjectNotInMyGroup($project);

        $this->authorizeProjectPi($project);

        // SOLO utenti del gruppo
        $users = User::where('group_id', $project->group_id)->get();
        $members = $project->users()->get();

        return view('projects.members', compact('project', 'users', 'members'));
    }

    public function storeMember(Request $request, Project $project)
    {
        $this->abortIfProjectNotInMyGroup($project);

        $this->authorizeProjectPi($project);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:manager,researcher,collaborator',
            'effort' => 'nullable|integer|min:0|max:100',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // L’utente deve appartenere allo stesso gruppo
        if ($user->group_id !== auth()->user()->group_id) {
            return back()->withErrors([
                'user_id' => 'Puoi assegnare al progetto solo utenti del tuo gruppo di ricerca.'
            ]);
        }

        $existingMember = $project->users()
            ->where('users.id', $validated['user_id'])
            ->first();

        if ($existingMember) {
            if ($existingMember->pivot->role === 'pi') {
                return back()->withErrors([
                    'user_id' => 'Il PI del progetto non può essere modificato.'
                ]);
            }

            $project->users()->updateExistingPivot(
                $validated['user_id'],
                [
                    'role' => $validated['role'],
                    'effort' => $validated['effort'] ?? null,
                ]
            );

            return back()->with('success', 'Ruolo del membro aggiornato.');
        }

        $project->users()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'effort' => $validated['effort'] ?? null,
        ]);

        return back()->with('success', 'Membro assegnato correttamente.');
    }

    public function destroyMember(Project $project, User $user)
    {
        $this->abortIfProjectNotInMyGroup($project);

        $this->authorizeProjectPi($project);

        abort_unless($user->group_id === auth()->user()->group_id, 403);

        $member = $project->users()
            ->where('users.id', $user->id)
            ->firstOrFail();

        if ($member->pivot->role === 'pi') {
            return back()->withErrors([
                'member' => 'Il Principal Investigator non può essere rimosso dal progetto.'
            ]);
        }

        $project->users()->detach($user->id);

        return back()->with('success', 'Membro rimosso correttamente.');
    }
}
