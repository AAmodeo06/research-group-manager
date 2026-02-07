
{{-- Realizzato da Andrea Amodeo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-8 space-y-8">

            {{-- MESSAGGIO DI SUCCESSO --}}
            @if(session('success'))
                <div class="text-sm text-green-700 bg-green-100 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- TAG --}}
            @if($task->tags->isNotEmpty())
                <div class="flex gap-2 flex-wrap">
                    @foreach($task->tags as $tag)
                        <span class="px-2 py-1 bg-secondary-100 text-xs rounded">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- INFO TASK --}}
            <section class="space-y-2 text-sm">
                <p><strong>Progetto:</strong> {{ $task->project->title }}</p>
                <p><strong>Assegnato a:</strong> {{ $task->assignee?->name ?? '—' }}</p>
                <p><strong>Stato:</strong> {{ ucfirst(str_replace('_',' ', $task->status)) }}</p>
                <p><strong>Priorità:</strong> {{ ucfirst($task->priority) }}</p>
                <p><strong>Scadenza:</strong> {{ $task->due_date ?? '—' }}</p>

                @if($task->milestone)
                    <p><strong>Milestone:</strong> {{ $task->milestone->title }}</p>
                @endif
            </section>

            {{-- DESCRIZIONE --}}
            @if($task->description)
                <section>
                    <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                        Descrizione
                    </h3>
                    <p class="text-sm text-secondary-800">
                        {{ $task->description }}
                    </p>
                </section>
            @endif

            {{-- ALLEGATI --}}
            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Allegati
                </h3>

                @if($task->attachments->isEmpty())
                    <p class="text-sm text-secondary-500">Nessun allegato.</p>
                @else
                    <ul class="list-disc list-inside text-sm">
                        @foreach($task->attachments as $attachment)
                            <li>
                                <a href="{{ asset('storage/'.$attachment->path) }}"
                                   class="text-primary-600 underline"
                                   target="_blank">
                                    {{ basename($attachment->path) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- COMMENTI --}}
            <section>
                <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">
                    Commenti
                </h3>

                @if($task->comments->isEmpty())
                    <p class="text-sm text-secondary-500">Nessun commento.</p>
                @else
                    <ul class="space-y-3 text-sm">
                        @foreach($task->comments as $comment)
                            <li class="border-l-2 pl-3 border-secondary-200">
                                <p class="text-secondary-800">
                                    {{ $comment->body }}
                                </p>
                                <span class="text-xs text-secondary-500">
                                    {{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- FORM NUOVO COMMENTO --}}
                @auth
                    <form method="POST"
                          action="{{ route('comments.store') }}"
                          class="mt-4 space-y-2">
                        @csrf

                        <input type="hidden" name="commentable_type" value="App\Models\Task">
                        <input type="hidden" name="commentable_id" value="{{ $task->id }}">

                        <textarea name="body"
                                  required
                                  rows="3"
                                  class="w-full rounded border-gray-300 text-sm"
                                  placeholder="Aggiungi un commento..."></textarea>

                        <div class="text-right">
                            <button class="px-4 py-2 bg-primary-600 text-white rounded text-sm">
                                Commenta
                            </button>
                        </div>
                    </form>
                @endauth
            </section>

            {{-- AZIONI --}}
            <div class="flex justify-between pt-6 border-t">
                <a href="{{ route('tasks.index') }}"
                   class="text-primary-600 hover:underline">
                    ← Torna ai task
                </a>

                <div class="flex gap-3">
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="px-4 py-2 bg-primary-600 text-white rounded">
                        Modifica
                    </a>

                    <form method="POST"
                          action="{{ route('tasks.destroy', $task) }}"
                          onsubmit="return confirm('Eliminare il task?');">
                        @csrf
                        @method('DELETE')

                        <button class="px-4 py-2 bg-secondary-200 rounded">
                            Elimina
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
