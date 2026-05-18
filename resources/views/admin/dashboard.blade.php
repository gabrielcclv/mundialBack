@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6">Panel de Administración del Mundial</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Actualizar Resultados Reales</h2>
        
        <div class="space-y-4">
            @foreach ($partidos as $partido)
                <div class="border p-4 rounded-lg flex flex-col md:flex-row justify-between items-center bg-gray-50">
                    <div class="mb-3 md:mb-0 text-center md:text-left">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-blue-100 text-blue-800 uppercase">{{ $partido->fase }}</span>
                        <div class="font-bold text-lg mt-1">{{ $partido->equipo_local }} vs {{ $partido->equipo_visitante }}</div>
                        <div class="text-xs text-gray-400">{{ $partido->fecha_partido }}</div>
                    </div>

                    <div>
                        @if ($partido->estado === 'pendiente')
                            <form action="/partidos/{{ $partido->id }}/resultado" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="goles_local" class="w-14 p-1 border text-center rounded font-bold" required>
                                <span class="font-bold">-</span>
                                <input type="number" name="goles_visitante" class="w-14 p-1 border text-center rounded font-bold" required>
                                <button type="submit" class="bg-blue-800 text-white px-3 py-1 text-sm rounded hover:bg-blue-900 transition">
                                    Cerrar Partido
                                </button>
                            </form>
                        @else
                            <div class="text-sm font-bold text-green-700 bg-green-100 px-3 py-1 rounded text-center">
                                Finalizado: {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow h-fit">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Añadir Nuevo Partido</h2>
        
        <form action="/partidos" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Equipo Local</label>
                <input type="text" name="equipo_local" class="w-full p-2 border rounded" required placeholder="Ej: España">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Equipo Visitante</label>
                <input type="text" name="equipo_visitante" class="w-full p-2 border rounded" required placeholder="Ej: Argentina">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Fecha y Hora (Saque Inicial)</label>
                <input type="datetime-local" name="fecha_partido" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Fase del Torneo</label>
                <select name="fase" class="w-full p-2 border rounded" required>
                    <option value="Grupos">Fase de Grupos</option>
                    <option value="Octavos">Octavos de Final</option>
                    <option value="Cuartos">Cuartos de Final</option>
                    <option value="Semifinal">Semifinales</option>
                    <option value="Final">Final</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded hover:bg-green-700 transition">
                Guardar Partido
            </button>
        </form>
    </div>

</div>
@endsection