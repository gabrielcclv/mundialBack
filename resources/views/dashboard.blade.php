@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Partidos del Mundial</h1>
    <a href="/comunidades" class="bg-blue-800 text-white px-4 py-2 rounded shadow hover:bg-blue-900 transition">
        Ver mis Comunidades
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    @forelse ($partidos as $partido)
        <div class="bg-white p-5 rounded-lg shadow border-t-4 border-blue-800">
            <div class="text-sm text-gray-500 mb-2 font-bold">{{ $partido->fase }}</div>
            <div class="text-xs text-gray-400 mb-4">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y H:i') }}</div>
            
            <div class="flex justify-between items-center font-bold text-lg mb-4">
                <span class="w-1/3 text-right">{{ $partido->equipo_local }}</span>
                <span class="w-1/3 text-center text-gray-400 text-sm">vs</span>
                <span class="w-1/3 text-left">{{ $partido->equipo_visitante }}</span>
            </div>

            @if ($partido->estado === 'pendiente')
                <form action="/predicciones" method="POST" class="flex justify-center items-center gap-2">
                    @csrf
                    <input type="hidden" name="partido_id" value="{{ $partido->id }}">
                    <input type="number" name="goles_local_predicho" class="w-16 p-2 border text-center rounded focus:ring-2 focus:ring-blue-500" placeholder="0" required min="0">
                    <span class="font-bold">-</span>
                    <input type="number" name="goles_visitante_predicho" class="w-16 p-2 border text-center rounded focus:ring-2 focus:ring-blue-500" placeholder="0" required min="0">
                    <button type="submit" class="bg-green-600 text-white px-3 py-2 rounded font-bold hover:bg-green-700 transition">Guardar</button>
                </form>
            @else
                <div class="text-center font-bold text-lg text-green-700 bg-green-100 py-2 rounded">
                    Resultado Final: {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                </div>
            @endif
        </div>
    @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center bg-white p-10 rounded shadow text-gray-500">
            Aún no hay partidos registrados en el sistema. Dile al Administrador que importe el fichero JSON.
        </div>
    @endforelse

</div>
@endsection