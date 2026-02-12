{{-- Realizzato da Cosimo Mandrillo --}}

<form method="POST" action="{{ $action }}" class="bg-white p-6 rounded shadow space-y-4">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div>
        <label class="block text-sm font-medium">Titolo</label>
        <input type="text"
               name="title"
               value="{{ old('title', $milestone->title ?? '') }}"
               class="w-full border rounded px-3 py-2"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium">Data di scadenza</label>
        <input type="date"
               name="due_date"
               value="{{ old('due_date', $milestone->due_date ?? '') }}"
               class="w-full border rounded px-3 py-2"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium">Stato</label>
        <select name="status" class="w-full border rounded px-3 py-2">
            <option value="planned" @selected(old('status', $milestone->status ?? '') === 'planned')>
                planned
            </option>
            <option value="in_progress" @selected(old('status', $milestone->status ?? '') === 'in_progress')>
                in progress
            </option>
            <option value="completed" @selected(old('status', $milestone->status ?? '') === 'completed')>
                completed
            </option>
        </select>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('projects.show', $project) }}"
           class="text-sm text-gray-600 hover:underline">
            Annulla
        </a>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Salva
        </button>
    </div>
</form>
