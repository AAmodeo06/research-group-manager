<x-app-layout>

    <div class="max-w-3xl mx-auto py-12 text-center">

        <h1 class="text-3xl py-12 font-bold text-yellow-600 mb-6">
            419 - Sessione scaduta
        </h1>

        <p class="text-gray-700 py-10 text-lg mb-8">
            La tua sessione è scaduta. Ricarica la pagina e riprova.
        </p>

        <a href="{{ url()->previous() }}"
           class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow
                  hover:bg-blue-700 transition">
            Torna indietro
        </a>

    </div>

</x-app-layout>
