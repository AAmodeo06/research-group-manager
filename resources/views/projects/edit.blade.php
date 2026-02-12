{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Modifica progetto
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">

            {{-- Errori di validazione --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded bg-red-100 text-red-800 text-sm">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('projects.update', $project) }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Titolo --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-secondary-700">
                        Titolo
                    </label>
                    <input id="title"
                           type="text"
                           name="title"
                           value="{{ old('title', $project->title) }}"
                           required
                           class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Codice --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-secondary-700">
                        Codice
                    </label>
                    <input id="code"
                           type="text"
                           name="code"
                           value="{{ old('code', $project->code) }}"
                           class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Funder --}}
                <div>
                    <label for="funder" class="block text-sm font-medium text-secondary-700">
                        Funder
                    </label>
                    <input id="funder"
                           type="text"
                           name="funder"
                           value="{{ old('funder', $project->funder) }}"
                           class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Data inizio --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-secondary-700">
                            Data inizio
                        </label>
                        <input id="start_date"
                               type="date"
                               name="start_date"
                               value="{{ old('start_date', $project->start_date) }}"
                               required
                               class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <p class="mt-1 text-xs text-secondary-500">
                            Deve essere precedente o uguale alla data di fine.
                        </p>
                    </div>

                    {{-- Data fine --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-secondary-700">
                            Data fine
                        </label>
                        <input id="end_date"
                               type="date"
                               name="end_date"
                               value="{{ old('end_date', $project->end_date) }}"
                               class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <p class="mt-1 text-xs text-secondary-500">
                            Può essere lasciata vuota se il progetto è in corso.
                        </p>
                    </div>
                </div>

                {{-- Stato --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-secondary-700">
                        Stato
                    </label>
                    <select id="status"
                            name="status"
                            class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach(['open','in_progress','completed'] as $status)
                            <option value="{{ $status }}"
                                @selected(old('status', $project->status) === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-secondary-500">
                        Indica lo stato corrente del progetto.
                    </p>
                </div>

                {{-- Descrizione --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-secondary-700">
                        Descrizione
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $project->description) }}</textarea>
                </div>

                {{-- Documento PDF --}}
                <div>
                    <label for="file" class="block text-sm font-medium text-secondary-700">
                        Documenti PDF
                    </label>

                    @if($project->attachments && $project->attachments->count() > 0)
                        <p class="text-sm mb-1">File caricati:</p>
                        <ul class="list-disc ml-5 text-sm">
                            @foreach($project->attachments as $file)
                                <li>
                                    <a href="{{ Storage::url($file->file_path ?? $file->path) }}" target="_blank" class="text-primary-600 underline">
                                        {{ $file->title ?? '(PDF)' }} - v{{ $file->version ?? 1 }} - {{ $file->created_at->format('d/m/Y H:i') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <input id="file"
                           type="file"
                           name="file"
                           accept=".pdf"
                           class="mt-1 text-sm text-secondary-700">
                    <p class="mt-1 text-xs text-secondary-500">
                        Caricando un nuovo file, verrà creata una nuova versione.
                    </p>
                </div>

                {{-- Azioni --}}
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('projects.show', $project) }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-secondary-200 text-secondary-800 text-sm rounded hover:bg-secondary-300">
                        Annulla
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700">
                        Salva modifiche
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
