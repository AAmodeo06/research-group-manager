{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Dettagli progetto: {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-6 flex justify-center">
        <div class="max-w-5xl w-full bg-white shadow rounded-lg p-8 space-y-10">

            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Informazioni generali
                </h3>

                <p class="text-sm text-secondary-900"><strong>Stato:</strong> {{ $project->status }}</p>
                <p class="text-sm text-secondary-900"><strong>Data inizio:</strong> {{ $project->start_date }}</p>
                <p class="text-sm text-secondary-900"><strong>Data fine:</strong> {{ $project->end_date ?? 'Non definita' }}</p>

                @if($project->code)
                    <p class="text-sm text-secondary-900"><strong>Codice progetto:</strong> {{ $project->code }}</p>
                @endif

                @if($project->funder)
                    <p class="text-sm text-secondary-900"><strong>Funder:</strong> {{ $project->funder }}</p>
                @endif

                @if($project->attachments && $project->attachments->count() > 0)
                    <p class="mt-2 text-sm font-semibold text-secondary-700">Documenti allegati:</p>
                    <ul class="list-disc ml-5 text-sm text-secondary-900">
                        @foreach($project->attachments->groupBy('title') as $title => $files)
                            @php
                                $latest = $files->sortByDesc('version')->first();
                                $previous = $files->sortByDesc('version')->slice(1);
                            @endphp
                            <li>
                                <div class="flex items-center gap-2">
                                    <a href="{{ Storage::url($latest->path) }}" target="_blank" class="text-primary-600 hover:underline">
                                        {{ $latest->title }} ({{ strtoupper(pathinfo($latest->path, PATHINFO_EXTENSION)) }}) v{{ $latest->version }}
                                    </a>
                                    @if($previous->count() > 0)
                                        <button type="button"
                                                class="text-secondary-500 hover:text-secondary-700 text-xs"
                                                onclick="document.getElementById('versions-{{ $latest->id }}').classList.toggle('hidden')">
                                            ↓
                                        </button>
                                    @endif
                                </div>

                                @if($previous->count() > 0)
                                    <ul id="versions-{{ $latest->id }}" class="ml-5 mt-1 list-disc text-xs text-secondary-500 hidden">
                                        @foreach($previous as $prev)
                                            <li>
                                                <a href="{{ Storage::url($prev->path) }}" target="_blank" class="hover:underline">
                                                    {{ $prev->title }} - v{{ $prev->version }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-sm text-secondary-500">Nessun documento allegato.</p>
                @endif
            </section>

            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Descrizione
                </h3>
                <p class="text-sm text-secondary-900 leading-relaxed">
                    {{ $project->description ?? 'Nessuna descrizione disponibile.' }}
                </p>
            </section>

            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Stato di avanzamento
                </h3>
                <p class="text-sm text-secondary-900">
                    Stato: <span class="font-semibold">{{ ucfirst(str_replace('_',' ', $project->progressStatus() ?? '')) }}</span>
                </p>
                <p class="text-sm text-secondary-600">
                    Avanzamento: <span class="font-medium">{{ $project->progressPercentage() ?? 0 }}%</span>
                </p>
            </section>

            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Membri del progetto ({{ $project->users ? $project->users->count() : 0 }})
                </h3>
                @if($project->users && $project->users->count() > 0)
                    <ul class="list-disc ml-5 space-y-1 text-sm text-secondary-900">
                        @foreach($project->users as $member)
                            <li>
                                {{ $member->name }}
                                <span class="text-xs text-secondary-500">({{ ucfirst($member->pivot->role) }})</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-secondary-500">Nessun membro assegnato.</p>
                @endif
            </section>

            <section>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-secondary-700 uppercase">
                        Milestone ({{ $project->milestones ? $project->milestones->count() : 0 }})
                    </h3>
                    @if(in_array(auth()->user()->global_role, ['pi', 'manager']))
                        <a href="{{ route('milestones.create', $project) }}" class="text-sm text-primary-600 hover:underline">
                            + Nuova milestone
                        </a>
                    @endif
                </div>

                @php
                    $statusColors = [
                        'planned' => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                    ];
                @endphp

                @if(!$project->milestones || $project->milestones->isEmpty())
                    <p class="text-sm text-secondary-500">Nessuna milestone definita.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($project->milestones as $ms)
                            <li class="flex justify-between items-center p-3 border rounded">
                                <div>
                                    <h4 class="font-medium">{{ $ms->title }}</h4>
                                    <p class="text-xs text-secondary-500">
                                        Scadenza: {{ $ms->due_date->format('d/m/Y') }}
                                    </p>
                                    <span class="inline-block px-2 py-1 mt-1 rounded text-xs font-semibold {{ $statusColors[$ms->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_',' ', $ms->status ?? '')) }}
                                    </span>
                                </div>

                                @if(in_array(auth()->user()->global_role, ['pi', 'manager']))
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('milestones.edit', [$project, $ms]) }}"
                                           class="text-xs text-yellow-600 hover:underline">
                                            Modifica
                                        </a>

                                        <form method="POST"
                                              action="{{ route('milestones.destroy', [$project, $ms]) }}"
                                              class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs text-red-600 hover:underline"
                                                    onclick="return confirm('Eliminare questa milestone?')">
                                                Elimina
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Modificato da Andrea Amodeo --}}
            @if($canViewProjectTasks)

            <section>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-secondary-700 uppercase">
                        Task del progetto ({{ $project->tasks->count() }})
                    </h3>

                    @if($canViewProjectTasks)
                        <a href="{{ route('projects.tasks.create', $project) }}"
                            class="text-sm text-primary-600 hover:underline">
                                + Nuovo Task
                        </a>
                    @endif
                </div>

                @if($project->tasks->isEmpty())
                    <p class="text-sm text-secondary-500">
                        Nessun task associato a questo progetto.
                    </p>
                @else
                    <ul class="space-y-2">
                        @foreach($project->tasks as $task)
                            <li class="flex justify-between items-center p-3 border rounded">
                                <div>
                                    <div class="font-medium text-secondary-900">
                                        {{ $task->title }}
                                    </div>

                                    <div class="text-xs text-secondary-500">
                                        Assegnato a:
                                        {{ $task->assignee?->name ?? 'Non assegnato' }}
                                    </div>
                                </div>

                                <a href="{{ route('projects.tasks.show', [$project, $task]) }}"
                                    class="text-xs text-primary-600 hover:underline">
                                    Dettagli
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            @endif
            {{-- Fine modifica da Andrea Amodeo --}}


            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Pubblicazioni ({{ $project->publications ? $project->publications->count() : 0 }})
                </h3>

                @if(!$project->publications || $project->publications->isEmpty())
                    <p class="text-sm text-secondary-500">Nessuna pubblicazione associata.</p>
                @else
                    <ul class="list-disc ml-5 space-y-1 text-sm text-secondary-900">
                        @foreach($project->publications->take(3) as $pub)
                            <li>{{ $pub->title }}</li>
                        @endforeach
                    </ul>
                    @if($project->publications->count() > 3)
                        <p class="mt-2 text-sm text-secondary-500">+ {{ $project->publications->count() - 3 }} altre</p>
                    @endif
                @endif
            </section>

            <div class="pt-6 border-t border-secondary-200 flex justify-between items-center">
                <a href="{{ route('projects.index') }}" class="text-sm text-primary-600 hover:underline">
                    ← Torna ai progetti
                </a>

                @if(auth()->user()->global_role === 'pi')
                    <div class="flex items-center gap-3">

                        <a href="{{ route('projects.members', $project) }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-secondary-200 text-secondary-800 text-sm rounded hover:bg-secondary-300">
                             Gestisci membri
                        </a>
                        <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700 hover:text-white">
                            Modifica
                        </a>

                        <form method="POST"
                              action="{{ route('projects.destroy', $project) }}" class="inline-flex" onsubmit="return confirm('Sei sicuro di voler eliminare definitivamente questo progetto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-secondary-200 text-secondary-800 text-sm rounded hover:bg-secondary-300">
                                Elimina
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
