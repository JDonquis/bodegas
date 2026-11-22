<!DOCTYPE html>

<html class="h-full bg-slate-900" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover"
        name="viewport" />
    <title>Recuperar Contraseña | {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        input {
            user-select: auto;
        }
    </style>
</head>

<body
    class="min-h-full flex items-center justify-center p-0 sm:p-4 bg-slate-900 font-jakarta text-slate-800 antialiased selection:bg-teal-500 selection:text-white">

    @include('layout.toast')

    <div class="w-full max-w-[420px] min-h-screen sm:min-h-[860px] sm:max-h-[920px] sm:rounded-[44px] bg-brand-navy overflow-y-auto overflow-x-hidden flex flex-col justify-between shadow-2xl relative border-0 sm:border-8 sm:border-slate-800">

        <header class="relative px-7 pb-8 text-white z-10 pt-[max(1.75rem,env(safe-area-inset-top))]">
            <div aria-hidden="true"
                class="absolute -top-24 -right-24 w-60 h-60 bg-teal-500/15 rounded-full blur-3xl pointer-events-none">
            </div>
            <div aria-hidden="true"
                class="absolute top-28 -left-20 w-48 h-48 bg-blue-600/10 rounded-full blur-2xl pointer-events-none">
            </div>

            <div class="flex items-center justify-between mb-8">
                <a href="{{ route('login') }}" class="flex items-center space-x-2.5">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-400 to-emerald-300 flex items-center justify-center shadow-lg shadow-teal-500/20 text-brand-navy">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2.2" viewbox="0 0 24 24">
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" x2="12" y1="22.08" y2="12"></line>
                        </svg>
                    </div>
                    <div>
                        <span
                            class="text-2xl font-extrabold tracking-tight text-white inline-block">{{ config('app.name') }}</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-teal-400 ml-0.5 align-baseline"></span>
                    </div>
                </a>

                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-400"></span>
                    </span>
                    <span class="text-[11px] font-semibold text-teal-300 uppercase tracking-wider">Recuperación</span>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight leading-tight mb-3">
                Recupera el acceso de forma <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 via-emerald-300 to-teal-200">segura</span>
            </h1>

            <p class="text-slate-300 text-sm leading-relaxed max-w-[340px] font-normal">
                Valida tu identidad con la Master Password para restablecer las credenciales de tu cuenta.
            </p>
        </header>

        <main
            class="bg-white rounded-t-[34px] px-6 sm:px-7 pt-7 pb-[max(1.5rem,env(safe-area-inset-bottom))] shadow-card relative z-20 flex-1 flex flex-col justify-between">

            <div aria-hidden="true" class="w-12 h-1 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>

            <div>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-1.5 text-teal-600 hover:text-teal-700 font-semibold text-xs mb-5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                        <line x1="19" x2="5" y1="12" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Volver al login
                </a>

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-brand-navy tracking-tight">Recuperar Acceso</h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Valida tu identidad con la Master Password</p>
                </div>

                <form class="space-y-4" method="POST" action="{{ route('password.validate') }}">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="ci">
                            Tu Cédula
                        </label>
                        <div
                            class="relative rounded-2xl bg-slate-50 border border-slate-200 focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/20 transition-all duration-150 @error('ci') border-red-400 @enderror">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                    <rect height="16" rx="3" ry="3" width="18" x="3" y="4"></rect>
                                    <circle cx="9" cy="10" r="2"></circle>
                                    <line x1="15" x2="17" y1="8" y2="8"></line>
                                    <line x1="15" x2="17" y1="12" y2="12"></line>
                                    <line x1="7" x2="17" y1="16" y2="16"></line>
                                </svg>
                            </div>
                            <input
                                class="block w-full pl-11 pr-4 py-3.5 text-sm bg-transparent border-0 rounded-2xl text-slate-900 placeholder:text-slate-400 font-medium focus:ring-0 focus:outline-none"
                                id="ci" inputmode="numeric" name="ci" placeholder="Ej: 12345678" required=""
                                type="text" value="{{ old('ci') }}" autofocus autocomplete="username" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700"
                            for="master_password">
                            Master Password
                        </label>
                        <div
                            class="relative rounded-2xl bg-slate-50 border border-slate-200 focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/20 transition-all duration-150 @error('master_password') border-red-400 @enderror">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                    <circle cx="7.5" cy="15.5" r="5.5"></circle>
                                    <path d="m21 2-9.6 9.6"></path>
                                    <path d="m15.5 7.5 3 3L22 7l-3-3"></path>
                                </svg>
                            </div>
                            <input
                                class="block w-full pl-11 pr-11 py-3.5 text-sm bg-transparent border-0 rounded-2xl text-slate-900 placeholder:text-slate-400 font-medium tracking-wide focus:ring-0 focus:outline-none"
                                id="master_password" name="master_password" placeholder="Ingrese la clave maestra"
                                required="" type="password" autocomplete="off" />
                            <button aria-label="Alternar visibilidad de contraseña"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                id="togglePassword" type="button">
                                <svg class="w-5 h-5" fill="none" id="eyeIcon" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 pt-1 text-[11px] text-slate-400">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewbox="0 0 20 20">
                            <path clip-rule="evenodd"
                                d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                fill-rule="evenodd"></path>
                        </svg>
                        Validación protegida con cifrado SSL 256-bit
                    </div>

                    <div class="pt-2">
                        <button
                            class="w-full h-12 bg-brand-navy hover:bg-brand-navy-light active:scale-[0.99] text-white font-semibold rounded-2xl flex items-center justify-center gap-2.5 shadow-elevated transition duration-200 ease-out"
                            type="submit">
                            <span>Validar Master Password</span>
                            <svg class="w-5 h-5 text-teal-300" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2.5" viewbox="0 0 24 24">
                                <path
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <footer class="mt-8 pt-4 border-t border-slate-100 text-center space-y-2">
                <p class="text-xs text-slate-500 font-medium">
                    ¿Problemas para acceder?
                    <a class="text-teal-600 font-semibold hover:underline"
                        href="mailto:juandonquis07@gmail.com">juandonquis07@gmail.com</a>
                </p>
                <p class="text-[11px] text-slate-400">
                    © 2026 {{ config('app.name') }}. Todos los derechos reservados.
                </p>
            </footer>
        </main>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('master_password');
        const eyeIcon = document.getElementById('eyeIcon');

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            if (isPassword) {
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        });
    </script>
</body>

</html>
