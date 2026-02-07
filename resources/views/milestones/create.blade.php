
{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Nuova Milestone</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        @include('milestones._form', [
            'action' => route('milestones.store', $project),
            'method' => 'POST',
            'milestone' => null
        ])
    </div>
</x-app-layout>
