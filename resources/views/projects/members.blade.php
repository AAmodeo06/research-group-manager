
{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Membri progetto: {{ $project->title }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4 space-y-10">

        {{-- Messaggi --}}
        @if(session('success'))
            <div class="p-4 rounded bg-secondary-100 text-secondary-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded bg-red-100 text-red-800 text-sm">
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CARD: Assegnazione / aggiornamento membro --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-secondary-900 mb-4">
                Assegna o aggiorna un membro
            </h3>

            <p class="text-sm text-secondary-600 mb-6">
                Puoi assegnare o aggiornare ruoli operativi (manager, researcher, collaborator).
                Il Principal Investigator viene definito solo in fase di creazione del progetto
                e non può essere modificato da questa sezione.
            </p>

            <form method="POST"
                  action="{{ route('projects.members.store', $project) }}"
                  class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @csrf

                {{-- Utente --}}
                <div>
                    <label for="user_id" class="block text-sm font-medium text-secondary-700 mb-1">
                        Utente
                    </label>
                    <select id="user_id"
                            name="user_id"
                            class="w-full rounded border border-secondary-300 bg-secondary-50 text-secondary-900">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ruolo nel progetto --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-secondary-700 mb-1">
                        Ruolo nel progetto
                    </label>
                    <select id="role"
                            name="role"
                            class="w-full rounded border border-secondary-300 bg-secondary-50 text-secondary-900">
                        <option value="manager">Project Manager</option>
                        <option value="researcher">Researcher</option>
                        <option value="collaborator">Collaborator</option>
                    </select>
                </div>

                {{-- Effort --}}
                <div>
                    <label for="effort" class="block text-sm font-medium text-secondary-700 mb-1">
                        Effort (%)
                    </label>
                    <input id="effort"
                           type="number"
                           name="effort"
                           min="0"
                           max="100"
                           placeholder="0–100"
                           class="w-full rounded border border-secondary-300 bg-secondary-50 text-secondary-900">
                </div>

                <div class="md:col-span-3 text-right">
                    <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white text-sm rounded hover:bg-primary-700">
                        Assegna / aggiorna
                    </button>
                </div>
            </form>
        </div>

        {{-- CARD: Membri attuali --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-secondary-900 mb-4">
                Membri del progetto
            </h3>

            @if($members->isEmpty())
                <p class="text-sm text-secondary-600">
                    Nessun membro assegnato al progetto.
                </p>
            @else
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-secondary-200 text-left text-secondary-600">
                            <th class="py-2">Nome</th>
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

                                <td>
                                    @if($member->pivot->role === 'pi')
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-primary-100 text-primary-800 font-semibold">
                                            Principal Investigator
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-secondary-100 text-secondary-800">
                                            {{ ucfirst($member->pivot->role) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-secondary-700">
                                    {{ $member->pivot->effort ?? '—' }}%
                                </td>

                                <td class="text-right">
                                    @if($member->pivot->role !== 'pi')
                                        <form method="POST"
                                              action="{{ route('projects.members.destroy', [$project, $member]) }}"
                                              onsubmit="return confirm('Rimuovere questo membro dal progetto?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-sm text-red-600 hover:text-red-800">
                                                Rimuovi
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-secondary-400 italic">
                                            —
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</x-app-layout>
