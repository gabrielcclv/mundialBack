<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partido;
use App\Models\Comunidad;
use App\Models\User;
use App\Models\Prediccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\MailBienvenido;
use Illuminate\Support\Facades\Mail;

class ViewController extends Controller
{
    // --- AUTENTICACIÓN ---
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.']);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Mail::to($user->email)->send(new MailBienvenido($user, $request->password));
        Auth::login($user);
        return redirect('/dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // --- TORNEO Y PARTIDOS ---
    public function dashboard() {
        $partidos = Partido::orderBy('fecha_partido', 'asc')->get();
        return view('dashboard', compact('partidos'));
    }

    // --- COMUNIDADES ---
    public function comunidadesIndex() {
        $comunidades = auth()->user()->comunidadesMiembro()->where('status', 'aceptado')->get();
        return view('comunidades.index', compact('comunidades'));
    }

    public function comunidadesShow($id) {
        $comunidad = Comunidad::findOrFail($id);
        
        $ranking = $comunidad->miembros()
            ->where('comunidad_user.status', 'aceptado')
            ->withSum('predicciones as total_puntos', 'puntos')
            ->orderBy('total_puntos', 'desc')
            ->get();

        $solicitudesPendientes = $comunidad->miembros()
            ->where('comunidad_user.status', 'pendiente')
            ->get();

        return view('comunidades.show', compact('comunidad', 'ranking', 'solicitudesPendientes'));
    }

    public function storeComunidad(Request $request) {
        $request->validate(['nombre' => 'required|string|max:255']);
        $comunidad = Comunidad::create([
            'nombre' => $request->nombre,
            'code' => strtoupper(Str::random(6)),
            'user_id' => auth()->id()
        ]);
        $comunidad->miembros()->attach(auth()->id(), ['status' => 'aceptado']);
        return back();
    }

    public function joinComunidad(Request $request) {
        $request->validate(['code' => 'required|string']);
        $comunidad = Comunidad::where('code', strtoupper($request->code))->firstOrFail();
        if (!$comunidad->miembros()->where('user_id', auth()->id())->exists()) {
            $comunidad->miembros()->attach(auth()->id(), ['status' => 'pendiente']);
        }
        return back();
    }

    public function gestionarMiembro(Request $request, $comunidadId, $userId) {
        $comunidad = Comunidad::findOrFail($comunidadId);
        if ($comunidad->user_id === auth()->id()) {
            $comunidad->miembros()->updateExistingPivot($userId, ['status' => $request->status]);
        }
        return back();
    }

    // --- PREDICCIONES ---
    public function storePrediccion(Request $request) {
        $request->validate([
            'partido_id' => 'required|exists:partidos,id',
            'goles_local_predicho' => 'required|integer',
            'goles_visitante_predicho' => 'required|integer'
        ]);

        Prediccion::updateOrCreate(
            ['user_id' => auth()->id(), 'partido_id' => $request->partido_id],
            [
                'goles_local_predicho' => $request->goles_local_predicho,
                'goles_visitante_predicho' => $request->goles_visitante_predicho
            ]
        );
        return back();
    }

    // --- PANEL ADMIN ---
    public function adminDashboard() {
        if (!auth()->user()->is_admin) { abort(403); }
        $partidos = Partido::orderBy('fecha_partido', 'asc')->get();
        return view('admin.dashboard', compact('partidos'));
    }
}