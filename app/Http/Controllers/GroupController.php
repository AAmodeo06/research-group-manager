<?php

//Realizzato da: Cosimo Mandrillo

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = auth()->user();

        // Solo il PI può accedere alla sezione Group
        abort_unless($user->global_role === 'pi', 403);

        // Il PI deve appartenere a un gruppo
        abort_unless($user->group, 404);

        $group = $user->group->load([
            'users',
            'projects.users',
        ]);

        $availableUsers = User::whereNull('group_id')->get();

        return view('groups.show', compact('group', 'availableUsers'));
    }

    public function edit()
    {
        $user = auth()->user();

        abort_unless($user->global_role === 'pi', 403);
        abort_unless($user->group, 404);

        $group = $user->group;

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->global_role === 'pi', 403);
        abort_unless($user->group, 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user->group->update($data);

        return redirect()
            ->route('groups.show')
            ->with('success', 'Gruppo aggiornato correttamente.');
    }

    public function addMember(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->global_role === 'pi', 403);
        abort_unless($user->group, 404);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $member = User::findOrFail($data['user_id']);

        // 1) VINCOLO: non puoi aggiungere te stesso
        abort_unless($member->id !== $user->id, 422);

        // 2) VINCOLO: se è già nel tuo gruppo, inutile
        abort_unless($member->group_id !== $user->group->id, 422);

        // 3) regola “forte”: l'utente deve essere libero (non in altri gruppi)
        abort_unless($member->group_id === null, 422);

        $member->update([
            'group_id' => $user->group->id,
        ]);

        return back()->with('success', 'Utente aggiunto al gruppo.');
    }

    public function removeMember(User $member)
    {
        $user = auth()->user();

        abort_unless($user->global_role === 'pi', 403);
        abort_unless($user->group, 404);
        abort_unless($member->group_id === $user->group->id, 403);
        abort_unless($member->id !== $user->id, 403);

        $member->update([
            'group_id' => null,
        ]);

        return back()->with('success', 'Utente rimosso dal gruppo.');
    }
}
