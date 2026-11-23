@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-md flex-col gap-4 font-jakarta">

        <section class="space-y-3">
            <div>
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Usuarios de la Bodega</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Gestiona el acceso del personal a
                    {{ config('app.name') }}.</p>
            </div>

            <a href="{{ route('users.create') }}"
                class="w-full bg-brand-navy hover:bg-brand-navy-light text-white font-bold py-3.5 px-5 rounded-2xl flex items-center justify-center gap-2.5 shadow-lg shadow-brand-navy/25 active:scale-[0.99] transition-all">
                <span class="material-symbols-outlined text-[20px] text-brand-accent">person_add</span>
                <span class="text-sm tracking-wide">Nuevo Usuario</span>
            </a>
        </section>

        <section class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-3.5 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Usuarios</p>
                <p class="mt-1 text-2xl font-extrabold text-brand-navy">{{ $users->total() }}</p>
                <p class="mt-1 text-[10px] font-medium text-blue-600">Cuentas de esta bodega</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-3.5 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Tu sesión</p>
                <p class="mt-1 truncate text-sm font-extrabold text-brand-navy">{{ auth()->user()->name }}</p>
                <p class="mt-1 text-[10px] font-medium text-emerald-600">Administrador</p>
            </div>
        </section>

        <section>
            <div
                class="rounded-xl border border-amber-100 bg-amber-50/60 px-3 py-2 text-[11px] font-medium text-amber-800">
                Los usuarios creados solo podrán ver la información de esta bodega.
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Personal autorizado</h2>
                <span class="text-[11px] font-semibold text-slate-500">{{ $users->total() }} registros</span>
            </div>

            <div class="space-y-3">
                @forelse ($users as $user)
                    @php
                        $fullName = trim($user->name . ' ' . $user->last_name);
                        $initials = collect(preg_split('/\s+/', $fullName))
                            ->filter()
                            ->take(2)
                            ->map(function ($part) {
                                return mb_strtoupper(mb_substr($part, 0, 1));
                            })
                            ->implode('');
                        $initials = $initials ?: 'US';
                        $isCurrent = $user->id === auth()->id();
                    @endphp

                    <article class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-brand-card space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0 ring-1 ring-blue-200/70">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h3 class="text-sm font-bold text-brand-navy truncate">{{ $fullName }}</h3>
                                        @if ($isCurrent)
                                            <span
                                                class="rounded bg-blue-100 px-1.5 py-0.5 text-[9px] font-bold text-blue-700">Tú</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5 font-mono">CI: {{ $user->ci }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $user->charge }} ·
                                        <span class="font-semibold text-secondary">Administrador</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('users.edit', ['user' => $user->id]) }}" title="Editar usuario"
                                    class="p-1.5 text-blue-600 hover:text-brand-navy hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                @if (! $isCurrent)
                                    <button type="button"
                                        onclick="deleteTenantUser('{{ route('users.delete', ['user' => $user->id]) }}')"
                                        title="Eliminar usuario"
                                        class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                            <span class="text-slate-400">Acceso a {{ config('app.name') }}</span>
                            <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Activo
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-brand-card text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300">manage_accounts</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No hay usuarios registrados</p>
                        <p class="text-[11px] text-slate-400 mt-1">Crea el primer usuario para esta bodega.</p>
                    </div>
                @endforelse
            </div>

            @if ($users->hasPages())
                <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-3 shadow-sm">
                    <span class="text-xs font-medium text-slate-500">Página
                        <strong class="text-brand-navy">{{ $users->currentPage() }}</strong> de
                        {{ $users->lastPage() }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $users->previousPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ $users->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        <a href="{{ $users->nextPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ !$users->hasMorePages() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <form action="" id="tenant-user-delete-form" class="hidden" method="POST">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <script>
        function deleteTenantUser(url) {
            if (confirm('¿Está seguro de eliminar este usuario? Ya no podrá acceder a la bodega.')) {
                const form = document.getElementById('tenant-user-delete-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
@endsection
