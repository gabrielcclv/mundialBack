<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partido;

class PartidoController extends Controller
{

    public function index()
    {
        $partidos = Partido::all();
        return response()->json(['success' => true, 'partidos' => $partidos], 200);
    }


    public function store(Request $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['error' => 'No tienes permisos de Administrador'], 403);
        }

        $request->validate([
            'equipo_local' => 'required|string',
            'equipo_visitante' => 'required|string',
            'fecha_partido' => 'required|date',
            'fase' => 'required|string',
        ]);

        $partido = Partido::create($request->all());

        return response()->json(['success' => true, 'partido' => $partido], 201);
    }


    public function updateResultado(Request $request, $id)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['error' => 'No tienes permisos de Administrador'], 403);
        }

        $request->validate([
            'goles_local' => 'required|integer',
            'goles_visitante' => 'required|integer',
        ]);

        $partido = Partido::findOrFail($id);
        
        $partido->update([
            'goles_local' => $request->goles_local,
            'goles_visitante' => $request->goles_visitante,
            'estado' => 'finalizado'
        ]);

        $predicciones = $partido->predicciones; 

        foreach ($predicciones as $prediccion) {
            $puntos = 0;
            
            $realLocal = $partido->goles_local;
            $realVisita = $partido->goles_visitante;
            $predLocal = $prediccion->goles_local_predicho;
            $predVisita = $prediccion->goles_visitante_predicho;

            if ($realLocal === $predLocal && $realVisita === $predVisita) {
                $puntos = 3;
            } 
            else {
                $ganoLocalReal = $realLocal > $realVisita;
                $ganoVisitaReal = $realLocal < $realVisita;
                $empateReal = $realLocal === $realVisita;

                $ganoLocalPred = $predLocal > $predVisita;
                $ganoVisitaPred = $predLocal < $predVisita;
                $empatePred = $predLocal === $predVisita;

                if (
                    ($ganoLocalReal && $ganoLocalPred) || 
                    ($ganoVisitaReal && $ganoVisitaPred) || 
                    ($empateReal && $empatePred)
                ) {
                    $puntos = 1;
                }
            }

            $prediccion->update(['puntos' => $puntos]);
        }

        return response()->json([
            'success' => true, 
            'mensaje' => 'Resultado actualizado y puntos repartidos correctamente.',
            'partido' => $partido
        ], 200);
    }

    public function importar(Request $request)
    {
        if (!$request->user()->is_admin) {
            return $this->sendError('Acceso denegado', ['No eres administrador'], 403);
        }

        $request->validate([
            'fichero' => 'required|file|mimetypes:application/json'
        ]);

        $contenido = file_get_contents($request->file('fichero')->getRealPath());
        $partidos = json_decode($contenido, true);

        if (!$partidos) {
            return $this->sendError('Formato inválido', ['El fichero no es un JSON válido'], 400);
        }

        $insertados = [];
        foreach ($partidos as $p) {
            $insertados[] = Partido::create([
                'equipo_local' => $p['equipo_local'],
                'equipo_visitante' => $p['equipo_visitante'],
                'fecha_partido' => $p['fecha_partido'],
                'fase' => $p['fase'],
                'estado' => 'pendiente'
            ]);
        }

        return $this->sendResponse($insertados, count($insertados) . ' partidos importados correctamente', 201);
    }
}