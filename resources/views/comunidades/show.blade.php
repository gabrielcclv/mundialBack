@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $comunidad->nombre }}</h1>
        <p class="text-gray-500">Código de invitación: <span class="font-mono font-bold text-blue-800">{{ $comunidad->code }}</span></p>
    </div>
    <a href="/comunidades" class="text-sm text-blue-800 hover:underline mt-2 md:mt-0">&larr; Volver a mis comunidades</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-blue-800 text-white px-6 py-4 font-bold text-lg">
            📊 Clasificación General
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 uppercase text-xs font-bold border-b">
                    <th class="px-6 py-3 w-16 text-center">Pos</th>
                    <th class="px-6 py-3">Usuario</th>
                    <th class="px-6 py-3 text-center">Puntos</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-700">
                @foreach ($ranking as $index => $miembro)
                    <tr class="{{ auth()->id() === $miembro->id ? 'bg-yellow-50 font-semibold' : '' }}">
                        <td class="px-6 py-4 text-center font-bold">
                            @if($index == 0) 🥇 @elseif($index == 1) 🥈 @elseif($index == 2) 🥉 @else {{ $index + 1 }} @endif
                        </td>
                        <td class="px-6 py-4">{{ $miembro->name }}</td>
                        <td class="px-6 py-4 text-center font-bold text-blue-800">{{ $miembro->total_puntos ?? 0 }} pts</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="space-y-6">
        @if ($comunidad->user_id === auth()->id())
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-800 text-white px-4 py-3 font-bold text-sm">
                    🔔 Solicitudes Pendientes
                </div>
                <div class="p-4 divide-y divide-gray-100">
                    @forelse ($solicitudesPendientes as $solicitud)
                        <div class="py-3 flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-800">{{ $solicitud->name }}</span>
                            <div class="flex gap-2">
                                <form action="/comunidades/{{ $comunidad->id }}/miembros/{{ $solicitud->id }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="aceptado">
                                    <button type="submit" class="bg-green-500 text-white text-xs px-2 py-1 rounded hover:bg-green-600">Aceptar</button>
                                </form>
                                <form action="/comunidades/{{ $comunidad->id }}/miembros/{{ $solicitud->id }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rechazado">
                                    <button type="submit" class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600">Rechazar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No hay solicitudes pendientes.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

</div>
@endsection