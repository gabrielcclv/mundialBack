@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 border rounded shadow-lg mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-800">Iniciar Sesión</h2>
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="/login" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2 text-sm">Correo Electrónico</label>
            <input type="email" name="email" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2 text-sm">Contraseña</label>
            <input type="password" name="password" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" required>
        </div>
        <button type="submit" class="w-full bg-blue-800 text-white font-bold py-2 rounded hover:bg-blue-900 transition">Entrar</button>
    </form>
</div>
@endsection