<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 bg-[radial-gradient(circle_at_top,#1e293b,transparent_55%)] text-slate-100">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4">
        <div class="grid w-full overflow-hidden rounded-3xl bg-white shadow-2xl lg:grid-cols-[1.1fr_0.9fr]">
            <section class="hidden bg-slate-900 p-12 text-white lg:block">
                <p class="mb-4 text-sm uppercase tracking-[0.3em] text-cyan-300">TechStore</p>
                <h1 class="text-4xl font-bold leading-tight">Bienvenido a TechStore</h1>
                <p class="mt-6 text-slate-300">Tu tienda online de tecnologia de confianza.</p>
            </section>

            <section class="p-8 sm:p-12">
                <h2 class="text-3xl font-bold text-slate-900">Iniciar sesion</h2>

                <div class="mt-6">
                    @include('partials.flash')
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label for="correo" class="mb-2 block text-sm font-semibold text-slate-700">Correo</label>
                        <input id="correo" name="correo" type="email" value="{{ old('correo') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-slate-900 focus:outline-none" required autofocus>
                    </div>
                    <div>
                        <label for="clave" class="mb-2 block text-sm font-semibold text-slate-700">Clave</label>
                        <input id="clave" name="clave" type="password" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-slate-900 focus:outline-none" required>
                    </div>
                    <button type="submit" class="w-full rounded-xl px-4 py-3 text-sm font-semibold transition hover:bg-cyan-600" style="background-color: #0f172a; color: #ffffff; border: 1px solid #0f172a;">
                        Entrar
                    </button>
                </form>

                <p class="mt-6 text-sm text-slate-600">
                    No tienes cuenta?
                    <a href="{{ route('register') }}" class="font-semibold text-cyan-700 hover:text-cyan-900">Registrate aqui</a>
                </p>
            </section>
        </div>
    </div>
</body>
</html>
