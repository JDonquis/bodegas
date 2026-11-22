<!DOCTYPE html>

<html class="h-full bg-slate-900" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover"
        name="viewport" />
    <title>Iniciar Sesión | {{ config('app.name') }}</title>
    <meta name="theme-color" content="#0b1e36">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <script>
        window.addEventListener('beforeinstallprompt', function(event) {
            event.preventDefault();
            window.__pwaPrompt = event;
        });
    </script>

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
                <div class="flex items-center space-x-2.5">
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
                </div>

                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-400"></span>
                    </span>
                    <span class="text-[11px] font-semibold text-teal-300 uppercase tracking-wider">v2.4 Seguro</span>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight leading-tight mb-3">
                Gestiona tu inventario con <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 via-emerald-300 to-teal-200">precisión</span>
            </h1>

            <p class="text-slate-300 text-sm leading-relaxed max-w-[340px] font-normal">
                Acceso centralizado para el control de suministros y gestión de dispensarios médicos y almacenes.
            </p>
        </header>

        <main
            class="bg-white rounded-t-[34px] px-6 sm:px-7 pt-7 pb-[max(1.5rem,env(safe-area-inset-bottom))] shadow-card relative z-20 flex-1 flex flex-col justify-between">

            <div aria-hidden="true" class="w-12 h-1 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>

            <div>
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-brand-navy tracking-tight">Bienvenido</h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Ingresa tus credenciales para continuar</p>
                </div>

                <form class="space-y-4" method="POST" action="{{ route('login-post') }}">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="ci">
                            Cédula de Identidad
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
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700"
                                for="password">
                                Contraseña
                            </label>
                            <a class="text-xs font-semibold text-teal-600 hover:text-teal-700 transition-colors"
                                href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div
                            class="relative rounded-2xl bg-slate-50 border border-slate-200 focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/20 transition-all duration-150 @error('password') border-red-400 @enderror">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                    <rect height="11" rx="2" ry="2" width="18" x="3" y="11"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                            <input
                                class="block w-full pl-11 pr-11 py-3.5 text-sm bg-transparent border-0 rounded-2xl text-slate-900 placeholder:text-slate-400 font-medium tracking-wide focus:ring-0 focus:outline-none"
                                id="password" name="password" placeholder="••••••••••••" required=""
                                type="password" autocomplete="current-password" />
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

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input class="w-4 h-4 accent-teal-600 rounded border-slate-300" name="remember"
                                type="checkbox" />
                            <span class="text-xs text-slate-600 font-medium">Recordar en este equipo</span>
                        </label>
                        <span class="text-[11px] text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewbox="0 0 20 20">
                                <path clip-rule="evenodd"
                                    d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                    fill-rule="evenodd"></path>
                            </svg>
                            SSL 256-bit
                        </span>
                    </div>

                    <div class="pt-2">
                        <button
                            class="w-full h-12 bg-brand-navy hover:bg-brand-navy-light active:scale-[0.99] text-white font-semibold rounded-2xl flex items-center justify-center gap-2.5 shadow-elevated transition duration-200 ease-out"
                            type="submit">
                            <span>Iniciar Sesión</span>
                            <svg class="w-5 h-5 text-teal-300" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2.5" viewbox="0 0 24 24">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" x2="3" y1="12" y2="12"></line>
                            </svg>
                        </button>
                    </div>

                </form>

                <div class="mt-4">
                    <button type="button" id="login-pwa-install"
                        class="w-full h-11 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100 active:scale-[0.99] text-slate-700 text-xs font-bold flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" x2="12" y1="15" y2="3"></line>
                        </svg>
                        Descargar App
                    </button>
                </div>
            </div>

            <footer class="mt-8 pt-4 border-t border-slate-100 text-center space-y-2">
                <p class="text-xs text-slate-500 font-medium">
                    ¿Problemas para acceder?
                    <a class="text-teal-600 font-semibold hover:underline" href="mailto:juandonquis07@gmail.com">juandonquis07@gmail.com</a>
                </p>
                <p class="text-[11px] text-slate-400">
                    © 2026 {{ config('app.name') }}. Todos los derechos reservados.
                </p>
            </footer>
        </main>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
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

    <script>
        (function() {
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').catch(function(error) {
                        console.error('No se pudo registrar el service worker:', error);
                    });
                });
            }

            const installButton = document.getElementById('login-pwa-install');
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent) ||
                (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

            if (!installButton) return;

            if (isStandalone) {
                installButton.style.display = 'none';
                return;
            }

            installButton.addEventListener('click', async function() {
                if (window.__pwaPrompt) {
                    window.__pwaPrompt.prompt();

                    const choice = await window.__pwaPrompt.userChoice;
                    window.__pwaPrompt = null;

                    if (choice.outcome === 'accepted') {
                        installButton.style.display = 'none';
                    }

                    return;
                }

                if (isIOS) {
                    alert('Para instalar la app en iPhone/iPad: abre esta página en Safari, toca el botón Compartir y elige "Añadir a pantalla de inicio".');
                    return;
                }

                alert('Para instalar la app, abre esta página en Chrome o Edge y usa el menú del navegador > Instalar aplicación.');
            });
        })();
    </script>
</body>

</html>
