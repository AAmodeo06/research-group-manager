
{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Crea un nuovo progetto
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow">

            <form method="POST"
                  action="{{ route('projects.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf

                {{-- Titolo --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-secondary-700">
                        Titolo
                    </label>
                    <input id="title"
                           type="text"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Descrizione --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-secondary-700">
                        Descrizione
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
                </div>

                {{-- Data inizio --}}
                <div>
                    <label for="start_date" class="block text-sm font-medium text-secondary-700">
                        Data inizio
                    </label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           required
                           class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                {{-- Data fine --}}
                <div>
                    <label for="end_date" class="block text-sm font-medium text-secondary-700">
                        Data fine (opzionale)
                    </label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-secondary-500">
                        Può essere lasciata vuota se il progetto è in corso.
                    </p>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-secondary-700">
                        Stato
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="mt-1 w-full rounded-md bg-secondary-50 text-secondary-900 border border-secondary-300 p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="Open">Open</option>
                        <option value="In progress">In progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                {{-- Documento PDF --}}
                <div>
                    <label for="file" class="block text-sm font-medium text-secondary-700">
                        Allega documento (PDF)
                    </label>
                    <input id="file"
                           type="file"
                           name="file"
                           accept=".pdf"
                           class="mt-1 text-sm text-secondary-700">
                    <p class="mt-1 text-xs text-secondary-500">
                        Documento opzionale relativo al progetto (max 2MB).
                    </p>
                </div>

                {{-- Azioni --}}
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('projects.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-secondary-200 text-secondary-800 text-sm rounded hover:bg-secondary-300">
                        Annulla
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700">
                        Salva progetto
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
