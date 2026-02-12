{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
                {{ $group->name }}
            </h2>

            <a href="{{ route('projects.index') }}"
               class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700">
                Vai alla sezione progetti
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-6 space-y-8">

        {{-- MESSAGGI --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- DESCRIZIONE GRUPPO --}}
        @if($group->description)
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-secondary-900 mb-2">
                    Descrizione del gruppo
                </h3>
                <p class="text-secondary-700 text-sm">
                    {{ $group->description }}
                </p>
            </div>
        @endif

        {{-- GRID: MEMBRI + PROGETTI --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- CARD: MEMBRI --}}
            <div class="bg-white rounded-lg shadow flex flex-col h-[420px]">

                {{-- HEADER --}}
                <div class="p-6 border-b border-secondary-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-secondary-900">
                        Membri del gruppo
                    </h3>
                    <span class="text-sm text-secondary-600">
                        {{ $group->users->count() }}
                    </span>
                </div>

                {{-- FORM AGGIUNTA MEMBRO --}}
                <div class="p-4 border-b border-secondary-200">
                    <form action="{{ route('group.members.add') }}" method="POST" class="flex gap-3">
                        @csrf

                        <select name="user_id"
                                required
                                class="flex-1 rounded border-secondary-300 text-sm">
                            <option value="">Seleziona un utente da aggiungere</option>
                            @foreach($availableUsers as $available)
                                <option value="{{ $available->id }}">
                                    {{ $available->name }} ({{ $available->email }})
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white rounded text-sm hover:bg-primary-700">
                            Aggiungi
                        </button>
                    </form>
                </div>

                {{-- LISTA MEMBRI --}}
                <div class="p-6 overflow-y-auto flex-1">
                    @if($group->users->isEmpty())
                        <p class="text-sm text-secondary-600">
                            Nessun membro presente.
                        </p>
                    @else
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-secondary-200 text-left text-secondary-600">
                                    <th class="py-2">Nome</th>
                                    <th>Ruolo</th>
                                    <th class="text-right">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->users as $member)
                                    <tr class="border-b border-secondary-100 last:border-0">
                                        <td class="py-3 font-medium text-secondary-900">
                                            {{ $member->name }}
                                        </td>
                                        <td class="text-secondary-700">
                                            {{ ucfirst($member->global_role) }}
                                        </td>
                                        <td class="text-right">
                                            @if($member->id !== auth()->id())
                                                <form action="{{ route('group.members.remove', $member) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Rimuovere l’utente dal gruppo?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-xs text-red-600 hover:underline">
                                                        Rimuovi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-secondary-400 italic">
                                                    Tu
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

            {{-- CARD: PROGETTI --}}
            <div class="bg-white rounded-lg shadow flex flex-col h-[420px]">
                <div class="p-6 border-b border-secondary-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-secondary-900">
                        Progetti del gruppo
                    </h3>
                    <span class="text-sm text-secondary-600">
                        {{ $group->projects->count() }}
                    </span>
                </div>

                <div class="p-6 overflow-y-auto flex-1 space-y-3">
                    @if($group->projects->isEmpty())
                        <p class="text-sm text-secondary-600">
                            Nessun progetto associato al gruppo.
                        </p>
                    @else
                        @foreach($group->projects as $project)
                            <div class="border border-secondary-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="font-semibold text-secondary-900 hover:text-primary-700">
                                        {{ $project->title }}
                                    </a>

                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-secondary-400 hover:text-primary-600">
                                        →
                                    </a>
                                </div>

                                <div class="text-sm text-secondary-600 mt-1">
                                    Stato: {{ $project->status }}
                                </div>

                                <div class="text-sm text-secondary-600">
                                    Membri: {{ $project->users->count() }}
                                </div>

                                @if($project->description)
                                    <p class="text-sm text-secondary-700 mt-2">
                                        {{ \Illuminate\Support\Str::limit($project->description, 100) }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
