@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Mis Comunidades</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($comunidades as $comunidad)
                <div class="bg-white p-5 rounded-lg shadow border-l-4 border-blue-800 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $comunidad->nombre }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Código: <span class="font-mono font-bold text-blue-700">{{ $comunidad->code }}</span></p>
                        <p class="text-xs text-gray-400 mt-2">Rol: {{ $comunidad->user_id === auth()->id() ? 'Creador' : 'Miembro' }}</p>
                    </div>
                    <div class="mt-4">
                        <a href="/comunidades/{{ $comunidad->id }}" class="block text-center bg-blue-800 text-white text-sm py-2 rounded hover:bg-blue-900 transition">
                            Ver Clasificación
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white p-6 rounded shadow text-center text-gray-500">
                    No perteneces a ninguna comunidad todavía.
                </div>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Unirse a una Liga</h2>
            <form action="/comunidades/join" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text" name="code" placeholder="Código de 6 dígitos" class="w-full p-2 border rounded uppercase text-center font-mono font-bold focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded hover:bg-green-700 transition">
                    Enviar Solicitud
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Nueva Liga</h2>
            <form action="/comunidades" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text" name="nombre" placeholder="Nombre de la comunidad" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-800 text-white font-bold py-2 rounded hover:bg-blue-900 transition">
                    Crear Liga
                </button>
            </form>
        </div>
    </div>

</div>
@endsection