{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center w-full">
            <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
                Progetti di Ricerca
            </h2>

            @if(auth()->user()->global_role === 'pi')
                <a href="{{ route('projects.create') }}"
                   class="ml-auto inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700 hover:text-white">
                    + Nuovo progetto
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @forelse($projects as $project)
                <div class="bg-white shadow rounded-lg p-6">

                    <h3 class="font-semibold text-lg text-secondary-900">
                        {{ $project->title }}
                    </h3>

                    <p class="text-xs text-secondary-500 mt-1">
                        Stato: {{ $project->status }}
                    </p>

                    <p class="mt-1 text-sm text-secondary-700">
                        {{ $project->description ?? 'Nessuna descrizione.' }}
                    </p>

                    <p class="mt-2 text-sm text-secondary-600">
                        Milestone: {{ $project->milestones?->count() ?? 0 }} |
                        Pubblicazioni: {{ $project->publications?->count() ?? 0 }}
                    </p>

                    <p class="mt-2 text-sm text-secondary-700">
                        Documenti PDF allegati: {{ $project->attachments?->count() ?? 0 }}
                    </p>

                    <div class="mt-4 flex items-center gap-3 text-sm">
                        <a href="{{ route('projects.show', $project) }}"
                           class="text-primary-600 hover:underline">
                            Dettagli →
                        </a>

                        @if(isset($project->pivot) && in_array($project->pivot->role, ['pi', 'manager']))
                            <span class="text-secondary-400">|</span>
                            <a href="{{ route('projects.members', $project) }}"
                               class="text-primary-600 hover:underline">
                                Gestisci membri
                            </a>
                        @endif

                        @if(isset($project->pivot) && $project->pivot->role === 'pi')
                            <span class="text-secondary-400">|</span>

                            <form action="{{ route('projects.destroy', $project) }}"
                                  method="POST"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        onclick="return confirm('Sei sicuro di voler eliminare questo progetto?');"
                                        class="text-red-600 hover:text-red-800 ml-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-5 w-5"
                                         viewBox="0 0 20 20"
                                         fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 5a1 1 0 011 1v7a1 1 0 11-2 0V8a1 1 0 011-1zm4 0a1 1 0 00-1 1v7a1 1 0 102 0V8a1 1 0 00-1-1z"
                                              clip-rule="evenodd" />
                                    </svg>
                                    Elimina
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @empty
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-secondary-600">
                        Nessun progetto disponibile.
                    </p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
