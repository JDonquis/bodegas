<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<body class="bg-surface text-on-surface">
    <!-- SideNavBar Shell -->
    <aside
        class="fixed left-0 top-0 h-screen flex flex-col p-6 bg-primary dark:bg-slate-950 shadow-2xl dark:shadow-none w-72 z-50">
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
                <span class="material-symbols-outlined">login</span>
                <span>Entradas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('outputs*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('outputs') }}">
                <span class="material-symbols-outlined">logout</span>
                <span>Salidas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('clients*') ? 'bg-primary-container text-white rounded-full border-l-4 border-secondary' : 'text-slate-400 hover:text-white hover:bg-primary-container/50' }} transition-all duration-200"
                href="{{ route('clients') }}">
                <span class="material-symbols-outlined">group</span>
                <span>Clientes</span>
            </a>
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

    <!-- TopNavBar Shell -->
    <header
        class="flex justify-end items-center h-20 px-8 ml-72 bg-surface/80 dark:bg-slate-900/80 backdrop-blur-xl sticky top-0 z-40 ">
        <div class="flex items-center gap-4">

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
    <main class="ml-72 p-10 min-h-[calc(100vh-80px)]">
        @include('layout.toast')
        @yield('content')
    </main>

    @yield('scripts')
</body>

</html>
