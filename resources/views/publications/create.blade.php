{{-- Realizzato da Luigi La Gioia --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Nuova pubblicazione
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">

            <form method="POST"
                  action="{{ route('publications.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf

                {{-- Titolo --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700">Titolo</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="mt-1 w-full rounded border-secondary-300">
                    @error('title')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo + Venue --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Tipo</label>
                        <input type="text" name="type" value="{{ old('type') }}"
                               class="mt-1 w-full rounded border-secondary-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-secondary-700">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue') }}"
                               class="mt-1 w-full rounded border-secondary-300">
                    </div>
                </div>

                {{-- Stato --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700">Stato</label>
                    <select name="status" class="mt-1 w-full rounded border-secondary-300">
                        <option value="drafting">Drafting</option>
                        <option value="submitted">Submitted</option>
                        <option value="accepted">Accepted</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                {{-- Progetti (CHECKBOX) --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Progetti collegati
                    </label>

                    <div class="space-y-2">
                        @foreach($projects as $project)
                            <label class="flex items-center gap-2">
                                <input type="checkbox"
                                       name="projects[]"
                                       value="{{ $project->id }}"
                                       class="rounded border-secondary-300 text-primary-600">
                                <span>{{ $project->title }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('projects')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PDF --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700">PDF</label>
                    <input type="file" name="pdf" accept=".pdf" class="mt-1">
                </div>

                {{-- Azioni --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                        Salva
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
