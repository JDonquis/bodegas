@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
@endsection

@section('content')
    <div class="mx-auto w-full max-w-md px-1 font-jakarta">
        <div class="flex items-start gap-3.5 mb-5 px-1">
            <a href="{{ route('users') }}" aria-label="Regresar a usuarios"
                class="mt-0.5 w-9 h-9 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-sm active:scale-90 transition-transform">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Nuevo Usuario</h1>
                <p class="text-xs text-slate-600 font-medium leading-relaxed mt-0.5">Crea una cuenta de acceso para
                    {{ config('app.name') }}.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-blue-50/80 shadow-brand-card">
            <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50/60 px-3 py-2 text-[11px] font-medium text-blue-800">
                Esta cuenta tendrá acceso de administrador únicamente a esta bodega.
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nombre *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Juan"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        @error('name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Apellido *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            placeholder="Pérez"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        @error('last_name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="ci" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Cédula / Usuario *</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-[18px] text-slate-400">badge</span>
                        <input type="text" name="ci" id="ci" value="{{ old('ci') }}" required
                            placeholder="12345678"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Se usará para iniciar sesión. Debe ser única.</p>
                    @error('ci')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="charge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Cargo *</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-[18px] text-slate-400">work</span>
                        <input type="text" name="charge" id="charge" value="{{ old('charge', 'Administrador') }}" required
                            placeholder="Administrador"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>
                    @error('charge')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="password" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contraseña *</label>
                        <input type="password" name="password" id="password" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        @error('password')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Confirmar *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-between gap-3">
                    <a href="{{ route('users') }}"
                        class="w-1/3 py-3.5 px-3 rounded-2xl text-slate-500 hover:text-slate-800 text-xs font-bold tracking-wide uppercase active:scale-95 transition-all text-center">Descartar</a>
                    <button type="submit"
                        class="w-2/3 py-3.5 px-4 rounded-2xl bg-brand-navy text-white text-xs font-extrabold tracking-wide uppercase shadow-brand-btn active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-brand-accent">person_add</span>
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
