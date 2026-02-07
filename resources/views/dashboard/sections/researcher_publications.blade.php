
{{-- Realizzato da Luigi La Gioia --}}

<section class="bg-white rounded-xl border border-secondary-200 p-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-secondary-900">
                Pubblicazioni
            </h3>
            <p class="text-sm text-secondary-500">
                Pubblicazioni a cui stai contribuendo
            </p>
        </div>

        <a href="{{ route('publications.index') }}"
           class="text-sm font-medium text-primary-600 hover:underline">
            Vai alle pubblicazioni →
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Totali</div>
            <div class="text-2xl font-bold">
                {{ $publications->count() }}
            </div>
        </div>

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Draft</div>
            <div class="text-2xl font-bold text-gray-600">
                {{ $publications->where('status', 'draft')->count() }}
            </div>
        </div>

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Submitted</div>
            <div class="text-2xl font-bold text-blue-600">
                {{ $publications->where('status', 'submitted')->count() }}
            </div>
        </div>

        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Published</div>
            <div class="text-2xl font-bold text-green-600">
                {{ $publications->where('status', 'published')->count() }}
            </div>
        </div>

    </div>
</section>
