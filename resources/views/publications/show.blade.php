
{{-- Realizzato da Luigi La Gioia --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            {{ $publication->title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow space-y-6">

            <p><strong>Stato:</strong> {{ ucfirst($publication->status) }}</p>

            <p>
                <strong>Progetti:</strong>
                {{ $publication->projects->pluck('title')->join(', ') }}
            </p>

            {{-- PDF --}}
            @if($publication->attachments->isNotEmpty())
                <a href="{{ asset('storage/' . $publication->attachments->first()->path) }}"
                   target="_blank"
                   class="text-primary-600 hover:underline">
                    Apri PDF
                </a>
            @endif

            <hr>

            {{-- AUTORI --}}
            <h3 class="font-semibold text-lg">Autori</h3>

            <ul class="space-y-2">
                @foreach($publication->authors as $author)
                    <li class="flex justify-between items-center">
                        <span>
                            {{ $loop->iteration }}. {{ $author->user->name }}
                            <span class="text-xs text-secondary-500">
                                @if($author->is_corresponding)
                                    – corresponding author
                                @endif
                            </span>
                        </span>

                        <form method="POST"
                              action="{{ route('authors.destroy', [$publication, $author]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:underline">
                                Rimuovi
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>

            <hr>

            {{-- AGGIUNGI AUTORE --}}
            <h4 class="font-medium text-lg">Aggiungi autore</h4>

            <form method="POST"
                  action="{{ route('authors.store', $publication) }}"
                  class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf

                <div class="md:col-span-5">
                    <label class="text-sm font-medium">Utente</label>
                    <select name="user_id" class="w-full rounded border-secondary-300">
                        <option value="">Seleziona utente</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="min-h-[18px]">
                        @error('user_id')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="text-sm font-medium">Ordine</label>
                    <input type="number" name="position"
                           value="{{ old('position') }}"
                           class="w-full rounded border-secondary-300">
                    <div class="min-h-[18px]">
                        @error('position')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:col-span-3 flex items-center gap-2 pb-6">
                    <input type="checkbox" name="is_corresponding" value="1"
                           {{ old('is_corresponding') ? 'checked' : '' }}>
                    <span class="text-sm">
                        Corresponding author
                        <span class="block text-xs text-secondary-500">
                            Autore principale di riferimento
                        </span>
                    </span>
                </div>

                <div class="md:col-span-2 flex items-end">
                    <button class="w-full px-4 py-2 bg-primary-600 text-white rounded">
                        Aggiungi
                    </button>
                </div>
            </form>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('publications.edit', $publication) }}"
                   class="px-4 py-2 bg-secondary-200 rounded">
                    Modifica
                </a>

                <form method="POST" action="{{ route('publications.destroy', $publication) }}">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-red-600 text-white rounded">
                        Elimina
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
