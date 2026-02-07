<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return Publication::whereHas('projects.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with([
                'projects:id,title',
                'authors.user:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'  => 'required|string|max:255',
            'type'   => 'required|string',
            'venue'  => 'nullable|string',
            'doi'    => 'nullable|string',
            'status' => 'required|in:drafting,submitted,accepted,published',
        ]);

        $publication = Publication::create($data);

        return response()->json($publication, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Publication $publication)
    {
        $user = $request->user();

        abort_unless(
            $publication->projects()
                ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                ->exists(),
            403
        );

        return $publication->load([
            'projects:id,title',
            'authors.user:id,name'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publication $publication)
    {
        $data = $request->validate([
            'title'  => 'sometimes|string|max:255',
            'type'   => 'sometimes|string',
            'venue'  => 'nullable|string',
            'doi'    => 'nullable|string',
            'status' => 'sometimes|in:drafting,submitted,accepted,published',
        ]);

        $publication->update($data);

        return response()->json($publication);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication)
    {
        $publication->delete();

        return response()->json(null, 204);
    }
}
