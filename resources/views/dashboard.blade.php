{{-- Realizzato da Luigi La Gioia --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="bg-secondary-200 min-h-[calc(100vh-72px)]">
        <div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

            {{-- KPI --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @if($user->global_role === 'pi')
                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-secondary-500">Progetti</p>
                        <p class="text-2xl font-semibold">{{ $projects->count() }}</p>
                    </div>
                @endif

                @if($user->global_role !== 'collaborator')
                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-secondary-500">Task totali</p>
                        <p class="text-2xl font-semibold">{{ $taskStats['total'] }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-secondary-500">Completati</p>
                        <p class="text-2xl font-semibold text-green-600">
                            {{ $taskStats['completed'] }}
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-secondary-500">In ritardo</p>
                        <p class="text-2xl font-semibold text-red-600">
                            {{ $taskStats['overdue'] }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- CONTENUTO PRINCIPALE --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- GRAFICO --}}
                @if($user->global_role !== 'collaborator' && $taskStats['total'] >= 3)
                    <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                        <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-4">
                            Stato dei task
                        </h3>

                        <div class="relative flex-1 min-h-[260px]">
                            <canvas id="taskChart"></canvas>
                        </div>
                    </div>
                @endif

                {{-- TASK IMMINENTI --}}
                <div class="bg-white rounded-lg shadow p-6 flex flex-col
                    {{ ($user->global_role !== 'collaborator' && $taskStats['total'] >= 3) ? 'lg:col-span-2' : 'lg:col-span-3' }}">

                    <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-4">
                        Task imminenti
                    </h3>

                    @if($tasks->isEmpty())
                        <p class="text-sm text-secondary-500 mt-8 text-center">
                            Nessun task assegnato.
                        </p>
                    @else
                        <ul class="divide-y overflow-y-auto pr-2" style="max-height: 320px;">
                            @foreach(
                                $tasks->whereNotNull('due_date')->sortBy('due_date')->take(8)
                                as $task
                            )
                                <li class="py-4 flex items-center justify-between gap-6">

                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-secondary-900">
                                            {{ $task->title }}
                                        </p>
                                        <p class="text-xs text-secondary-500 mt-1">
                                            Scadenza:
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    <div class="w-40 flex justify-end">
                                        <span class="text-xs font-medium px-3 py-1 rounded-full
                                            @if($task->status === 'completed') bg-green-100 text-green-700
                                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                                            @elseif($task->status === 'open') bg-gray-100 text-gray-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ ucfirst(str_replace('_',' ', $task->status)) }}
                                        </span>
                                    </div>

                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @if($user->global_role !== 'collaborator' && $taskStats['total'] >= 3)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('taskChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Completati', 'In corso', 'Aperti', 'In ritardo'],
                    datasets: [{
                        data: [
                            {{ $taskStats['completed'] }},
                            {{ $taskStats['in_progress'] }},
                            {{ $taskStats['total'] - $taskStats['completed'] - $taskStats['in_progress'] - $taskStats['overdue'] }},
                            {{ $taskStats['overdue'] }}
                        ],
                        backgroundColor: ['#22c55e','#3b82f6','#e5e7eb','#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12 }
                        }
                    },
                    maintainAspectRatio: false
                }
            });
        </script>
    @endif
</x-app-layout>
