@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <style>
        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection

@section('content')
    @php
        Carbon\Carbon::setLocale('es');
        $netPositive = $netProfit >= 0;
    @endphp

    <div class="mx-auto flex w-full max-w-md flex-col gap-4 font-jakarta">

        <section class="space-y-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-brand-navy">Gastos del Dueño</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Registra el consumo propio del inventario y mide el impacto en tus ganancias.
                </p>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Tasa BCV:
                    {{ number_format($usdRate ?? 0, 2) }} Bs/USD</p>
            </div>

            <a href="{{ route('expenses.create') }}"
                class="w-full bg-brand-navy hover:bg-brand-navy-light text-white font-bold py-3.5 px-5 rounded-2xl flex items-center justify-center gap-2.5 shadow-lg shadow-brand-navy/25 active:scale-[0.99] transition-all">
                <span class="material-symbols-outlined text-[20px] text-brand-accent">add_card</span>
                <span class="text-sm tracking-wide">Registrar Gasto</span>
            </a>
        </section>

        <section class="grid grid-cols-2 gap-2.5">
            <div
                class="bg-gradient-to-br from-rose-50/90 to-red-50/40 p-3 rounded-2xl border border-rose-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-rose-900 uppercase tracking-wider leading-none">Gasto
                        total</span>
                    <div class="w-6 h-6 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">money_off</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-rose-600">${{ number_format($totalCost, 2) }}</span>
                    <span class="block text-[9px] text-rose-500 font-semibold mt-0.5">Bs.
                        {{ number_format($totalCost * ($usdRate ?? 0), 2) }}</span>
                    <span class="block text-[9px] text-slate-400 font-medium mt-1">
                        Valor venta: ${{ number_format($totalRetail, 2) }}
                    </span>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-emerald-50/90 to-teal-50/50 p-3 rounded-2xl border border-emerald-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider leading-none">Ganancia
                        bruta</span>
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">payments</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-emerald-700">${{ number_format($grossProfit, 2) }}</span>
                    <span class="block text-[9px] text-emerald-600 font-semibold mt-0.5">Bs.
                        {{ number_format($grossProfit * ($usdRate ?? 0), 2) }}</span>
                    <span class="block text-[9px] text-slate-400 font-medium mt-1">
                        Dejado de ganar: ${{ number_format($lostProfit, 2) }}
                    </span>
                </div>
            </div>

            <div
                class="col-span-2 bg-gradient-to-r from-slate-900 to-brand-navy p-4 rounded-2xl text-white shadow-md flex items-center justify-between gap-3">
                <div class="space-y-0.5 min-w-0">
                    <span class="text-[10px] font-bold text-brand-accent uppercase tracking-widest">Ganancia neta</span>
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <span
                            class="text-2xl font-black tracking-tight {{ $netPositive ? 'text-white' : 'text-rose-300' }}">${{ number_format($netProfit, 2) }}</span>
                        <span class="text-[10px] font-medium text-slate-300">Bs.
                            {{ number_format($netProfit * ($usdRate ?? 0), 2) }}</span>
                    </div>
                    <p class="text-[10px] text-slate-300">
                        Ganancia bruta menos gastos del período
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] font-bold text-brand-accent uppercase tracking-wider block">Impacto</span>
                    <span class="text-lg font-extrabold {{ $impact > 50 ? 'text-rose-300' : 'text-brand-accent' }}">
                        {{ number_format($impact, 1) }}%
                    </span>
                    <span class="block text-[9px] text-slate-300">de la ganancia bruta</span>
                </div>
            </div>
        </section>

        <section>
            <form method="GET" action="{{ route('expenses') }}"
                class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-brand-card space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Filtrar gastos</span>
                    <a href="{{ route('expenses') }}" class="text-[10px] font-bold text-brand-navy hover:underline">Limpiar</a>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Desde</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="w-full py-2.5 px-2.5 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hasta</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="w-full py-2.5 px-2.5 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-2.5 rounded-xl bg-brand-navy text-white text-xs font-bold flex items-center justify-center gap-1.5 active:scale-[0.98] transition">
                    <span class="material-symbols-outlined text-[16px] text-brand-accent">filter_alt</span>
                    Aplicar filtros
                </button>
            </form>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Gastos registrados</h2>
                <span class="text-[11px] font-semibold text-slate-500">{{ $records }} registro(s)</span>
            </div>

            <div class="space-y-3">
                @forelse ($expenses as $expense)
                    <article class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-brand-card space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center shrink-0 ring-1 ring-rose-200">
                                    <span class="material-symbols-outlined text-[22px]">shopping_basket</span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-brand-navy truncate">
                                        {{ $expense->description ?: 'Consumo del dueño' }}
                                    </h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $expense->created_at->translatedFormat('j M · h:i A') }}
                                        · {{ $expense->items->count() }} producto(s)
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-lg font-extrabold text-rose-600">${{ number_format($expense->total_cost, 2) }}</span>
                                <p class="text-[10px] font-medium text-slate-400">Bs.
                                    {{ number_format($expense->total_cost * ($usdRate ?? 0), 2) }}</p>
                                <p class="text-[10px] font-medium text-slate-400">Venta:
                                    ${{ number_format($expense->total_retail, 2) }}</p>
                            </div>
                        </div>

                        <div class="bg-slate-50/80 border border-slate-100 rounded-xl px-3 py-2 space-y-1.5">
                            @foreach ($expense->items as $item)
                                <div class="flex items-center justify-between gap-2 text-[11px]">
                                    <span class="text-slate-600 truncate">
                                        {{ $item->product_name }}
                                        <span class="text-slate-400">
                                            · {{ number_format($item->quantity, $item->sale_type === 'weight' ? 3 : 0, ',', '.') }}
                                            {{ $item->unit_label }}
                                        </span>
                                    </span>
                                    <span class="font-bold text-rose-700 shrink-0">${{ number_format($item->subtotal_cost, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 text-[11px]">
                            <span class="text-slate-500">
                                Ganancia dejada de percibir:
                                <strong class="text-amber-600">${{ number_format($expense->total_lost_profit, 2) }}</strong>
                            </span>

                            <form method="POST" action="{{ route('expenses.delete', $expense->id) }}"
                                onsubmit="return confirm('¿Eliminar este gasto y restaurar el stock?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold hover:bg-rose-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-brand-card text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No hay gastos registrados</p>
                        <p class="text-[11px] text-slate-400 mt-1">Registra el consumo de inventario del dueño.</p>
                    </div>
                @endforelse
            </div>

            @if ($expenses->hasPages())
                <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-sm flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Página
                        <strong class="text-brand-navy">{{ $expenses->currentPage() }}</strong> de
                        {{ $expenses->lastPage() }}
                        <span class="text-slate-400">({{ $expenses->total() }} reg.)</span>
                    </span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $expenses->previousPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ $expenses->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        <a href="{{ $expenses->nextPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ !$expenses->hasMorePages() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
