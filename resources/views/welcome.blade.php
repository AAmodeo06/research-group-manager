@extends('layouts.public')

@section('content')

<main class="max-w-7xl mx-auto px-6 py-12 lg:py-20 flex flex-col gap-16">

    {{-- HERO --}}
    <section class="max-w-3xl">
        <h1 class="text-5xl lg:text-6xl font-extrabold text-secondary-900 leading-tight mb-6">
            Evolvi il modo di fare <br>
            <span class="text-primary-600 italic">Ricerca Scientifica</span>
        </h1>

        <p class="text-xl text-secondary-600 mb-8">
            Una piattaforma per organizzare progetti, persone e pubblicazioni
            all’interno di gruppi di ricerca universitari.
        </p>

        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('register') }}"
                class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold
                    text-white bg-primary-600 rounded-lg
                    transition-color hover:text-white">
                Registrati
            </a>

            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold
                    text-primary-700 border border-primary-200 rounded-lg
                    hover:bg-primary-50 transition">
                Accedi
            </a>
        </div>
    </section>

    {{-- RUOLI --}}
    <section>
        <h2 class="text-2xl font-bold text-secondary-900 mb-6">
            Ruoli del sistema
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- PI --}}
            <div class="bg-white border border-secondary-200 rounded-xl p-6">
                <svg class="w-8 h-8 text-primary-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 4h8l-1 1v5l5 5c1.26 1.26.37 3.41-1.41 3.41H4.83c-1.78 0-2.67-2.15-1.41-3.41l5-5V5l-1-1z"/>
                </svg>
                <h3 class="font-semibold text-lg mb-2">Principal Investigator</h3>
                <p class="text-sm text-secondary-600">
                    Responsabile scientifico del gruppo e dei progetti.
                </p>
            </div>

            {{-- PM --}}
            <div class="bg-white border border-secondary-200 rounded-xl p-6">
                <svg class="w-8 h-8 text-indigo-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5h6v14H9z"/>
                </svg>
                <h3 class="font-semibold text-lg mb-2">Project Manager</h3>
                <p class="text-sm text-secondary-600">
                    Coordina attività, milestone e scadenze.
                </p>
            </div>

            {{-- Researcher --}}
            <div class="bg-white border border-secondary-200 rounded-xl p-6">
                <svg class="w-8 h-8 text-emerald-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v13M4 6c4-2 12-2 16 0"/>
                </svg>
                <h3 class="font-semibold text-lg mb-2">Researcher</h3>
                <p class="text-sm text-secondary-600">
                    Contribuisce alla ricerca e alle pubblicazioni.
                </p>
            </div>

            {{-- Collaborator --}}
            <div class="bg-white border border-secondary-200 rounded-xl p-6">
                <svg class="w-8 h-8 text-amber-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5-3M7 20H2v-2a3 3 0 015-3m5-8a3 3 0 110-6 3 3 0 010 6z"/>
                </svg>
                <h3 class="font-semibold text-lg mb-2">Collaborator</h3>
                <p class="text-sm text-secondary-600">
                    Accesso limitato per supporto esterno.
                </p>
            </div>

        </div>
    </section>

</main>

@endsection
