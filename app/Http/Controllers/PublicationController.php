<?php

//Realizzato da: Luigi La Gioia

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Project;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $publications = Publication::whereHas('projects', function ($q) use ($user) {
                $q->where('group_id', $user->group_id);
            })
            ->with(['projects', 'authors.user'])
            ->latest()
            ->get();

        return view('publications.index', compact('publications'));
    }

    public function create()
    {
        $projects = Project::where('group_id', auth()->user()->group_id)->get();

        return view('publications.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'nullable|string|max:100',
            'venue'            => 'nullable|string|max:255',
            'doi'              => 'nullable|string|max:255|unique:publications,doi',
            'status'           => 'required|in:drafting,submitted,accepted,published',
            'target_deadline'  => 'nullable|date',
            'projects'         => 'required|array',
            'projects.*'       => 'exists:projects,id',
            'pdf'              => 'nullable|mimes:pdf|max:4096',
        ]);

        // SOLO dati della publication
        $publication = Publication::create([
            'title'           => $validated['title'],
            'type'            => $validated['type'] ?? null,
            'venue'           => $validated['venue'] ?? null,
            'doi'             => $validated['doi'] ?? null,
            'status'          => $validated['status'],
            'target_deadline' => $validated['target_deadline'] ?? null,
        ]);

        // collega i progetti
        $publication->projects()->sync($validated['projects']);

        // upload PDF come attachment
        if ($request->hasFile('pdf')) {
            $path = $request->file('pdf')->store('publications', 'public');

            Attachment::create([
                'attachable_type' => Publication::class,
                'attachable_id'   => $publication->id,
                'title'           => $publication->title,
                'path'            => $path,
                'uploaded_by'     => auth()->id(),
            ]);
        }

        return redirect()
            ->route('publications.index')
            ->with('success', 'Pubblicazione creata correttamente.');
    }

    public function show(Publication $publication)
    {
        $publication->load(['projects', 'attachments', 'authors.user']);

        $users = User::all();

        return view('publications.show', compact('publication', 'users'));
    }

    public function edit(Publication $publication)
    {
        $projects = Project::where('group_id', auth()->user()->group_id)->get();

        $publication->load('projects');

        return view('publications.edit', compact('publication', 'projects'));
    }

    public function update(Request $request, Publication $publication)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'nullable|string|max:100',
            'venue'            => 'nullable|string|max:255',
            'doi'              => 'nullable|string|max:255|unique:publications,doi,' . $publication->id,
            'status'           => 'required|in:drafting,submitted,accepted,published',
            'target_deadline'  => 'nullable|date',
            'projects'         => 'required|array',
            'projects.*'       => 'exists:projects,id',
            'pdf'              => 'nullable|mimes:pdf|max:4096',
        ]);

        $publication->update([
            'title'           => $validated['title'],
            'type'            => $validated['type'] ?? null,
            'venue'           => $validated['venue'] ?? null,
            'doi'             => $validated['doi'] ?? null,
            'status'          => $validated['status'],
            'target_deadline' => $validated['target_deadline'] ?? null,
        ]);

        $publication->projects()->sync($validated['projects']);

        if ($request->hasFile('pdf')) {
            foreach ($publication->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            $path = $request->file('pdf')->store('publications', 'public');

            Attachment::create([
                'attachable_type' => Publication::class,
                'attachable_id'   => $publication->id,
                'title'           => $publication->title,
                'path'            => $path,
                'uploaded_by'     => auth()->id(),
            ]);
        }

        return redirect()
            ->route('publications.show', $publication)
            ->with('success', 'Pubblicazione aggiornata correttamente.');
    }

    public function destroy(Publication $publication)
    {
        foreach ($publication->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
            $attachment->delete();
        }

        $publication->projects()->detach();
        $publication->delete();

        return redirect()
            ->route('publications.index')
            ->with('success', 'Pubblicazione eliminata.');
    }
}
