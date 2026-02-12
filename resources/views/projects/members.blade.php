{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Gestione membri progetto: {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow space-y-8">

            {{-- Messaggi successo --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Errori --}}
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded text-sm">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM AGGIUNTA MEMBRO --}}
            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-4">
                    Aggiungi / modifica membro
                </h3>

                <form method="POST"
                      action="{{ route('projects.members.store', $project) }}"
                      class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf

                    {{-- Utente --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm text-secondary-700 mb-1">
                            Utente
                        </label>
                        <select name="user_id"
                                required
                                class="w-full rounded-md bg-secondary-50 border border-secondary-300 p-2 text-sm">
                            <option value="">Seleziona utente</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ruolo --}}
                    <div>
                        <label class="block text-sm text-secondary-700 mb-1">
                            Ruolo
                        </label>
                        <select name="role"
                                required
                                class="w-full rounded-md bg-secondary-50 border border-secondary-300 p-2 text-sm">
                            <option value="manager">Manager</option>
                            <option value="researcher">Researcher</option>
                            <option value="collaborator">Collaborator</option>
                        </select>
                    </div>

                    {{-- Effort --}}
                    <div>
                        <label class="block text-sm text-secondary-700 mb-1">
                            Effort (%)
                        </label>
                        <input type="number"
                               name="effort"
                               min="0"
                               max="100"
                               class="w-full rounded-md bg-secondary-50 border border-secondary-300 p-2 text-sm">
                    </div>

                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2 bg-primary-600 text-white text-sm rounded hover:bg-primary-700">
                            Salva
                        </button>
                    </div>
                </form>
            </section>

            {{-- LISTA MEMBRI ATTUALI --}}
            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-4">
                    Membri attuali ({{ $members->count() }})
                </h3>

                @if($members->isEmpty())
                    <p class="text-sm text-secondary-500">
                        Nessun membro assegnato al progetto.
                    </p>
                @else
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-secondary-200 text-left text-secondary-600">
                                <th class="py-3">Nome</th>
                                <th>Ruolo</th>
                                <th>Effort</th>
                                <th class="text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr class="border-b border-secondary-100 last:border-0">
                                    <td class="py-3 font-medium text-secondary-900">
                                        {{ $member->name }}
                                    </td>
                                    <td class="text-secondary-700">
                                        {{ ucfirst($member->pivot->role) }}
                                    </td>
                                    <td class="text-secondary-700">
                                        {{ $member->pivot->effort ?? '—' }}
                                    </td>
                                    <td class="text-right">
                                        @if($member->pivot->role !== 'pi')
                                            <form method="POST"
                                                  action="{{ route('projects.members.destroy', [$project, $member]) }}"
                                                  onsubmit="return confirm('Rimuovere questo membro dal progetto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs text-red-600 hover:underline">
                                                    Rimuovi
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-secondary-400 italic">
                                                PI
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>

            <div class="pt-6 border-t border-secondary-200 flex justify-between">
                <a href="{{ route('projects.show', $project) }}"
                   class="text-sm text-primary-600 hover:underline">
                    ← Torna al progetto
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
