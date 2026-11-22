<!DOCTYPE html>
<html class="min-h-full bg-surface" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Cobros SaaS | Superadministrador</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-full bg-surface font-jakarta text-slate-800 antialiased">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-brand-navy">Cobros de Bodegas</h1>
                    <p class="text-[11px] font-medium text-slate-500">Mensualidad: ${{ number_format(5, 2) }} por
                        bodega</p>
                </div>
            </div>

            <a href="{{ route('superadmin.dashboard') }}"
                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-navy px-3 py-2 text-xs font-bold text-white active:scale-[0.98]">
                <span class="material-symbols-outlined text-[16px] text-brand-accent">dashboard</span>
                Panel principal
            </a>
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

        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Ingreso mensual</p>
                <p class="mt-1 text-2xl font-extrabold text-brand-navy">${{ number_format($monthlyRecurring, 2) }}</p>
                <p class="mt-1 text-[10px] font-medium text-blue-600">{{ $activeTenants }} bodegas activas</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Total cobrado</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-700">${{ number_format($totalCollected, 2) }}</p>
                <p class="mt-1 text-[10px] font-medium text-emerald-600">Pagos registrados</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Pendiente total</p>
                <p class="mt-1 text-2xl font-extrabold text-rose-600">${{ number_format($totalPending, 2) }}</p>
                <p class="mt-1 text-[10px] font-medium text-rose-500">Por cobrar</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-brand-card">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Costo mensual</p>
                <p class="mt-1 text-2xl font-extrabold text-brand-navy">$5.00</p>
                <p class="mt-1 text-[10px] font-medium text-slate-400">Por cada bodega</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-5">
            <section class="lg:col-span-2">
                <div id="payment-form-card" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-brand-card">
                    <h2 class="text-sm font-extrabold text-brand-navy">Registrar pago</h2>
                    <p class="mt-1 text-[11px] text-slate-500">Registra un abono o mensualidad recibida.</p>

                    <form method="POST" action="{{ route('superadmin.billing.payments.store') }}" class="mt-4 space-y-3">
                        @csrf

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Bodega</label>
                            <select name="tenant_id" id="billing-tenant" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                                <option value="">-- Seleccionar bodega --</option>
                                @foreach ($billing as $row)
                                    <option value="{{ $row['tenant']->id }}"
                                        data-balance="{{ $row['balance'] }}"
                                        @selected(old('tenant_id') == $row['tenant']->id)>
                                        {{ $row['tenant']->name }} · {{ $row['up_to_date'] ? 'Al día' : 'Debe $' . number_format($row['balance'], 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="billing-balance-hint" class="mt-1 text-[10px] font-semibold text-slate-400">Selecciona una
                                bodega para ver su saldo.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Mes</label>
                                <input type="month" name="period" required
                                    value="{{ old('period', now()->format('Y-m')) }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                            </div>
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Monto ($)</label>
                                <input type="number" name="amount" step="0.01" min="0.01" required
                                    value="{{ old('amount', 5) }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-2.5 text-xs font-extrabold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Fecha de
                                pago</label>
                            <input type="date" name="paid_at" required value="{{ old('paid_at', now()->toDateString()) }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Notas</label>
                            <textarea name="notes" rows="2" placeholder="Referencia, método de pago, etc. (opcional)"
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-medium text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-navy px-4 py-3 text-xs font-extrabold uppercase tracking-wide text-white active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[18px] text-brand-accent">payments</span>
                            Guardar pago
                        </button>
                    </form>
                </div>
            </section>

            <section class="lg:col-span-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-brand-card">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-extrabold text-brand-navy">Estado de cobro</h2>
                        <span class="text-[10px] font-semibold text-slate-400">Mensualidad $5</span>
                    </div>

                    <div class="mt-3 space-y-2.5">
                        @forelse ($billing as $row)
                            <div class="rounded-xl border {{ $row['up_to_date'] ? 'border-emerald-100 bg-emerald-50/40' : 'border-rose-100 bg-rose-50/30' }} p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <h3 class="text-xs font-extrabold text-brand-navy">{{ $row['tenant']->name }}</h3>
                                            <span
                                                class="rounded px-1.5 py-0.5 text-[9px] font-bold {{ $row['tenant']->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">{{ $row['tenant']->is_active ? 'Activa' : 'Inactiva' }}</span>
                                            <span
                                                class="rounded px-1.5 py-0.5 text-[9px] font-bold {{ $row['up_to_date'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $row['up_to_date'] ? 'Al día' : 'Pendiente' }}</span>
                                        </div>
                                        <p class="mt-1 text-[10px] text-slate-500">{{ $row['months'] }} mes(es) facturado(s)
                                            · Debe ${{ number_format($row['due'], 2) }} · Pagado
                                            ${{ number_format($row['paid'], 2) }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        @if ($row['up_to_date'])
                                            <span class="text-xs font-extrabold text-emerald-700">$0.00</span>
                                            <p class="text-[9px] text-emerald-600 font-medium">pendiente</p>
                                        @else
                                            <span class="text-xs font-extrabold text-rose-600">${{ number_format($row['balance'], 2) }}</span>
                                            <p class="text-[9px] text-rose-500 font-medium">pendiente</p>
                                        @endif
                                        <button type="button" onclick="selectBillingTenant({{ $row['tenant']->id }})"
                                            class="mt-1 inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-[9px] font-bold text-brand-navy hover:bg-slate-50">
                                            <span class="material-symbols-outlined text-[12px]">add_card</span>
                                            Pago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs font-semibold text-slate-500">
                                No hay bodegas registradas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <section class="mt-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-brand-card">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-brand-navy">Pagos recientes</h2>
                    <span class="text-[10px] font-semibold text-slate-400">Últimos 20</span>
                </div>

                <div class="mt-3 space-y-2">
                    @forelse ($recentPayments as $payment)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-brand-navy">{{ $payment->tenant->name ?? 'Bodega' }}</p>
                                <p class="text-[10px] text-slate-500">Mes {{ $payment->period }} ·
                                    {{ $payment->paid_at->format('d/m/Y') }}
                                    @if ($payment->notes)
                                        · {{ $payment->notes }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-xs font-extrabold text-emerald-700 shrink-0">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs font-semibold text-slate-400">Sin pagos registrados.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <script>
        const billingTenantSelect = document.getElementById('billing-tenant');
        const billingBalanceHint = document.getElementById('billing-balance-hint');

        function updateBillingHint() {
            const option = billingTenantSelect.options[billingTenantSelect.selectedIndex];

            if (!option || !option.value) {
                billingBalanceHint.textContent = 'Selecciona una bodega para ver su saldo.';
                billingBalanceHint.className = 'mt-1 text-[10px] font-semibold text-slate-400';
                return;
            }

            const balance = parseFloat(option.dataset.balance) || 0;
            const owes = balance > 0;
            billingBalanceHint.textContent = owes
                ? `Saldo pendiente: $${balance.toFixed(2)}`
                : `Esta bodega está al día${balance < 0 ? ' (crédito $' + Math.abs(balance).toFixed(2) + ')' : ''}.`;
            billingBalanceHint.className = 'mt-1 text-[10px] font-semibold ' + (owes ? 'text-rose-600' : 'text-emerald-600');
        }

        billingTenantSelect.addEventListener('change', updateBillingHint);

        function selectBillingTenant(tenantId) {
            billingTenantSelect.value = String(tenantId);
            updateBillingHint();
            document.getElementById('payment-form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        updateBillingHint();
    </script>
</body>

</html>
