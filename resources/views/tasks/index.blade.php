{{-- Realizzato da Andrea Amodeo --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
            I miei task
        </h2>
    </x-slot>

    @php
        $canManageTasks = in_array(auth()->user()->global_role, ['pi', 'manager']);
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6 space-y-6">

            {{-- Header sezione --}}
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold text-secondary-700 uppercase">
                    Elenco task assegnati a te
                </h3>

                @if($canManageTasks)
                    <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded text-sm hover:bg-primary-700">
                        + Nuovo task
                    </a>
                @endif
            </div>

            @if($tasks->isEmpty())
                <p class="text-sm text-secondary-500">
                    Nessun task assegnato.
                </p>
            @else
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="border-b text-secondary-600">
                            <th class="text-left py-3">Task</th>
                            <th class="text-center py-3">Stato</th>
                            <th class="text-center py-3">Scadenza</th>
                            @if($canManageTasks)
                                <th class="text-center py-3">Azioni</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($tasks as $task)
                            <tr class="border-b hover:bg-secondary-50 transition">

                                {{-- TITOLO + PROGETTO --}}
                                <td class="py-4">
                                    <div class="font-medium text-secondary-900">
                                        <a href="{{ route('tasks.show', $task) }}"
                                           class="hover:underline">
                                            {{ $task->title }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-secondary-500 mt-1">
                                        {{ $task->project->title ?? '—' }}
                                    </div>
                                </td>

                                {{-- STATO (badge) --}}
                                <td class="text-center py-4">
                                    <span
                                        @class([
                                            'px-2 py-1 text-xs rounded-full font-medium',
                                            'bg-blue-100 text-blue-700' => $task->status === 'open',
                                            'bg-yellow-100 text-yellow-700' => $task->status === 'in_progress',
                                            'bg-green-100 text-green-700' => $task->status === 'done',
                                        ])
                                    >
                                        {{ ucfirst(str_replace('_',' ', $task->status)) }}
                                    </span>
                                </td>

                                {{-- SCADENZA --}}
                                <td class="text-center py-4 text-secondary-700">
                                    {{ $task->due_date
                                        ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y')
                                        : '—'
                                    }}
                                </td>

                                {{-- AZIONI --}}
                                @if($canManageTasks)
                                    <td class="text-center py-4">
                                        <div class="flex justify-center gap-4">

                                            <a href="{{ route('tasks.edit', $task) }}" class="text-primary-600 hover:underline text-sm">
                                                Modifica
                                            </a>

                                            @if(auth()->user()->role === 'pi')
                                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Eliminare il task?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-red-600 hover:underline text-sm">
                                                        Elimina
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</x-app-layout>
