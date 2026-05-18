<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\MailBienvenido;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors(), 422);
        }

        $passwordPlana = $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($passwordPlana),
        ]);

        Mail::to($user->email)->send(new MailBienvenido($user, $passwordPlana));

        $token = $user->createToken('mundial_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user,
            'token' => $token
        ], 'Usuario registrado correctamente', 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Las credenciales son incorrectas.'
            ], 401); // 401 = Unauthorized
        }

        $user->tokens()->delete();

        $token = $user->createToken('mundial_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'mensaje' => 'Has iniciado sesión con éxito',
            'user' => $user,
            'token' => $token
        ], 200); // 200 = OK
    }
}
