{{-- Realizzato da Luigi La Gioia --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
                Pubblicazioni
            </h2>

            @if(in_array(auth()->user()->global_role, ['pi', 'researcher']))
                <a href="{{ route('publications.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700 hover:text-white">
                    + Nuova pubblicazione
                </a>
            @endif
        </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg divide-y">
                @forelse($publications as $publication)
                    <div class="p-6 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-secondary-900">
                                {{ $publication->title }}
                            </h3>

                            <p class="text-sm text-secondary-600">
                                Stato: {{ $publication->statusLabel() }}
                            </p>

                            <p class="text-sm text-secondary-500">
                                Progetti:
                                {{ $publication->projects->pluck('title')->join(', ') }}
                            </p>
                        </div>

                        <a href="{{ route('publications.show', $publication) }}"
                           class="text-primary-600 hover:underline text-sm">
                            Dettagli →
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-secondary-600">
                        Nessuna pubblicazione disponibile.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
