<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comunidad;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ComunidadController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:comunidades,nombre',
            Rule::unique('comunidades')->where(function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })
            ]);

        $comunidad = Comunidad::create([
            'nombre' => $request->nombre,
            'code' => strtoupper(Str::random(6)), 
            'user_id' => $request->user()->id, 
        ]);

        $comunidad->miembros()->attach($request->user()->id, ['status' => 'aceptado']);

        return response()->json([
            'success' => true,
            'mensaje' => 'Comunidad creada con éxito',
            'comunidad' => $comunidad
        ], 201);
    }

    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:comunidades,code'
        ]);

        $comunidad = Comunidad::where('code', $request->code)->first();
        $user = $request->user();

        if ($comunidad->miembros()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Ya eres miembro o tienes una solicitud pendiente en esta comunidad.'
            ], 400);
        }

        $comunidad->miembros()->attach($user->id, ['status' => 'pendiente']);

        return response()->json([
            'success' => true,
            'mensaje' => 'Solicitud enviada correctamente. Esperando a que el administrador te acepte.'
        ], 200);
    }

    public function gestionarMiembro(Request $request, $comunidad_id, $user_id)
    {
        $request->validate([
            'status' => 'required|in:aceptado,rechazado'
        ]);

        $comunidad = Comunidad::findOrFail($comunidad_id);

        if ($comunidad->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Solo el creador puede gestionar miembros.'], 403);
        }

        $comunidad->miembros()->updateExistingPivot($user_id, [
            'status' => $request->status
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Estado del usuario actualizado a ' . $request->status], 200);
    }

    public function eliminarMiembro(Request $request, $comunidad_id, $user_id)
    {
        $comunidad = Comunidad::findOrFail($comunidad_id);

        if ($comunidad->user_id !== $request->user()->id) {
            return $this->sendError('No tienes permisos', ['Solo el creador puede eliminar miembros.'], 403);
        }

        $comunidad->miembros()->detach($user_id);

        return $this->sendResponse(null, 'Miembro eliminado correctamente', 200);
    }

public function ranking(Request $request, $id)
    {
        $comunidad = Comunidad::findOrFail($id);

        $pertenece = $comunidad->miembros()
            ->where('user_id', $request->user()->id)
            ->where('comunidad_user.status', 'aceptado')
            ->exists();

        if (!$pertenece) {
            return response()->json(['error' => 'No tienes acceso a esta comunidad.'], 403);
        }

        $ranking = $comunidad->miembros()
            ->where('comunidad_user.status', 'aceptado')
            ->withSum('predicciones as total_puntos', 'puntos')
            ->orderBy('total_puntos', 'desc')
            ->select('users.id', 'users.name')
            ->get();

        return response()->json([
            'success' => true,
            'comunidad' => $comunidad->nombre,
            'ranking' => $ranking
        ], 200);
    }
}