
{{-- Realizzato da Luigi La Gioia --}}

<section class="bg-white rounded-xl border border-secondary-200 p-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-secondary-900">
                Progetti coordinati
            </h3>
            <p class="text-sm text-secondary-500">
                Panoramica dei progetti di cui sei responsabile
            </p>
        </div>

        <a href="{{ route('projects.index') }}"
           class="text-sm font-medium text-primary-600 hover:underline">
            Gestisci →
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Totale --}}
        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Totali</div>
            <div class="text-3xl font-bold">
                {{ $projects->count() }}
            </div>
        </div>

        {{-- Attivi --}}
        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Attivi</div>
            <div class="text-3xl font-bold text-green-600">
                {{ $projects->where('status', 'active')->count() }}
            </div>
        </div>

        {{-- Con milestone --}}
        <div class="bg-secondary-50 rounded-lg p-4 text-center">
            <div class="text-sm text-secondary-500">Con milestone</div>
            <div class="text-3xl font-bold text-blue-600">
                {{ $projects->filter(fn($p) => $p->milestones->isNotEmpty())->count() }}
            </div>
        </div>

    </div>
</section>
