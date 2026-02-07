<x-app-layout>

    <div class="max-w-3xl mx-auto py-12 text-center">

        <h1 class="text-3xl py-12 font-bold text-orange-600 mb-6">
            401 - Non autorizzato
        </h1>

        <p class="text-gray-700 py-10 text-lg mb-8">
            Devi effettuare l'accesso per accedere a questa risorsa.
        </p>

        <a href="{{ route('login') }}"
           class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow
                  hover:bg-blue-700 transition">
            Vai al Login
        </a>

    </div>

</x-app-layout>
