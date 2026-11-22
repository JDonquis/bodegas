<!DOCTYPE html>
<html class="min-h-full bg-surface" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Bodegas SaaS | Superadministrador</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-full bg-surface font-jakarta text-slate-800 antialiased">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-navy text-brand-accent">
                        <span class="material-symbols-outlined text-[20px]">shield_person</span>
                    </span>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-brand-navy">Superadministrador</h1>
                        <p class="text-[11px] font-medium text-slate-500">Gestión de instancias de bodega</p>
                        <p class="text-[10px] text-slate-400">Todos los usuarios entran desde el login principal; cada uno
                            verá los datos de su bodega.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.billing') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[16px]">payments</span>
                    Cobros
                </a>
                <span class="text-xs font-semibold text-slate-500">{{ auth('superadmin')->user()->name }}</span>
                <form method="POST" action="{{ route('superadmin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                        Salir
                    </button>
                </form>
            </div>
        </header>

        @if ($errors->any())
            <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 grid grid-cols-2 gap-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bodegas</p>
                <p class="mt-1 text-2xl font-extrabold text-brand-navy">{{ $tenants->count() }}</p>
                <p class="mt-1 text-[10px] font-medium text-slate-400">Instancias registradas</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Activas</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-700">{{ $tenants->where('is_active', true)->count() }}</p>
                <p class="mt-1 text-[10px] font-medium text-emerald-600">Disponibles para operar</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-5">
            <section class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-brand-card">
                    <h2 class="text-sm font-extrabold text-brand-navy">Nueva Bodega</h2>
                    <p class="mt-1 text-[11px] text-slate-500">Crea una instancia aislada con sus propios usuarios, clientes y
                        movimientos.</p>

                    <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="mt-4 space-y-3">
                        @csrf

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nombre
                                de la bodega</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Bodega Los Pinos"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                            <p class="mt-1 text-[9px] text-slate-400">Este nombre identifica la bodega. El acceso se genera
                                automáticamente.</p>
                        </div>

                        <div class="border-t border-slate-100 pt-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Administrador inicial
                            </p>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nombre
                                del administrador</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                                placeholder="Ana Pérez"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Cédula /
                                usuario</label>
                            <input type="text" name="admin_ci" value="{{ old('admin_ci') }}" required
                                placeholder="12345678"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Contraseña</label>
                            <input type="password" name="admin_password" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Confirmar
                                contraseña</label>
                            <input type="password" name="admin_password_confirmation" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>

                        <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-navy px-4 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-brand-btn transition active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[18px] text-brand-accent">add_business</span>
                            Crear bodega
                        </button>
                    </form>
                </div>
            </section>

            <section class="lg:col-span-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-brand-card">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-extrabold text-brand-navy">Bodegas registradas</h2>
                        <span class="text-[10px] font-semibold text-slate-400">{{ $tenants->count() }} total</span>
                    </div>

                    <div class="mt-3 space-y-2.5">
                        @forelse ($tenants as $tenant)
                            <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <h3 class="text-xs font-extrabold text-brand-navy">{{ $tenant->name }}</h3>
                                            @if ($tenant->is_default)
                                                <span
                                                    class="rounded bg-blue-100 px-1.5 py-0.5 text-[9px] font-bold text-blue-700">Principal</span>
                                            @endif
                                            <span
                                                class="rounded px-1.5 py-0.5 text-[9px] font-bold {{ $tenant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">{{ $tenant->is_active ? 'Activa' : 'Inactiva' }}</span>
                                        </div>
                                        <p class="mt-0.5 text-[10px] text-slate-500">
                                            Creada el {{ $tenant->created_at?->format('d/m/Y') }}</p>
                                        @if (! $tenant->is_active)
                                            <p class="mt-0.5 text-[10px] font-semibold text-rose-600">Usuarios bloqueados
                                                temporalmente.</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('superadmin.tenants.toggle', ['tenant' => $tenant->id]) }}"
                                        @if ($tenant->is_active) onsubmit="return confirm('¿Inhabilitar esta bodega? Sus usuarios no podrán ingresar.');" @endif>
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex shrink-0 items-center gap-1 rounded-lg border px-2.5 py-1.5 text-[10px] font-bold transition {{ $tenant->is_active ? 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                            <span class="material-symbols-outlined text-[14px]">{{ $tenant->is_active ? 'block' : 'check_circle' }}</span>
                                            {{ $tenant->is_active ? 'Deshabilitar' : 'Habilitar' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">storefront</span>
                                <p class="mt-2 text-xs font-bold text-slate-600">No hay bodegas registradas</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-6">
            <details class="rounded-2xl border border-slate-200 bg-white p-5 shadow-brand-card">
                <summary class="flex cursor-pointer items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-sm font-extrabold text-brand-navy">
                        <span class="material-symbols-outlined text-[18px] text-brand-accent">manage_accounts</span>
                        Mis credenciales
                    </span>
                    <span class="text-[10px] font-semibold text-slate-400">Correo y contraseña del superadministrador</span>
                </summary>

                <form method="POST" action="{{ route('superadmin.credentials.update') }}"
                    class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @csrf

                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nombre</label>
                        <input type="text" name="name" required value="{{ old('name', auth('superadmin')->user()->name) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Correo</label>
                        <input type="email" name="email" required value="{{ old('email', auth('superadmin')->user()->email) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Contraseña
                            actual</label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Nueva
                            contraseña (opcional)</label>
                        <input type="password" name="password"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Confirmar
                            nueva contraseña</label>
                        <input type="password" name="password_confirmation"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full rounded-xl bg-brand-navy px-4 py-2.5 text-xs font-extrabold uppercase tracking-wide text-white active:scale-[0.98]">
                            Guardar crédenciales
                        </button>
                    </div>
                </form>
            </details>
        </section>
    </div>
</body>

</html>
