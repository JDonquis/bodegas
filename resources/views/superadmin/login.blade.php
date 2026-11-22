<!DOCTYPE html>
<html class="h-full bg-brand-navy" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Superadministrador | MiBodega SaaS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-full bg-brand-navy font-jakarta antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-brand-accent ring-1 ring-white/15">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                        <path d="M12 3l7 3v6c0 5-4 8-7 9-3-1-7-4-7-9V6l7-3z"></path>
                        <circle cx="12" cy="10" r="2"></circle>
                        <path d="M8.5 16a3.5 3.5 0 017 0"></path>
                    </svg>
                </div>
                <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-white">Panel Superadministrador</h1>
                <p class="mt-1 text-xs font-medium text-slate-300">Acceso central para gestionar las bodegas SaaS.</p>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-2xl">
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('superadmin.login.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email"
                            class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Correo
                            electrónico</label>
                        <div class="relative flex items-center">
                            <svg class="absolute left-3.5 h-[18px] w-[18px] text-slate-400" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="m3 7 9 6 9-6"></path>
                            </svg>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>
                    </div>

                    <div>
                        <label for="password"
                            class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Contraseña</label>
                        <div class="relative flex items-center">
                            <svg class="absolute left-3.5 h-[18px] w-[18px] text-slate-400" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                                <path d="M8 10V7a4 4 0 018 0v3"></path>
                            </svg>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-accent">
                        Recordar sesión
                    </label>

                    <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-navy px-4 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-brand-btn transition active:scale-[0.98]">
                        <svg class="h-[18px] w-[18px] text-brand-accent" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Entrar
                    </button>
                </form>
            </div>

            <p class="mt-5 text-center text-[11px] text-slate-400">MiBodega SaaS · Acceso restringido</p>
        </div>
    </div>
</body>

</html>
