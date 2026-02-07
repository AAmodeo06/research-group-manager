
{{-- Realizzato da Luigi La Gioia --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            Modifica pubblicazione
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-8">

            <form method="POST"
                  action="{{ route('publications.update', $publication) }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium">Titolo</label>
                    <input name="title" value="{{ $publication->title }}" required
                           class="w-full mt-1 rounded border-secondary-300">
                </div>

                <div>
                    <label class="block text-sm font-medium">Stato</label>
                    <select name="status" class="w-full mt-1 rounded border-secondary-300">
                        @foreach(['drafting','submitted','accepted','published'] as $s)
                            <option value="{{ $s }}"
                                @selected($publication->status === $s)>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Progetti (CHECKBOX) --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Progetti 
                    </label>

                    <div class="space-y-2">
                        @foreach($projects as $project)
                            <label class="flex items-center gap-2">
                                <input type="checkbox"
                                       name="projects[]"
                                       value="{{ $project->id }}"
                                       class="rounded border-secondary-300 text-primary-600"
                                       @checked(
                                            collect(old('projects', $publication->projects->pluck('id')))
                                                ->contains($project->id)
                                        )>
                                <span>{{ $project->title }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('projects')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Nuovo PDF</label>
                    <input type="file" name="pdf">
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('publications.show', $publication) }}"
                       class="text-secondary-600 hover:underline">
                        Annulla
                    </a>

                    <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                        Aggiorna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
