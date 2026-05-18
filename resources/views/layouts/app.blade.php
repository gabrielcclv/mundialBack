<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundial 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/dashboard" class="text-2xl font-bold">🏆 Porra Mundial 2026</a>
            <div class="flex items-center gap-4">
                @auth
                    <span>Hola, <strong>{{ auth()->user()->name }}</strong></span>
                    <a href="/comunidades" class="hover:underline">Comunidades</a>
                    @if(auth()->user()->is_admin)
                        <a href="/admin/dashboard" class="bg-yellow-600 px-3 py-1 rounded text-xs font-bold">Panel Admin</a>
                    @endif
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 px-3 py-1 rounded text-sm hover:bg-red-700">Salir</button>
                    </form>
                @else
                    <a href="/login" class="hover:underline">Iniciar Sesión</a>
                    <a href="/register" class="bg-green-600 px-3 py-1 rounded hover:bg-green-700">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container mx-auto mt-8 p-4">
        @yield('content')
    </main>
</body>
</html>