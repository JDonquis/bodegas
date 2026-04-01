<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-on-surface font-body min-h-screen flex items-center justify-center p-6">

    @include('layout.toast')

    <div class="w-full max-w-5xl bg-surface-container-lowest rounded-[2.5rem] shadow-2xl shadow-primary/5 overflow-hidden flex flex-col md:flex-row min-h-[600px] border border-outline-variant/30">
        
        <!-- Left Side: Branding -->
        <div class="w-full md:w-1/2 bg-primary p-12 flex flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-secondary/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-primary-container/40 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <span class="text-3xl font-extrabold tracking-tighter">{{ config('app.name') }}</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold font-headline leading-tight mb-6">
                    Seguridad y <span class="text-secondary-fixed">Recuperación</span>
                </h1>
                <p class="text-on-primary-container text-lg max-w-sm font-medium">
                    Proceso de validación mediante Master Password para el restablecimiento de credenciales.
                </p>
            </div>

            <div class="relative z-10 text-xs text-on-primary-container/60 font-medium">
                © 2026 {{ config('app.name') }}. Todos los derechos reservados.
            </div>
        </div>

        <!-- Right Side: Validation Form -->
        <div class="w-full md:w-1/2 p-12 lg:p-20 flex flex-col justify-center bg-surface">
            <div class="mb-10">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-secondary font-bold text-sm mb-6 hover:translate-x-[-4px] transition-transform">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Volver al login
                </a>
                <h2 class="text-3xl font-extrabold text-primary font-headline mb-2">Recuperar Acceso</h2>
                <p class="text-on-surface-variant font-medium">Valida tu identidad con la Master Password</p>
            </div>

            <form method="POST" action="{{ route('password.validate') }}" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label for="ci" class="text-sm font-bold text-primary ml-1">Tu Cédula</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">badge</span>
                        <input type="text" name="ci" id="ci" required autofocus
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                            placeholder="Ej: 12345678">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="master_password" class="text-sm font-bold text-primary ml-1">Master Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">vpn_key</span>
                        <input type="password" name="master_password" id="master_password" required
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                            placeholder="Ingrese la clave maestra">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary hover:bg-primary-container text-white font-bold font-headline py-4 rounded-2xl shadow-lg shadow-primary/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>Validar Maestro</span>
                        <span class="material-symbols-outlined text-xl">verified_user</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
