<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
                {{ $group->name }}
            </h2>

            <a href="{{ route('projects.index') }}"
               class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700">
                Vai alla sezione progetti
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-6 space-y-8">

        {{-- Descrizione gruppo --}}
        @if($group->description)
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-secondary-900 mb-2">
                    Descrizione del gruppo
                </h3>
                <p class="text-secondary-700 text-sm">
                    {{ $group->description }}
                </p>
            </div>
        @endif

        {{-- GRID: Membri + Progetti --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- CARD: Membri --}}
            <div class="bg-white rounded-lg shadow flex flex-col h-[420px]">
                {{-- Header --}}
                <div class="p-6 border-b border-secondary-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-secondary-900">
                        Membri del gruppo
                    </h3>
                    <span class="text-sm text-secondary-600">
                        {{ $group->users->count() }}
                    </span>
                </div>

                {{-- Body scrollabile --}}
                <div class="p-6 overflow-y-auto flex-1">
                    @if($group->users->isEmpty())
                        <p class="text-sm text-secondary-600">
                            Nessun membro presente.
                        </p>
                    @else
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-secondary-200 text-left text-secondary-600">
                                    <th class="py-2">Nome</th>
                                    <th>Ruolo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->users as $user)
                                    <tr class="border-b border-secondary-100 last:border-0">
                                        <td class="py-3 font-medium text-secondary-900">
                                            {{ $user->name }}
                                        </td>
                                        <td class="text-secondary-700">
                                            {{ ucfirst($user->role) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- CARD: Progetti --}}
            <div class="bg-white rounded-lg shadow flex flex-col h-[420px]">
                {{-- Header --}}
                <div class="p-6 border-b border-secondary-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-secondary-900">
                        Progetti del gruppo
                    </h3>
                    <span class="text-sm text-secondary-600">
                        {{ $group->projects->count() }}
                    </span>
                </div>

                {{-- Body scrollabile --}}
                <div class="p-6 overflow-y-auto flex-1 space-y-3">
                    @if($group->projects->isEmpty())
                        <p class="text-sm text-secondary-600">
                            Nessun progetto associato al gruppo.
                        </p>
                    @else
                        @foreach($group->projects as $project)
                            <div class="border border-secondary-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="font-semibold text-secondary-900 hover:text-primary-700">
                                        {{ $project->title }}
                                    </a>

                                    <a href="{{ route('projects.show', $project) }}"
                                       class="text-secondary-400 hover:text-primary-600">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>

                                <div class="text-sm text-secondary-600 mt-1">
                                    Stato: {{ $project->status }}
                                </div>

                                <div class="text-sm text-secondary-600">
                                    Membri: {{ $project->users->count() }}
                                </div>

                                @if($project->description)
                                    <p class="text-sm text-secondary-700 mt-2">
                                        {{ \Illuminate\Support\Str::limit($project->description, 100) }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
