<x-app-layout>

    <div class="max-w-3xl mx-auto py-12 text-center">

        <h1 class="text-3xl py-12 font-bold text-red-600 mb-6">
            404 - Pagina non trovata
        </h1>

        <p class="text-gray-700 py-10 text-lg mb-8">
            La pagina che stai cercando non esiste o è stata rimossa.
        </p>

        <a href="{{ route('dashboard') }}"
           class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow
                  hover:bg-blue-700 transition">
            Torna alla Dashboard
        </a>

    </div>

</x-app-layout>
