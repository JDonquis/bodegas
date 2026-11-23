<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#f8f9ff">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <script>
        window.addEventListener('beforeinstallprompt', function(event) {
            event.preventDefault();
            window.__pwaPrompt = event;
            window.dispatchEvent(new Event('pwa-prompt-ready'));
        });
    </script>
    <title>@yield('title-page', config('app.name'))</title>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>

<body class="bg-surface text-on-surface antialiased">
    <!-- Mobile App Bar -->
    <header
        class="lg:hidden fixed inset-x-0 top-0 z-40 bg-surface/90 backdrop-blur-xl border-b border-outline-variant/20"
        style="padding-top: env(safe-area-inset-top);">
        <div class="h-16 px-4 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-secondary text-[22px]">inventory_2</span>
                </div>
                <div class="flex flex-col leading-tight min-w-0">
                    <span
                        class="text-base font-bold font-headline text-on-surface tracking-tight truncate">{{ config('app.name') }}</span>
                    <span class="text-[10px] font-label uppercase tracking-wider text-on-surface-variant">Control de
                        stock</span>
                </div>
            </a>
            <div class="flex items-center gap-1.5">
                <button type="button" data-pwa-install aria-label="Instalar aplicación"
                    class="hidden h-10 w-10 rounded-full items-center justify-center text-secondary active:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[22px]">download</span>
                </button>
                <a href="{{ route('outputs') }}" aria-label="Movimientos recientes"
                    class="w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant active:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                </a>
                <button type="button" id="mobile-menu-trigger" aria-label="Abrir menú" aria-expanded="false"
                    class="w-9 h-9 rounded-full bg-primary ring-2 ring-primary-container/20 overflow-hidden shrink-0">
                    <img class="w-full h-full object-cover"
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=7F9CF5&background=EBF4FF"
                        alt="{{ auth()->user()->name }}">
                </button>
            </div>
        </div>

        <!-- Menú móvil -->
        <div id="mobile-menu"
            class="hidden absolute right-4 top-full mt-2 w-60 rounded-2xl bg-surface-container-lowest shadow-2xl border border-outline-variant/20 p-2 z-50">
            <div class="px-3 py-2 border-b border-outline-variant/20 mb-1">
                <p class="text-xs font-bold text-on-surface truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-on-surface-variant truncate">{{ auth()->user()->typeUser->name ?? 'Usuario' }}
                </p>
            </div>
            <a href="{{ route('products') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">package</span>
                Productos
            </a>
            <a href="{{ route('clients') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">group</span>
                Clientes
            </a>
            <a href="{{ route('debts') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                Deudas
            </a>
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('users') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                    Usuarios
                </a>
            @endif
            <button type="button" data-pwa-install
                class="hidden w-full items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-secondary hover:bg-secondary-container/30 transition-colors text-left">
                <span class="material-symbols-outlined text-[20px]">download</span>
                Instalar App
            </button>
            <a href="{{ route('profile') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">settings</span>
                Configuración
            </a>
            <a href="{{ route('logout') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-error hover:bg-error-container/40 transition-colors">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Cerrar sesión
            </a>
        </div>
    </header>

    <!-- Desktop SideNavBar -->
    <aside
        class="hidden lg:flex fixed left-0 top-0 h-screen flex-col p-6 bg-primary dark:bg-slate-950 shadow-2xl dark:shadow-none w-72 z-50 overflow-y-auto">
        <div class="mb-10 px-4">
            <span class="text-2xl font-bold text-white tracking-tight">{{ config('app.name') }}</span>
            <p class="text-on-primary-container text-xs mt-1 font-headline">Sistema de Bodegas</p>
        </div>
        <nav class="flex-1 space-y-2 font-headline text-sm tracking-wide">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('home') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('home') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('inventory') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('inventory') }}">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Inventario</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('products*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('products') }}">
                <span class="material-symbols-outlined">package</span>
                <span>Productos</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('entries*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('entries') }}">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span>Compras</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('outputs*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('outputs') }}">
                <span class="material-symbols-outlined">logout</span>
                <span>Ventas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('clients*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('clients') }}">
                <span class="material-symbols-outlined">group</span>
                <span>Clientes</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('debts*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('debts') }}">
                <span class="material-symbols-outlined">account_balance_wallet</span>
                <span>Deudas</span>
            </a>
            @if (auth()->user()->hasRole('admin'))
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('users*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                    href="{{ route('users') }}">
                    <span class="material-symbols-outlined">manage_accounts</span>
                    <span>Usuarios</span>
                </a>
            @endif
            @if (Route::has('patients'))
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('patients*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                    href="{{ route('patients') }}">
                    <span class="material-symbols-outlined">medical_services</span>
                    <span>Pacientes</span>
                </a>
            @endif
        </nav>
        <div class="mt-auto pt-6 border-t border-white/10 space-y-2">
            <a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white transition-colors font-headline text-sm"
                href="{{ route('profile') }}">
                <span class="material-symbols-outlined">settings</span>
                <span>Configuración</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white transition-colors font-headline text-sm"
                href="{{ route('logout') }}">
                <span class="material-symbols-outlined">logout</span>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- Desktop TopNavBar -->
    <header
        class="hidden lg:flex justify-end items-center h-20 px-8 ml-72 bg-surface/80 dark:bg-slate-900/80 backdrop-blur-xl sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <button type="button" data-pwa-install
                class="hidden items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-2 text-xs font-bold text-primary hover:bg-surface-container transition">
                <span class="material-symbols-outlined text-[16px] text-secondary">download</span>
                Instalar App
            </button>
            <div class="flex items-center gap-3 ml-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-primary">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-on-surface-variant">{{ auth()->user()->typeUser->name ?? 'Usuario' }}</p>
                </div>
                <div class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-primary-container">
                    <img class="w-full h-full object-cover"
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=7F9CF5&background=EBF4FF"
                        alt="{{ auth()->user()->name }}">
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main
        class="min-h-dvh w-full px-4 pt-[calc(5rem+env(safe-area-inset-top))] pb-[calc(5rem+env(safe-area-inset-bottom))] lg:ml-72 lg:px-10 lg:pt-8 lg:pb-10">
        @include('layout.toast')
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav id="mobile-bottom-nav"
        class="lg:hidden fixed inset-x-0 bottom-0 z-40 bg-surface-container-lowest/95 backdrop-blur-xl border-t border-outline-variant/20"
        style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ route('inventory') }}"
                class="flex flex-col items-center justify-center gap-0.5 transition-colors {{ request()->routeIs('inventory') ? 'text-secondary' : 'text-on-surface-variant active:text-on-surface' }}">
                <span class="material-symbols-outlined text-[22px]">warehouse</span>
                <span
                    class="text-[10px] font-label {{ request()->routeIs('inventory') ? 'font-bold' : '' }}">Inventario</span>
            </a>
            <a href="{{ route('products') }}"
                class="flex flex-col items-center justify-center gap-0.5 transition-colors {{ request()->routeIs('products*') ? 'text-secondary' : 'text-on-surface-variant active:text-on-surface' }}">
                <span class="material-symbols-outlined text-[22px]">package</span>
                <span
                    class="text-[10px] font-label {{ request()->routeIs('products*') ? 'font-bold' : '' }}">Productos</span>
            </a>
            <a href="{{ route('home') }}"
                class="flex flex-col items-center justify-center gap-0.5 transition-colors {{ request()->routeIs('home') ? 'text-secondary' : 'text-on-surface-variant active:text-on-surface' }}">
                <span class="material-symbols-outlined text-[22px]">dashboard</span>
                <span class="text-[10px] font-label {{ request()->routeIs('home') ? 'font-bold' : '' }}">Inicio</span>
            </a>
            <a href="{{ route('entries') }}"
                class="flex flex-col items-center justify-center gap-0.5 transition-colors {{ request()->routeIs('entries*') ? 'text-secondary' : 'text-on-surface-variant active:text-on-surface' }}">
                <span class="material-symbols-outlined text-[22px]">shopping_cart</span>
                <span
                    class="text-[10px] font-label {{ request()->routeIs('entries*') ? 'font-bold' : '' }}">Compras</span>
            </a>
            <a href="{{ route('outputs') }}"
                class="flex flex-col items-center justify-center gap-0.5 transition-colors {{ request()->routeIs('outputs*') ? 'text-secondary' : 'text-on-surface-variant active:text-on-surface' }}">
                <span class="material-symbols-outlined text-[22px]">logout</span>
                <span
                    class="text-[10px] font-label {{ request()->routeIs('outputs*') ? 'font-bold' : '' }}">Ventas</span>
            </a>
        </div>
    </nav>

    <script>
        (function() {
            const trigger = document.getElementById('mobile-menu-trigger');
            const menu = document.getElementById('mobile-menu');

            if (!trigger || !menu) {
                return;
            }

            trigger.addEventListener('click', function(event) {
                event.stopPropagation();
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                trigger.setAttribute('aria-expanded', String(!isOpen));
            });

            document.addEventListener('click', function(event) {
                if (menu.classList.contains('hidden')) {
                    return;
                }

                if (menu.contains(event.target) || trigger.contains(event.target)) {
                    return;
                }

                menu.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            });
        })();
    </script>

    <script>
        (function() {
            const nav = document.getElementById('mobile-bottom-nav');

            if (!nav || !window.visualViewport) {
                return;
            }

            let frame = null;

            function syncBottomNav() {
                frame = null;
                const viewport = window.visualViewport;
                const inset = Math.max(0, window.innerHeight - viewport.height - viewport.offsetTop);

                nav.style.bottom = inset > 1 ? Math.round(inset) + 'px' : '';
            }

            function scheduleSync() {
                if (frame === null) {
                    frame = requestAnimationFrame(syncBottomNav);
                }
            }

            window.visualViewport.addEventListener('resize', scheduleSync);
            window.visualViewport.addEventListener('scroll', scheduleSync);
            window.addEventListener('orientationchange', scheduleSync);
            syncBottomNav();
        })();
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

            const installButtons = document.querySelectorAll('[data-pwa-install]');
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent) ||
                (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

            function showInstallButtons(show) {
                installButtons.forEach(function(button) {
                    button.classList.toggle('hidden', !show);
                    button.classList.toggle('flex', show);
                });
            }

            function refreshInstallButtons() {
                showInstallButtons(!isStandalone && Boolean(window.__pwaPrompt || isIOS));
            }

            installButtons.forEach(function(button) {
                button.addEventListener('click', async function() {
                    if (window.__pwaPrompt) {
                        window.__pwaPrompt.prompt();

                        await window.__pwaPrompt.userChoice;
                        window.__pwaPrompt = null;
                        showInstallButtons(false);

                        return;
                    }

                    if (isIOS) {
                        alert('Para instalar la app en iPhone/iPad: abre esta página en Safari, toca el botón Compartir y elige "Añadir a pantalla de inicio".');
                        return;
                    }

                    alert('Tu navegador no permite instalar la app directamente. Usa Chrome o Edge en Android/escritorio, o Safari en iPhone/iPad.');
                });
            });

            window.addEventListener('pwa-prompt-ready', refreshInstallButtons);
            window.addEventListener('appinstalled', function() {
                window.__pwaPrompt = null;
                showInstallButtons(false);
            });
            document.addEventListener('DOMContentLoaded', refreshInstallButtons);
            refreshInstallButtons();
        })();
    </script>

    <script>
        window.tenantBase = '';
    </script>

    @yield('scripts')
</body>

</html>
