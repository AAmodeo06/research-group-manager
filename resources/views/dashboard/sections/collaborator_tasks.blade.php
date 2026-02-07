
{{-- Realizzato da Luigi La Gioia --}}

<section class="bg-white rounded-xl border border-secondary-200 p-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-secondary-900">
                I miei task
            </h3>
            <p class="text-sm text-secondary-500">
                Attività assegnate a te
            </p>
        </div>

        <a href="{{ route('tasks.index') }}"
           class="text-sm font-medium text-primary-600 hover:underline">
            Vai ai task →
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Totali</div>
            <div class="text-2xl font-bold">
                {{ $tasks->count() }}
            </div>
        </div>

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Completati</div>
            <div class="text-2xl font-bold text-green-600">
                {{ $tasks->where('status', 'completed')->count() }}
            </div>
        </div>

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">In corso</div>
            <div class="text-2xl font-bold text-blue-600">
                {{ $tasks->where('status', 'in_progress')->count() }}
            </div>
        </div>

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">In ritardo</div>
            <div class="text-2xl font-bold text-red-600">
                {{ $tasks->filter(fn($t) => $t->isOverdue())->count() }}
            </div>
        </div>

    </div>
</section>
