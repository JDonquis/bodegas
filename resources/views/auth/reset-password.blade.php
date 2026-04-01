<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface text-on-surface font-body min-h-screen flex items-center justify-center p-6">

    @include('layout.toast')

    <div
        class="w-full max-w-5xl bg-surface-container-lowest rounded-[2.5rem] shadow-2xl shadow-primary/5 overflow-hidden flex flex-col md:flex-row min-h-[600px] border border-outline-variant/30">

        <!-- Left Side: Branding -->
        <div
            class="w-full md:w-1/2 bg-secondary p-12 flex flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-primary-container/40 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <span class="text-3xl font-extrabold tracking-tighter">{{ config('app.name') }}</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold font-headline leading-tight mb-6">
                    Nueva <span class="text-primary-fixed">Contraseña</span>
                </h1>
                <p class="text-on-secondary-container text-lg max-w-sm font-medium">
                    Actualiza tus credenciales para recuperar el acceso al sistema.
                </p>
            </div>

            <div class="relative z-10 text-xs text-on-secondary-container/60 font-medium">
                © 2026 {{ config('app.name') }}. Todos los derechos reservados.
            </div>
        </div>

        <!-- Right Side: Reset Form -->
        <div class="w-full md:w-1/2 p-12 lg:p-20 flex flex-col justify-center bg-surface">
            <div class="mb-10">
                <h2 class="text-3xl font-extrabold text-primary font-headline mb-2">Restablecer</h2>
                <p class="text-on-surface-variant font-medium">Establece tu nueva clave de acceso</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="ci" value="{{ $ci }}">

                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-primary ml-1">Nueva Contraseña</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">lock_reset</span>
                        <input type="password" name="password" id="password" required autofocus
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                            placeholder="Mínimo 6 caracteres">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-bold text-primary ml-1">Confirmar
                        Contraseña</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">lock</span>

                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                            placeholder="Repite tu nueva contraseña">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-secondary hover:bg-on-secondary-container text-white font-bold font-headline py-4 rounded-2xl shadow-lg shadow-secondary/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>Actualizar Contraseña</span>
                        <span class="material-symbols-outlined text-xl">save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
