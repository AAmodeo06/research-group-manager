<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-secondary-900 leading-tight">
                Gestione Gruppo: {{ $group->name }}
            </h2>
            <div class="flex gap-4">
                <a href="{{ route('group.edit') }}" class="inline-flex items-center px-4 py-2 bg-secondary-200 text-secondary-800 text-sm font-medium rounded hover:bg-secondary-300">
                    Modifica Gruppo
                </a>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700">
                    I miei progetti &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-6 space-y-8">
        
        {{-- Notifiche di successo o errore --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Descrizione del Gruppo --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-secondary-700 uppercase mb-2">Informazioni</h3>
            <p class="text-secondary-900">{{ $group->description ?? 'Nessuna descrizione disponibile per questo gruppo.' }}</p>
        </div>

        {{-- CARD: Gestione Membri --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-secondary-200 flex items-center justify-between bg-secondary-50">
                <h3 class="text-lg font-semibold text-secondary-900">Membri del team</h3>
                <span class="bg-primary-100 text-primary-800 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $group->users->count() }} utenti
                </span>
            </div>

            {{-- Form Aggiunta Membro --}}
            <div class="p-6 border-b border-secondary-200">
                <form action="{{ route('groups.members.add') }}" method="POST" class="flex flex-col md:flex-row gap-4">
                    @csrf
                    <div class="flex-1">
                        <select name="user_id" required class="w-full rounded-md border-secondary-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            <option value="">Seleziona un utente senza gruppo da aggiungere...</option>
                            @foreach($availableUsers as $available)
                                <option value="{{ $available->id }}">{{ $available->name }} ({{ $available->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md text-sm font-semibold hover:bg-primary-700 transition">
                        Aggiungi al Gruppo
                    </button>
                </form>
            </div>

            {{-- Tabella Membri --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-secondary-500">
                    <thead class="text-xs text-secondary-700 uppercase bg-secondary-50">
                        <tr>
                            <th class="px-6 py-3">Nome</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Ruolo</th>
                            <th class="px-6 py-3 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        @foreach($group->users as $member)
                            <tr class="bg-white hover:bg-secondary-50 transition">
                                <td class="px-6 py-4 font-medium text-secondary-900">{{ $member->name }}</td>
                                <td class="px-6 py-4">{{ $member->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md text-xs font-medium {{ $member->global_role === 'pi' ? 'bg-purple-100 text-purple-800' : 'bg-secondary-100 text-secondary-800' }}">
                                        {{ $member->roleLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($member->id !== auth()->id())
                                        <form action="{{ route('groups.members.remove', $member) }}" method="POST" onsubmit="return confirm('Rimuovere l\'utente dal gruppo? Non potrà più accedere ai progetti del gruppo.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">
                                                RIMUOVI
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-secondary-400 text-xs italic">Tu</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>