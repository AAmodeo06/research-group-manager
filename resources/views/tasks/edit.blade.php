{{-- Realizzato da Andrea Amodeo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Modifica Task
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-8">

            <form method="POST"
                  action="{{ route('tasks.update', $task) }}"
                  class="space-y-4">
                @csrf
                @method('PUT')

                {{-- TITOLO --}}
                <div>
                    <label class="block font-medium mb-1">Titolo</label>
                    <input type="text"
                           name="title"
                           value="{{ old('title', $task->title) }}"
                           required
                           class="w-full rounded border-gray-300">
                    @error('title')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DESCRIZIONE --}}
                <div>
                    <label class="block font-medium mb-1">Descrizione</label>
                    <textarea name="description"
                              rows="4"
                              class="w-full rounded border-gray-300">{{ old('description', $task->description) }}</textarea>
                </div>

                {{-- PROGETTO --}}
                <div>
                    <label class="block font-medium mb-1">Progetto</label>
                    <select name="project_id"
                            class="w-full rounded border-gray-300">
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}"
                                @selected(old('project_id', $task->project_id) == $project->id)>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ASSEGNATARIO --}}
                <div>
                    <label class="block font-medium mb-1">Assegnatario</label>
                    <select name="assignee_id"
                            class="w-full rounded border-gray-300">
                        <option value="">—</option>

                        @foreach($projects as $project)
                            <optgroup label="{{ $project->title }}">
                                @foreach($project->users as $user)
                                    <option value="{{ $user->id }}"
                                        @selected(old('assignee_id', $task->assignee_id) == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('assignee_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- STATO / PRIORITÀ / SCADENZA --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium mb-1">Stato</label>
                        <select name="status"
                                class="w-full rounded border-gray-300">
                            <option value="open"
                                @selected(old('status', $task->status) === 'open')>
                                Open
                            </option>
                            <option value="in_progress"
                                @selected(old('status', $task->status) === 'in_progress')>
                                In progress
                            </option>
                            <option value="done"
                                @selected(old('status', $task->status) === 'done')>
                                Done
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Priorità</label>
                        <select name="priority"
                                class="w-full rounded border-gray-300">
                            <option value="low"
                                @selected(old('priority', $task->priority) === 'low')>
                                Low
                            </option>
                            <option value="medium"
                                @selected(old('priority', $task->priority) === 'medium')>
                                Medium
                            </option>
                            <option value="high"
                                @selected(old('priority', $task->priority) === 'high')>
                                High
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Scadenza</label>
                        <input type="date"
                               name="due_date"
                               value="{{ old('due_date', $task->due_date) }}"
                               class="w-full rounded border-gray-300">
                    </div>
                </div>

                {{-- AZIONI --}}
                <div class="flex justify-between pt-6">
                    <a href="{{ route('tasks.index') }}"
                       class="text-primary-600 hover:underline">
                        ← Torna
                    </a>

                    <button class="px-6 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                        Salva modifiche
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
