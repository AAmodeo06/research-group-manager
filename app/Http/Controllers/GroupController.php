<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\SupportContext\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Visualizza la dashboard del gruppo del PI.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Verifica che l'utente sia un PI e abbia un gruppo associato
        abort_unless($user->global_role === 'pi', 403);
        
        $group = $user->group;
        if (!$group) {
            abort(404, "Nessun gruppo associato a questo utente.");
        }

        // Carica i membri e i progetti del gruppo
        $group->load(['users', 'projects.users']);

        // Recupera gli utenti che non appartengono a nessun gruppo per il dropdown
        $availableUsers = User::whereNull('group_id')
            ->where('global_role', '!=', 'pi')
            ->orderBy('name')
            ->get();

        return view('groups.show', compact('group', 'availableUsers'));
    }

    /**
     * Mostra il form di modifica del gruppo.
     */
    public function edit()
    {
        $user = auth()->user();
        abort_unless($user->global_role === 'pi', 403);
        
        $group = $user->group;
        return view('groups.edit', compact('group'));
    }

    /**
     * Aggiorna i dati del gruppo (nome e descrizione).
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->global_role === 'pi', 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user->group->update($validated);

        return redirect()->route('groups.show')->with('success', 'Informazioni del gruppo aggiornate con successo.');
    }

    /**
     * Aggiunge un utente esistente al gruppo del PI.
     */
    public function addMember(Request $request)
    {
        $me = auth()->user();
        abort_unless($me->global_role === 'pi', 403);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $newMember = User::findOrFail($validated['user_id']);

        $newMember->group_id = $me->group_id; 
        $newMember->save();

        return back()->with('success', "{$newMember->name} è stato aggiunto al gruppo.");
    }

    /**
     * Rimuove un utente dal gruppo (imposta group_id a null).
     */
    public function removeMember(User $user)
    {
        $me = auth()->user();
        
        abort_unless($me->global_role === 'pi', 403);
        
        if ($user->group_id === null) {
            return back()->with('success', "{$user->name} non appartiene a nessun gruppo.");
        }

        abort_unless($user->group_id === $me->group_id, 403);
        
        if ($user->id === $me->id) {
            return back()->withErrors(['message' => 'Non puoi rimuovere te stesso dal tuo gruppo.']);
        }

        $user->group_id = null;
        $user->save();

        return back()->with('success', "{$user->name} rimosso dal gruppo correttamente.");
    }
}