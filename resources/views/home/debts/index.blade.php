@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #debt-sheet {
            transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #debt-backdrop {
            transition: opacity 0.3s ease-out;
        }
    </style>
@endsection

@section('content')
    @php
        Carbon\Carbon::setLocale('es');

        $shouldOpenDebt = ($openDebtSheet ?? false) || ($errors->any() && old('form_type') !== 'payment');
        $shouldOpenPayment = ($openPaymentSheet ?? false) || ($errors->any() && old('form_type') === 'payment');
    @endphp

    <div class="mx-auto flex w-full max-w-md flex-col gap-4 font-jakarta">

        <section class="space-y-3">
            <div>
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Gestión de Deudas</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Registro de cuentas por cobrar y ventas asociadas.</p>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Tasa BCV:
                    {{ number_format($usdRate ?? 0, 2) }} Bs/USD</p>
            </div>

            <button type="button" onclick="openDebtSheet()"
                class="w-full bg-brand-navy hover:bg-brand-navy-light text-white font-bold py-3.5 px-5 rounded-2xl flex items-center justify-center gap-2.5 shadow-lg shadow-brand-navy/25 active:scale-[0.99] transition-all">
                <span class="material-symbols-outlined text-[20px] text-brand-accent">add_card</span>
                <span class="text-sm tracking-wide">Registrar Deuda</span>
            </button>
        </section>

        <section class="grid grid-cols-3 gap-2.5">
            <div
                class="bg-gradient-to-br from-rose-50/90 to-red-50/40 p-3 rounded-2xl border border-rose-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-rose-900 uppercase tracking-wider leading-none">Deuda
                        Total</span>
                    <div class="w-6 h-6 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">account_balance_wallet</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-rose-600">${{ number_format($totalDebt, 2) }}</span>
                    <span class="block text-[9px] text-rose-500 font-semibold mt-0.5">Bs.
                        {{ number_format($totalDebt * ($usdRate ?? 0), 2) }}</span>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-blue-50/90 to-indigo-50/50 p-3 rounded-2xl border border-blue-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-blue-900 uppercase tracking-wider leading-none">Con
                        Deuda</span>
                    <div class="w-6 h-6 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">group</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-brand-navy">{{ $debtorsCount }}</span>
                    <span class="block text-[9px] text-blue-700 font-semibold mt-0.5">clientes</span>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-emerald-50/90 to-teal-50/50 p-3 rounded-2xl border border-emerald-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider leading-none">Filtrado</span>
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">payments</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-emerald-700">${{ number_format($filteredTotal, 2) }}</span>
                    <span class="block text-[9px] text-emerald-600 font-semibold mt-0.5">Bs.
                        {{ number_format($filteredTotal * ($usdRate ?? 0), 2) }}</span>
                </div>
            </div>
        </section>

        <section>
            <form method="GET" action="{{ route('debts') }}"
                class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-brand-card space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Filtrar deudas</span>
                    <a href="{{ route('debts') }}" class="text-[10px] font-bold text-brand-navy hover:underline">Limpiar</a>
                </div>

                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cliente</label>
                    <select name="client_id"
                        class="w-full py-2.5 px-3 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                        <option value="">Todos los clientes</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected((string) $clientId === (string) $client->id)>
                                {{ $client->name }} {{ $client->ci ? '(' . $client->ci . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
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
                <h2 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Deudas recientes</h2>
                <span class="text-[11px] font-semibold text-slate-500">Más reciente primero</span>
            </div>

            <div class="space-y-3">
                @forelse ($debts as $debt)
                    @php
                        $client = $debt->client;
                        $clientName = $client->name ?? 'Cliente';
                        $initials = collect(preg_split('/\s+/', trim($clientName)))
                            ->filter()
                            ->take(2)
                            ->map(function ($part) {
                                return mb_strtoupper(mb_substr($part, 0, 1));
                            })
                            ->implode('');
                        $initials = $initials ?: 'CL';
                        $paidAmount = $debt->payments->sum('amount');
                        $remaining = max(0, round((float) $debt->amount - $paidAmount, 2));
                    @endphp

                    <article
                        class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-brand-card space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center font-bold text-sm shrink-0 ring-1 ring-rose-200">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-brand-navy truncate">{{ $clientName }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5 font-mono">CI:
                                        {{ $client->ci ?? 'No registrada' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="text-lg font-extrabold text-rose-600">${{ number_format($debt->amount, 2) }}</span>
                                <p class="text-[10px] font-medium text-slate-400">Bs.
                                    {{ number_format($debt->amount * ($usdRate ?? 0), 2) }}</p>
                                <p class="text-[10px] font-medium text-slate-400">
                                    {{ $debt->created_at->translatedFormat('j M · h:i A') }}</p>
                                <p class="text-[10px] font-semibold text-rose-600 mt-0.5">Saldo:
                                    ${{ number_format($remaining, 2) }}</p>
                                <p class="text-[9px] text-slate-400">Bs.
                                    {{ number_format($remaining * ($usdRate ?? 0), 2) }}</p>
                                @if ($paidAmount > 0)
                                    <p class="text-[10px] font-semibold text-emerald-600">Abonado:
                                        ${{ number_format($paidAmount, 2) }}</p>
                                @endif
                            </div>
                        </div>

                        @if ($debt->description)
                            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/80 rounded-xl px-3 py-2 border border-slate-100">
                                {{ $debt->description }}</p>
                        @endif

                        @php
                            $creditOutputs = $debt->outputGeneral
                                ? $debt->outputGeneral->outputs->where('is_credit', true)
                                : collect();
                        @endphp

                        @if ($creditOutputs->isNotEmpty())
                            <div class="bg-amber-50/70 border border-amber-100 rounded-xl px-3 py-2 space-y-1.5">
                                <p
                                    class="text-[10px] font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">credit_card</span>
                                    Productos fiados
                                </p>
                                @foreach ($creditOutputs as $creditOutput)
                                    @php
                                        $creditProduct = $creditOutput->product;
                                        $isWeight = ($creditProduct->sale_type ?? null) === 'weight';
                                        $qtyLabel = $isWeight
                                            ? number_format((float) $creditOutput->quantity, 0, ',', '.') . ' g'
                                            : number_format((float) $creditOutput->quantity, 0, ',', '.') . ' uds';
                                    @endphp
                                    <div class="flex items-center justify-between gap-2 text-[11px]">
                                        <span class="text-slate-600 truncate">
                                            {{ $creditProduct->name ?? 'Producto' }}
                                            <span class="text-slate-400">· {{ $qtyLabel }}</span>
                                        </span>
                                        <span
                                            class="font-bold text-amber-800 shrink-0">${{ number_format($creditOutput->subtotal, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 text-[11px]">
                            <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                                @if ($debt->outputGeneral)
                                    <a href="{{ route('invoice', ['outputID' => $debt->outputGeneral->id]) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold hover:bg-blue-100 transition">
                                        <span class="material-symbols-outlined text-[13px]">receipt_long</span>
                                        Venta #{{ $debt->outputGeneral->id }}
                                    </a>
                                @else
                                    <span class="text-slate-400">Sin venta asociada</span>
                                @endif
                                <span class="text-slate-400">·</span>
                                <span class="text-slate-500">Deuda actual:
                                    <strong class="text-rose-600">${{ number_format($client->debt ?? 0, 2) }}</strong></span>
                            </div>
                            @if ($remaining > 0)
                                <button type="button" onclick="openPaymentSheet({{ $client->id }}, {{ $debt->id }})"
                                    class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold hover:bg-emerald-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">payments</span>
                                    Abonar
                                </button>
                            @else
                                <span
                                    class="shrink-0 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">Pagada</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-brand-card text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No hay deudas registradas</p>
                        <p class="text-[11px] text-slate-400 mt-1">Registra una deuda o ajusta los filtros.</p>
                    </div>
                @endforelse
            </div>

            @if ($debts->hasPages())
                <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-sm flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Página
                        <strong class="text-brand-navy">{{ $debts->currentPage() }}</strong> de
                        {{ $debts->lastPage() }}
                        <span class="text-slate-400">({{ $debts->total() }} reg.)</span>
                    </span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $debts->previousPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ $debts->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        <a href="{{ $debts->nextPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ !$debts->hasMorePages() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <div id="debt-backdrop"
        class="fixed inset-0 z-[60] bg-brand-navy/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"
        onclick="closeDebtSheet()"></div>

    <section id="debt-sheet"
        class="fixed inset-x-0 bottom-0 z-[70] mx-auto max-w-md bg-white rounded-t-[34px] shadow-brand-sheet border-t border-white/60 transform translate-y-full flex flex-col max-h-[92vh] overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="debt-title">
        <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeDebtSheet()">
            <div class="w-12 h-1.5 rounded-full bg-gray-300"></div>
        </div>

        <div class="px-5 pt-2 pb-3.5 border-b border-brand-border flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-brand-navy tracking-tight leading-tight" id="debt-title">
                        Registrar Deuda</h3>
                    <p class="text-[11px] font-semibold text-gray-500 tracking-wide mt-0.5">Asocia un cliente y una venta
                        opcional</p>
                </div>
            </div>
            <button type="button" onclick="closeDebtSheet()" aria-label="Cerrar ventana"
                class="w-9 h-9 rounded-full bg-[#f3f5fa] hover:bg-gray-200 text-gray-500 hover:text-brand-navy flex items-center justify-center active:scale-90 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-5 py-4 overflow-y-auto space-y-3.5 no-scrollbar bg-[#f8f9ff]">
            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('debts.store') }}" id="debt-form" class="space-y-3.5">
                @csrf
                <input type="hidden" name="form_type" value="debt">

                <div>
                    <label for="debt-client-select"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cliente</label>
                    <select name="client_id" id="debt-client-select"
                        class="w-full py-3 px-3 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                        <option value="">-- Seleccionar cliente --</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected((string) old('client_id', $clientId ?? '') === (string) $client->id)>
                                {{ $client->name }} {{ $client->ci ? '(' . $client->ci . ')' : '' }}
                            </option>
                        @endforeach
                        <option value="new" @selected(old('client_id') === 'new')>+ Nuevo cliente rápido</option>
                    </select>
                </div>

                <div id="new-client-fields"
                    class="hidden rounded-xl border border-amber-200 bg-amber-50/70 p-3 space-y-2">
                    <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Nuevo cliente rápido</p>
                    <input type="text" name="new_client_name" id="new-client-name" value="{{ old('new_client_name') }}"
                        placeholder="Nombre del cliente"
                        class="w-full py-2.5 px-3 text-xs font-semibold rounded-lg bg-white border border-amber-200 text-brand-navy placeholder:text-slate-400 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 transition">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="new_client_ci" value="{{ old('new_client_ci') }}"
                            placeholder="Cédula / RIF"
                            class="w-full py-2.5 px-3 text-xs font-semibold rounded-lg bg-white border border-amber-200 text-brand-navy placeholder:text-slate-400 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 transition">
                        <input type="text" name="new_client_phone" value="{{ old('new_client_phone') }}"
                            placeholder="Teléfono"
                            class="w-full py-2.5 px-3 text-xs font-semibold rounded-lg bg-white border border-amber-200 text-brand-navy placeholder:text-slate-400 focus:border-amber-400 focus:ring-1 focus:ring-amber-200 transition">
                    </div>
                </div>

                <div>
                    <label for="debt-amount"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Monto de la
                        deuda</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative flex items-center rounded-xl bg-rose-50 border border-rose-200 focus-within:ring-2 focus-within:ring-rose-500/20">
                            <span class="pl-3 pr-1 text-rose-500 font-bold text-sm">$</span>
                            <input type="number" name="amount" id="debt-amount" step="0.01" min="0.01" required
                                value="{{ old('amount') }}" placeholder="0.00"
                                class="w-full min-w-0 bg-transparent border-0 py-3 pr-2 text-base font-extrabold text-rose-700 placeholder:text-rose-300 focus:ring-0">
                        </div>
                        <div class="relative flex items-center rounded-xl bg-slate-50 border border-slate-200 focus-within:ring-2 focus-within:ring-slate-400/20">
                            <span class="pl-3 pr-1 text-slate-500 font-bold text-[10px]">Bs</span>
                            <input type="number" step="0.01" min="0" id="debt-amount-bs" placeholder="0.00"
                                class="w-full min-w-0 bg-transparent border-0 py-3 pr-2 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:ring-0">
                        </div>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-1">Puedes escribir en $ o Bs. Tasa:
                        {{ number_format($usdRate ?? 0, 2) }}</p>
                </div>

                <div>
                    <label for="debt-sale-select"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Venta
                        asociada (opcional)</label>
                    <select name="output_general_id" id="debt-sale-select"
                        class="w-full py-3 px-3 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                        <option value="">Sin venta asociada</option>
                        @foreach ($sales as $sale)
                            <option value="{{ $sale->id }}" data-client-id="{{ $sale->client_id }}"
                                data-total="{{ $sale->total_sold }}"
                                @selected((string) old('output_general_id') === (string) $sale->id)>
                                Ticket #{{ $sale->id }} · {{ $sale->client->name ?? ($sale->client_name ?: 'Cliente Genérico') }}
                                · ${{ number_format($sale->total_sold, 2) }}@if (!$sale->client_id) · Genérica @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="debt-description"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción</label>
                    <textarea name="description" id="debt-description" rows="3"
                        placeholder="Motivo o detalle de la deuda (opcional)..."
                        class="w-full py-2.5 px-3 text-xs font-medium rounded-xl bg-white border border-slate-200 text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition resize-none">{{ old('description') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-brand-navy text-white text-xs font-extrabold tracking-wide uppercase shadow-brand-btn flex items-center justify-center gap-2 active:scale-[0.98] transition">
                    <span class="material-symbols-outlined text-[18px] text-brand-accent">save</span>
                    Guardar Deuda
                </button>
            </form>
        </div>
    </section>

    <div id="payment-backdrop"
        class="fixed inset-0 z-[60] bg-brand-navy/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"
        onclick="closePaymentSheet()"></div>

    <section id="payment-sheet"
        class="fixed inset-x-0 bottom-0 z-[70] mx-auto max-w-md bg-white rounded-t-[34px] shadow-brand-sheet border-t border-white/60 transform translate-y-full flex flex-col max-h-[92vh] overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="payment-title">
        <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closePaymentSheet()">
            <div class="w-12 h-1.5 rounded-full bg-gray-300"></div>
        </div>

        <div class="px-5 pt-2 pb-3.5 border-b border-brand-border flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-brand-navy tracking-tight leading-tight" id="payment-title">
                        Registrar Abono</h3>
                    <p class="text-[11px] font-semibold text-gray-500 tracking-wide mt-0.5">Registra un pago a la deuda del
                        cliente</p>
                </div>
            </div>
            <button type="button" onclick="closePaymentSheet()" aria-label="Cerrar ventana"
                class="w-9 h-9 rounded-full bg-[#f3f5fa] hover:bg-gray-200 text-gray-500 hover:text-brand-navy flex items-center justify-center active:scale-90 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-5 py-4 overflow-y-auto space-y-3.5 no-scrollbar bg-[#f8f9ff]">
            @if ($errors->any() && old('form_type') === 'payment')
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('debts.payments.store') }}" id="payment-form" class="space-y-3.5">
                @csrf
                <input type="hidden" name="form_type" value="payment">

                <div>
                    <label for="payment-client-select"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cliente</label>
                    <select name="client_id" id="payment-client-select" required
                        class="w-full py-3 px-3 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                        <option value="">-- Seleccionar cliente --</option>
                        @foreach ($debtorClients as $client)
                            <option value="{{ $client->id }}" data-debt="{{ $client->debt }}"
                                @selected((string) old('client_id', $clientId ?? '') === (string) $client->id)>
                                {{ $client->name }} {{ $client->ci ? '(' . $client->ci . ')' : '' }} · Deuda
                                ${{ number_format($client->debt, 2) }}
                            </option>
                        @endforeach
                    </select>
                    <p id="payment-client-info"
                        class="hidden text-[10px] text-slate-500 mt-1 font-medium">
                        Deuda actual: <strong class="text-rose-600">$0.00</strong>
                    </p>
                </div>

                <div>
                    <label for="payment-debt-select"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deuda a
                        abonar</label>
                    <select name="client_debt_id" id="payment-debt-select"
                        class="w-full py-3 px-3 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                        <option value="">Abono general (sin deuda específica)</option>
                        @foreach ($pendingDebts as $pendingDebt)
                            @php
                                $pendingPaid = $pendingDebt->payments->sum('amount');
                                $pendingRemaining = max(0, round((float) $pendingDebt->amount - $pendingPaid, 2));
                            @endphp
                            <option value="{{ $pendingDebt->id }}" data-client-id="{{ $pendingDebt->client_id }}"
                                data-remaining="{{ $pendingRemaining }}"
                                @selected((string) old('client_debt_id') === (string) $pendingDebt->id)>
                                Ref #{{ $pendingDebt->id }} · {{ $pendingDebt->client->name ?? 'Cliente' }} · Saldo
                                ${{ number_format($pendingRemaining, 2) }} / Bs.
                                {{ number_format($pendingRemaining * ($usdRate ?? 0), 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="payment-amount"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Monto del
                        abono</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative flex items-center rounded-xl bg-emerald-50 border border-emerald-200 focus-within:ring-2 focus-within:ring-emerald-500/20">
                            <span class="pl-3 pr-1 text-emerald-600 font-bold text-sm">$</span>
                            <input type="number" name="amount" id="payment-amount" step="0.01" min="0.01" required
                                value="{{ old('amount') }}" placeholder="0.00"
                                class="w-full min-w-0 bg-transparent border-0 py-3 pr-2 text-base font-extrabold text-emerald-700 placeholder:text-emerald-300 focus:ring-0">
                        </div>
                        <div class="relative flex items-center rounded-xl bg-slate-50 border border-slate-200 focus-within:ring-2 focus-within:ring-slate-400/20">
                            <span class="pl-3 pr-1 text-slate-500 font-bold text-[10px]">Bs</span>
                            <input type="number" step="0.01" min="0" id="payment-amount-bs" placeholder="0.00"
                                class="w-full min-w-0 bg-transparent border-0 py-3 pr-2 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:ring-0">
                        </div>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-1">Puedes escribir en $ o Bs. Tasa:
                        {{ number_format($usdRate ?? 0, 2) }}</p>
                </div>

                <div>
                    <label for="payment-description"
                        class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción</label>
                    <textarea name="description" id="payment-description" rows="3"
                        placeholder="Detalle del pago (opcional)..."
                        class="w-full py-2.5 px-3 text-xs font-medium rounded-xl bg-white border border-slate-200 text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition resize-none">{{ old('description') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-emerald-600 text-white text-xs font-extrabold tracking-wide uppercase shadow-brand-btn flex items-center justify-center gap-2 active:scale-[0.98] transition">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                    Guardar Abono
                </button>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        const usdRate = @json($usdRate ?? 0);
        const openDebtFormOnLoad = @json($shouldOpenDebt);
        const openPaymentFormOnLoad = @json($shouldOpenPayment);
        const queryClientId = @json($clientId);

        document.addEventListener('DOMContentLoaded', function() {
            const clientSelect = document.getElementById('debt-client-select');
            const saleSelect = document.getElementById('debt-sale-select');
            const amountInput = document.getElementById('debt-amount');
            const paymentClientSelect = document.getElementById('payment-client-select');
            const paymentDebtSelect = document.getElementById('payment-debt-select');
            const paymentAmountInput = document.getElementById('payment-amount');
            const debtAmountBsInput = document.getElementById('debt-amount-bs');
            const paymentAmountBsInput = document.getElementById('payment-amount-bs');

            updateClientMode();
            syncDebtAmountFromUsd();
            syncPaymentAmountFromUsd();
            updatePaymentClientInfo();

            document.getElementById('debt-amount').addEventListener('input', syncDebtAmountFromUsd);
            debtAmountBsInput.addEventListener('input', syncDebtAmountFromBs);
            paymentAmountInput.addEventListener('input', syncPaymentAmountFromUsd);
            paymentAmountBsInput.addEventListener('input', syncPaymentAmountFromBs);

            clientSelect.addEventListener('change', updateClientMode);

            saleSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];

                if (option && option.value && option.dataset.total) {
                    const currentAmount = parseFloat(amountInput.value) || 0;

                    if (currentAmount <= 0) {
                        amountInput.value = parseFloat(option.dataset.total).toFixed(2);
                        syncDebtAmountFromUsd();
                    }
                }
            });

            paymentClientSelect.addEventListener('change', function() {
                filterPaymentDebts();
                updatePaymentClientInfo();
            });

            paymentDebtSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];

                if (option && option.value && option.dataset.remaining) {
                    const currentAmount = parseFloat(paymentAmountInput.value) || 0;

                    if (currentAmount <= 0) {
                        paymentAmountInput.value = parseFloat(option.dataset.remaining).toFixed(2);
                        syncPaymentAmountFromUsd();
                    }
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeDebtSheet();
                    closePaymentSheet();
                }
            });

            if (openDebtFormOnLoad) {
                openDebtSheet();
            }

            if (openPaymentFormOnLoad) {
                openPaymentSheet(queryClientId || null, null);
            }

            function updateClientMode() {
                const newFields = document.getElementById('new-client-fields');
                const newName = document.getElementById('new-client-name');
                const isNew = clientSelect.value === 'new';

                newFields.classList.toggle('hidden', !isNew);
                newName.required = isNew;
                saleSelect.disabled = isNew;

                if (isNew) {
                    saleSelect.value = '';
                }

                filterSalesByClient(isNew ? null : clientSelect.value);
            }
        });

        function filterSalesByClient(clientId) {
            const saleSelect = document.getElementById('debt-sale-select');

            Array.from(saleSelect.options).forEach(function(option) {
                if (!option.value) return;

                const isGeneric = !option.dataset.clientId;
                const matches = !clientId || isGeneric || String(option.dataset.clientId) === String(clientId);

                option.hidden = !matches;
                option.disabled = !matches;
            });

            const selectedOption = saleSelect.options[saleSelect.selectedIndex];

            if (selectedOption && selectedOption.disabled) {
                saleSelect.value = '';
            }
        }

        function syncDebtAmountFromUsd() {
            const usdInput = document.getElementById('debt-amount');
            const bsInput = document.getElementById('debt-amount-bs');
            const usd = parseFloat(usdInput.value);
            bsInput.value = !isNaN(usd) && usdRate > 0 ? (usd * usdRate).toFixed(2) : '';
        }

        function syncDebtAmountFromBs() {
            const usdInput = document.getElementById('debt-amount');
            const bsInput = document.getElementById('debt-amount-bs');
            const bs = parseFloat(bsInput.value);
            usdInput.value = !isNaN(bs) && usdRate > 0 ? (bs / usdRate).toFixed(2) : '';
        }

        function syncPaymentAmountFromUsd() {
            const usdInput = document.getElementById('payment-amount');
            const bsInput = document.getElementById('payment-amount-bs');
            const usd = parseFloat(usdInput.value);
            bsInput.value = !isNaN(usd) && usdRate > 0 ? (usd * usdRate).toFixed(2) : '';
        }

        function syncPaymentAmountFromBs() {
            const usdInput = document.getElementById('payment-amount');
            const bsInput = document.getElementById('payment-amount-bs');
            const bs = parseFloat(bsInput.value);
            usdInput.value = !isNaN(bs) && usdRate > 0 ? (bs / usdRate).toFixed(2) : '';
        }

        function updatePaymentClientInfo() {
            const select = document.getElementById('payment-client-select');
            const info = document.getElementById('payment-client-info');
            const option = select.options[select.selectedIndex];

            if (!option || !option.value) {
                info.classList.add('hidden');
                return;
            }

            const debt = parseFloat(option.dataset.debt) || 0;
            info.innerHTML = `Deuda actual: <strong class="text-rose-600">$${debt.toFixed(2)}</strong> <span class="text-slate-400">/ Bs. ${(debt * usdRate).toFixed(2)}</span>`;
            info.classList.remove('hidden');
        }

        function filterPaymentDebts() {
            const clientSelect = document.getElementById('payment-client-select');
            const debtSelect = document.getElementById('payment-debt-select');
            const clientId = clientSelect.value;

            Array.from(debtSelect.options).forEach(function(option) {
                if (!option.value) return;

                const matches = !clientId || String(option.dataset.clientId) === String(clientId);

                option.hidden = !matches;
                option.disabled = !matches;
            });

            const selectedOption = debtSelect.options[debtSelect.selectedIndex];

            if (selectedOption && selectedOption.disabled) {
                debtSelect.value = '';
            }
        }

        function openPaymentSheet(clientId = null, debtId = null) {
            const sheet = document.getElementById('payment-sheet');
            const backdrop = document.getElementById('payment-backdrop');
            const clientSelect = document.getElementById('payment-client-select');
            const debtSelect = document.getElementById('payment-debt-select');
            const amountInput = document.getElementById('payment-amount');

            if (clientId) {
                clientSelect.value = String(clientId);
            }

            filterPaymentDebts();

            if (debtId) {
                debtSelect.value = String(debtId);
                const option = debtSelect.options[debtSelect.selectedIndex];

                if (option && option.dataset.remaining) {
                    amountInput.value = parseFloat(option.dataset.remaining).toFixed(2);
                    syncPaymentAmountFromUsd();
                }
            }

            updatePaymentClientInfo();

            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');
            document.body.style.overflow = 'hidden';
        }

        function closePaymentSheet() {
            const sheet = document.getElementById('payment-sheet');
            const backdrop = document.getElementById('payment-backdrop');

            if (!sheet || !backdrop) return;

            sheet.classList.remove('translate-y-0');
            sheet.classList.add('translate-y-full');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }

        function openDebtSheet() {
            const sheet = document.getElementById('debt-sheet');
            const backdrop = document.getElementById('debt-backdrop');

            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');
            document.body.style.overflow = 'hidden';
        }

        function closeDebtSheet() {
            const sheet = document.getElementById('debt-sheet');
            const backdrop = document.getElementById('debt-backdrop');

            sheet.classList.remove('translate-y-0');
            sheet.classList.add('translate-y-full');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }
    </script>
@endsection
