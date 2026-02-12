{{-- Realizzato da Cosimo Mandrillo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Modifica Milestone</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        @include('milestones._form', [
            'action' => route('milestones.update', [$project, $milestone]),
            'method' => 'PUT',
            'milestone' => $milestone
        ])
    </div>
</x-app-layout>
