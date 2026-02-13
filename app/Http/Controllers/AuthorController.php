<?php

//Realizzato da: Luigi La Gioia

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Publication;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function store(Request $request, Publication $publication)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|integer|min:1',
            'is_corresponding' => 'nullable|boolean',
        ]);

        // evita duplicazione autore
        if ($publication->authors()->where('user_id', $validated['user_id'])->exists()) {
            return back()
                ->withErrors(['user_id' => 'Questo utente è già autore della pubblicazione.'])
                ->withInput();
        }

        $maxPosition = $publication->authors()->max('position') ?? 0;

        if ($validated['position'] > $maxPosition + 1) {
            return back()
                ->withErrors([
                    'position' => 'La posizione deve essere consecutiva. Prossima disponibile: ' . ($maxPosition + 1)
                ])
                ->withInput();
        }

        // controllo sull'ordine
        if ($publication->authors()->where('position', $validated['position'])->exists()) {
            return back()
                ->withErrors(['position' => 'Questa posizione è già occupata.'])
                ->withInput();
        }

        // garantisce un solo corresponding author per pubblicazione
        if ($request->boolean('is_corresponding')) {
            Author::where('publication_id', $publication->id)
                ->update(['is_corresponding' => false]);
        }

        Author::create([
            'publication_id' => $publication->id,
            'user_id' => $validated['user_id'],
            'position' => $validated['position'],
            'is_corresponding' => $request->boolean('is_corresponding'),
        ]);

        return back()->with('success', 'Autore aggiunto correttamente.');
    }

    public function update(Request $request, Publication $publication, Author $author)
    {
        // sicurezza: l'autore deve appartenere alla pubblicazione
        abort_if($author->publication_id !== $publication->id, 404);

        $validated = $request->validate([
            'position' => 'required|integer|min:1',
            'is_corresponding' => 'nullable|boolean',
        ]);

        $maxPosition = $publication->authors()->max('position') ?? 0;

        if ($validated['position'] > $maxPosition) {
            return back()
                ->withErrors(['position' => 'Posizione non valida.'])
                ->withInput();
        }

        // evita collisione di posizione con altri autori
        if (
            $publication->authors()
                ->where('position', $validated['position'])
                ->where('id', '!=', $author->id)
                ->exists()
        ) {
            return back()
                ->withErrors(['position' => 'Questa posizione è già occupata.'])
                ->withInput();
        }

        if ($request->boolean('is_corresponding')) {
            Author::where('publication_id', $publication->id)
                ->update(['is_corresponding' => false]);
        }

        $author->update([
            'position' => $validated['position'],
            'is_corresponding' => $request->boolean('is_corresponding'),
        ]);

        return back()->with('success', 'Autore aggiornato.');
    }

    public function destroy(Publication $publication, Author $author)
    {
        // l'autore deve appartenere alla pubblicazione
        abort_if($author->publication_id !== $publication->id, 404);

        $author->delete();

        $authors = $publication->authors()->orderBy('position')->get();

        foreach ($authors as $index => $a) {
            $a->update(['position' => $index + 1]);
        }

        return back()->with('success', 'Autore rimosso.');
    }
}
