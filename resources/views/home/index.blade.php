@extends('layout.app')

@section('styles')
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .period-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            line-height: 1rem;
            font-weight: 600;
            color: #44474e;
            transition: background-color .2s ease, color .2s ease, box-shadow .2s ease;
            white-space: nowrap;
        }

        .period-btn:hover {
            color: #0d1c2f;
        }

        .period-btn.is-active {
            background-color: #002046;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .12);
        }


        @keyframes sheet-up {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .sheet-panel {
            animation: sheet-up .28s cubic-bezier(.16, 1, .3, 1);
        }

        body.modal-open {
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-5">

        <!-- Cabecera contextual -->
        <section class="flex flex-col gap-3">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        <span class="text-[11px] font-label font-semibold uppercase tracking-wider text-secondary">Bodega
                            • Activa</span>
                    </div>
                    <h1 class="text-3xl font-extrabold font-headline tracking-tight text-on-surface">Panel de Control</h1>
                    <p class="text-sm text-on-surface-variant mt-0.5">Resumen de stock y balance operacional</p>
                </div>
                <button id="movement-trigger" type="button"
                    class="shrink-0 inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-label font-semibold shadow-md active:scale-95 transition-transform">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Movimiento</span>
                </button>
            </div>

            <!-- Selector de periodo -->
            <div class="mt-1 flex items-center justify-between gap-1.5">
                <div id="period-selector"
                    class="inline-flex min-w-0 max-w-full overflow-x-auto p-1 rounded-xl bg-surface-container no-scrollbar">
                    <button type="button" class="period-btn" data-period="today">Hoy</button>
                    <button type="button" class="period-btn is-active" data-period="week">Semana</button>
                    <button type="button" class="period-btn" data-period="month">Mes</button>
                    <button type="button" class="period-btn" data-period="custom">Personalizado</button>
                </div>
            </div>

            <!-- Rango personalizado -->
            <div id="custom-range" class="hidden items-end gap-2 rounded-2xl bg-surface-container-low p-3">
                <label class="flex-1 min-w-0 flex flex-col gap-1">
                    <span class="text-[10px] font-label font-semibold uppercase tracking-wider text-on-surface-variant">Desde</span>
                    <input type="date" id="start_date"
                        class="w-full min-w-0 px-3 py-2 bg-surface rounded-xl border border-outline-variant/40 text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                </label>
                <label class="flex-1 min-w-0 flex flex-col gap-1">
                    <span class="text-[10px] font-label font-semibold uppercase tracking-wider text-on-surface-variant">Hasta</span>
                    <input type="date" id="end_date"
                        class="w-full min-w-0 px-3 py-2 bg-surface rounded-xl border border-outline-variant/40 text-xs focus:outline-none focus:ring-2 focus:ring-primary/40">
                </label>
                <button type="button" id="apply-custom-range"
                    class="shrink-0 px-3 py-2 rounded-xl bg-primary text-on-primary text-xs font-label font-semibold active:scale-95 transition-transform">Aplicar</button>
            </div>
        </section>

        <!-- KPIs de flujo de inventario -->
        <section class="-mx-4 lg:mx-0">
            <div
                class="flex items-stretch gap-3 px-4 pb-1 overflow-x-auto no-scrollbar snap-x snap-mandatory lg:px-0 lg:overflow-visible">
                <!-- Compras -->
                <a href="{{ route('entries') }}"
                    class="w-[150px] shrink-0 snap-start lg:w-auto lg:flex-1 rounded-2xl bg-surface-container-lowest p-3.5 shadow-sm border border-outline-variant/20 flex flex-col justify-between active:scale-[0.98] transition-transform">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant">Compras</span>
                        <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-primary-container">
                            <span class="material-symbols-outlined text-[18px]">south</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-2xl font-extrabold font-headline text-on-surface leading-none">{{ number_format($entries) }}</span>
                        <div class="flex items-center gap-1 mt-1 text-on-surface-variant">
                            <span class="material-symbols-outlined text-[13px]">check_circle</span>
                            <span class="text-[10px] font-label">Total registrado</span>
                        </div>
                    </div>
                </a>

                <!-- Ventas -->
                <a href="{{ route('outputs') }}"
                    class="w-[150px] shrink-0 snap-start lg:w-auto lg:flex-1 rounded-2xl bg-error-container/40 p-3.5 shadow-sm border border-error-container/60 flex flex-col justify-between active:scale-[0.98] transition-transform">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-label font-bold uppercase tracking-wider text-on-error-container">Ventas</span>
                        <div class="w-8 h-8 rounded-lg bg-error-container flex items-center justify-center text-error">
                            <span class="material-symbols-outlined text-[18px]">north</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-2xl font-extrabold font-headline text-on-surface leading-none">{{ number_format($outputs) }}</span>
                        <div class="flex items-center gap-1 mt-1 text-on-error-container">
                            <span class="material-symbols-outlined text-[13px]">check_circle</span>
                            <span class="text-[10px] font-label">Total registrado</span>
                        </div>
                    </div>
                </a>

                <!-- Inventario -->
                <a href="{{ route('inventory') }}"
                    class="w-[158px] shrink-0 snap-start lg:w-auto lg:flex-1 rounded-2xl bg-secondary-fixed/30 p-3.5 shadow-sm border border-secondary-fixed/60 flex flex-col justify-between active:scale-[0.98] transition-transform">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-label font-bold uppercase tracking-wider text-on-secondary-container">Inventario</span>
                        <div class="w-8 h-8 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-extrabold font-headline text-on-surface leading-none">{{ number_format($inventories) }}</span>
                            <span class="text-[11px] font-semibold text-on-surface-variant">uds</span>
                        </div>
                        <div class="flex items-center gap-1 mt-1 text-secondary">
                            <span class="material-symbols-outlined text-[13px]">check_circle</span>
                            <span class="text-[10px] font-label">Disponible</span>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- Métricas de ventas -->
        <section class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold font-headline text-on-surface">Métricas de Ventas</h2>
                <span class="text-[11px] font-label text-on-surface-variant">Liquidación del periodo</span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Total ventas -->
                <div
                    class="col-span-2 rounded-2xl bg-surface-container-lowest p-4 shadow-sm border border-outline-variant/20 flex items-center justify-between gap-3">
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-1.5 text-on-surface-variant mb-1">
                            <span class="material-symbols-outlined text-[18px] text-primary">payments</span>
                            <span class="text-[10px] font-label font-bold uppercase tracking-wide">Total Ventas</span>
                        </div>
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span id="total-sales" class="text-2xl font-extrabold font-headline text-on-surface"><span class="skeleton inline-block h-8 w-28 rounded align-middle"></span></span>
                            <span id="sales-change" class="hidden"></span>
                        </div>
                        <span id="sales-caption" class="text-xs text-on-surface-variant mt-1"><span class="skeleton inline-block h-4 w-36 rounded align-middle"></span></span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary-container shrink-0">
                        <span class="material-symbols-outlined text-[26px]">point_of_sale</span>
                    </div>
                </div>

                <!-- Ganancias -->
                <div
                    class="rounded-2xl bg-surface-container-lowest p-3.5 shadow-sm border border-outline-variant/20 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant">Ganancias</span>
                        <span class="material-symbols-outlined text-[18px] text-secondary">monetization_on</span>
                    </div>
                    <div class="mt-2">
                        <span id="total-profits" class="text-2xl font-extrabold font-headline text-secondary block"><span class="skeleton inline-block h-8 w-24 rounded align-middle"></span></span>
                        <div class="flex items-center gap-1 mt-1 text-on-surface-variant text-[11px] font-label">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            <span id="total-margin"><span class="skeleton inline-block h-4 w-20 rounded align-middle"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Transacciones -->
                <div
                    class="rounded-2xl bg-surface-container-lowest p-3.5 shadow-sm border border-outline-variant/20 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant">Transacciones</span>
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">receipt_long</span>
                    </div>
                    <div class="mt-2">
                        <span id="total-outputs" class="text-2xl font-extrabold font-headline text-on-surface block"><span class="skeleton inline-block h-8 w-14 rounded align-middle"></span></span>
                        <div class="flex items-center gap-1 mt-1 text-on-surface-variant text-[11px] font-label">
                            <span class="material-symbols-outlined text-[13px]">schedule</span>
                            <span id="avg-transactions"><span class="skeleton inline-block h-4 w-24 rounded align-middle"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
            <!-- Gráfico ventas vs ganancias -->
            <section>
                <div class="rounded-2xl bg-surface-container-lowest p-4 shadow-sm border border-outline-variant/20">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="text-base font-bold font-headline text-on-surface">Ventas vs Ganancias</h3>
                            <p class="text-xs text-on-surface-variant">Comportamiento del periodo</p>
                        </div>
                        <div class="flex items-center gap-3 pt-0.5">
                            <div class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color:#002046"></span>
                                <span class="text-[10px] font-label text-on-surface-variant">Venta</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color:#006c49"></span>
                                <span class="text-[10px] font-label text-on-surface-variant">Ganancia</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative min-h-[260px]">
                        <div id="chart-sales" class="w-full -ml-1"></div>
                        <div id="chart-loading" class="absolute inset-0 skeleton rounded-xl"></div>
                    </div>
                </div>
            </section>

            <!-- Top 3 productos -->
            <section>
                <div class="flex items-end justify-between mb-3">
                    <div>
                        <h3 class="text-base font-bold font-headline text-on-surface">Top 3 Productos</h3>
                        <p class="text-xs text-on-surface-variant">Mayor rotación del periodo</p>
                    </div>
                    <a href="{{ route('products') }}"
                        class="text-[11px] font-label font-bold text-secondary inline-flex items-center gap-0.5">
                        Ver catálogo <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
                <div id="top-products" class="flex min-h-[290px] flex-col gap-2.5">
                    @for ($i = 0; $i < 3; $i++)
                        <div
                            class="rounded-2xl bg-surface-container-lowest p-3.5 shadow-sm border border-outline-variant/20 flex flex-col gap-2.5">
                            <div class="flex items-center gap-3">
                                <div class="skeleton w-11 h-11 rounded-xl shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton h-3.5 w-2/3 rounded"></div>
                                    <div class="skeleton h-2.5 w-1/3 rounded"></div>
                                </div>
                            </div>
                            <div class="skeleton h-1.5 w-full rounded-full"></div>
                        </div>
                    @endfor
                </div>
            </section>
        </div>

        <!-- Acciones rápidas -->
        <section>
            <div class="p-4 rounded-2xl bg-primary-container text-on-primary shadow-lg flex flex-col gap-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[22px] text-secondary-fixed">bolt</span>
                        <span class="text-base font-headline font-bold">Acciones Rápidas</span>
                    </div>
                    <span class="text-[11px] font-label text-on-primary-container">Atajos de piso</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('entries.create') }}"
                        class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl bg-white/10 hover:bg-white/15 active:scale-95 transition-all text-center">
                        <span class="material-symbols-outlined text-[24px] text-secondary-fixed">add_box</span>
                        <span class="text-[11px] font-label leading-tight">Nueva Compra</span>
                    </a>
                    <a href="{{ route('outputs.create') }}"
                        class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl bg-white/10 hover:bg-white/15 active:scale-95 transition-all text-center">
                        <span class="material-symbols-outlined text-[24px] text-surface-container-highest">remove_circle</span>
                        <span class="text-[11px] font-label leading-tight">Nueva Venta</span>
                    </a>
                    <a href="{{ route('inventory') }}"
                        class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl bg-white/10 hover:bg-white/15 active:scale-95 transition-all text-center">
                        <span class="material-symbols-outlined text-[24px] text-secondary-container">inventory_2</span>
                        <span class="text-[11px] font-label leading-tight">Inventario</span>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <!-- Bottom sheet: nuevo movimiento -->
    <div id="movement-modal" class="fixed inset-0 z-[70] hidden" role="dialog" aria-modal="true"
        aria-labelledby="movement-modal-title">
        <div class="absolute inset-0 bg-primary-container/60 backdrop-blur-sm" data-close-modal></div>
        <div class="sheet-panel absolute inset-x-0 bottom-0 w-full bg-surface-container-lowest rounded-t-3xl p-5 shadow-2xl flex flex-col gap-4 max-h-[85dvh] overflow-y-auto"
            style="padding-bottom: calc(1.25rem + env(safe-area-inset-bottom));">
            <div class="w-12 h-1.5 bg-outline-variant/60 rounded-full mx-auto mb-1"></div>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 id="movement-modal-title" class="text-lg font-bold font-headline text-on-surface">Registrar Movimiento</h3>
                    <p class="text-xs text-on-surface-variant">Selecciona el tipo de operación de bodega</p>
                </div>
                <button type="button" data-close-modal
                    class="w-9 h-9 shrink-0 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform"
                    aria-label="Cerrar">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-1">
                <a href="{{ route('entries.create') }}"
                    class="p-4 rounded-2xl bg-surface-container flex flex-col items-center gap-2 hover:bg-surface-container-high active:scale-[0.98] transition-all text-center">
                    <div class="w-12 h-12 rounded-full bg-primary-container text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">input</span>
                    </div>
                    <span class="text-sm font-bold font-headline text-on-surface">Compra</span>
                    <span class="text-[11px] text-on-surface-variant leading-tight">Recepción de proveedores</span>
                </a>
                <a href="{{ route('outputs.create') }}"
                    class="p-4 rounded-2xl bg-surface-container flex flex-col items-center gap-2 hover:bg-surface-container-high active:scale-[0.98] transition-all text-center">
                    <div class="w-12 h-12 rounded-full bg-error text-on-error flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">output</span>
                    </div>
                    <span class="text-sm font-bold font-headline text-on-surface">Venta</span>
                    <span class="text-[11px] text-on-surface-variant leading-tight">Despacho o merma</span>
                </a>
            </div>
            <a href="{{ route('entries') }}"
                class="p-3 rounded-2xl bg-surface-container-low flex items-center gap-3 active:scale-[0.99] transition-transform">
                <span class="material-symbols-outlined text-[22px] text-on-surface-variant">receipt_long</span>
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-semibold text-on-surface block">Ver historial de movimientos</span>
                    <span class="text-[11px] text-on-surface-variant">Compras y ventas registradas</span>
                </div>
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">chevron_right</span>
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (function() {
            const state = { period: 'week', start: null, end: null };
            let salesChart = null;

            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const applyButton = document.getElementById('apply-custom-range');
            const customRange = document.getElementById('custom-range');

            document.addEventListener('DOMContentLoaded', function() {
                loadStatistics('week');
            });

            document.querySelectorAll('.period-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    setPeriod(button.dataset.period);
                });
            });

            applyButton.addEventListener('click', function() {
                if (!startInput.value || !endInput.value) {
                    return;
                }

                if (startInput.value > endInput.value) {
                    const temp = startInput.value;
                    startInput.value = endInput.value;
                    endInput.value = temp;
                }

                state.start = startInput.value;
                state.end = endInput.value;
                loadStatistics('custom', state.start, state.end);
            });

            const modal = document.getElementById('movement-modal');
            const movementTrigger = document.getElementById('movement-trigger');

            movementTrigger.addEventListener('click', openModal);
            document.querySelectorAll('[data-close-modal]').forEach(function(element) {
                element.addEventListener('click', closeModal);
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            function setPeriod(period) {
                state.period = period;

                document.querySelectorAll('.period-btn').forEach(function(button) {
                    button.classList.toggle('is-active', button.dataset.period === period);
                });

                if (period === 'custom') {
                    customRange.classList.remove('hidden');
                    customRange.classList.add('flex');

                    if (!startInput.value || !endInput.value) {
                        setDefaultCustomDates();
                    }
                    return;
                }

                customRange.classList.add('hidden');
                customRange.classList.remove('flex');
                loadStatistics(period);
            }

            function setDefaultCustomDates() {
                const today = new Date();
                const dayOffset = (today.getDay() + 6) % 7;
                const monday = new Date(today);
                monday.setDate(today.getDate() - dayOffset);
                const sunday = new Date(monday);
                sunday.setDate(monday.getDate() + 6);

                startInput.value = toISODate(monday);
                endInput.value = toISODate(sunday);
            }

            function toISODate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function setSkeleton(id, size) {
                const element = document.getElementById(id);

                if (element) {
                    element.innerHTML = `<span class="skeleton inline-block ${size} rounded align-middle"></span>`;
                }
            }

            function showStatisticsSkeleton() {
                setSkeleton('date-range-label', 'h-4 w-24');
                setSkeleton('total-sales', 'h-8 w-28');
                setSkeleton('total-profits', 'h-8 w-24');
                setSkeleton('total-outputs', 'h-8 w-14');
                setSkeleton('total-margin', 'h-4 w-20');
                setSkeleton('avg-transactions', 'h-4 w-24');
                setSkeleton('sales-caption', 'h-4 w-36');

                const change = document.getElementById('sales-change');

                if (change) {
                    change.className = 'hidden';
                    change.innerHTML = '';
                }

                const chartLoading = document.getElementById('chart-loading');

                if (chartLoading) {
                    chartLoading.classList.remove('hidden');
                }

                const topProducts = document.getElementById('top-products');

                if (topProducts) {
                    topProducts.innerHTML = Array.from({ length: 3 }).map(function() {
                        return `<div class="rounded-2xl bg-surface-container-lowest p-3.5 shadow-sm border border-outline-variant/20 flex flex-col gap-2.5">
                            <div class="flex items-center gap-3">
                                <div class="skeleton w-11 h-11 rounded-xl shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton h-3.5 w-2/3 rounded"></div>
                                    <div class="skeleton h-2.5 w-1/3 rounded"></div>
                                </div>
                            </div>
                            <div class="skeleton h-1.5 w-full rounded-full"></div>
                        </div>`;
                    }).join('');
                }
            }

            function hideChartSkeleton() {
                const chartLoading = document.getElementById('chart-loading');

                if (chartLoading) {
                    chartLoading.classList.add('hidden');
                }
            }

            async function loadStatistics(period, startDate = null, endDate = null) {
                let url = '{{ route('statistics') }}?period=' + encodeURIComponent(period);

                if (period === 'custom' && startDate && endDate) {
                    url += '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
                }

                showStatisticsSkeleton();

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('No se pudieron cargar las estadísticas');
                    }

                    const data = await response.json();
                    updateTotals(data);
                    updateRange(data.range);
                    renderChart(data);
                    hideChartSkeleton();
                    renderTopProducts(data.topProducts);
                } catch (error) {
                    console.error(error);
                    document.getElementById('sales-caption').textContent = 'Sin datos disponibles';
                    document.getElementById('total-sales').textContent = '$0.00';
                    document.getElementById('total-profits').textContent = '$0.00';
                    document.getElementById('total-outputs').textContent = '0';
                    document.getElementById('total-margin').textContent = 'Margen 0%';
                    document.getElementById('avg-transactions').textContent = 'Prom. 0/día';
                    hideChartSkeleton();
                    renderTopProducts([]);
                }
            }

            function updateTotals(data) {
                const totals = data.totals || {};
                const previous = data.previous || {};
                const changes = data.changes || {};

                document.getElementById('total-sales').textContent = formatMoney(totals.sales);
                document.getElementById('total-profits').textContent = formatMoney(totals.profits);
                document.getElementById('total-outputs').textContent = formatNumber(totals.outputs);

                const margin = Number(totals.sales) > 0 ? (Number(totals.profits) / Number(totals.sales)) * 100 : 0;
                document.getElementById('total-margin').textContent = 'Margen ' + margin.toFixed(1) + '%';

                const days = data.range && data.range.days ? data.range.days : 1;
                document.getElementById('avg-transactions').textContent = 'Prom. ' +
                    Math.round(Number(totals.outputs || 0) / days) + '/día';

                renderChange(document.getElementById('sales-change'), changes.sales);

                const caption = document.getElementById('sales-caption');
                if (Number(previous.sales) > 0) {
                    const difference = Number(totals.sales || 0) - Number(previous.sales);
                    caption.textContent = (difference >= 0 ? '+' : '-') + formatMoney(Math.abs(difference)) +
                        ' vs periodo anterior';
                } else {
                    caption.textContent = 'Periodo actual';
                }
            }

            function renderChange(element, change) {
                if (change === null || change === undefined) {
                    element.className = 'hidden';
                    return;
                }

                const positive = Number(change) >= 0;
                element.className = 'inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-label font-bold ' +
                    (positive ? 'bg-secondary-container text-on-secondary-container' :
                    'bg-error-container text-on-error-container');
                element.innerHTML =
                    '<span class="material-symbols-outlined text-[13px]">' +
                    (positive ? 'arrow_upward' : 'arrow_downward') + '</span>' +
                    Math.abs(Number(change)).toFixed(1) + '%';
            }

            function updateRange(range) {
                if (!range || !range.start || !range.end) {
                    return;
                }

                const label = document.getElementById('date-range-label');

                if (!label) {
                    return;
                }

                const start = new Date(range.start + 'T00:00:00');
                const end = new Date(range.end + 'T00:00:00');
                const formatter = new Intl.DateTimeFormat('es-ES', {
                    day: 'numeric',
                    month: 'short'
                });

                label.textContent = formatter.format(start).replace('.', '') + ' - ' + formatter.format(end)
                    .replace('.', '');
            }

            function renderChart(data) {
                if (typeof ApexCharts === 'undefined') {
                    return;
                }

                const options = {
                    chart: {
                        type: 'area',
                        height: 260,
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    colors: ['#002046', '#006c49'],
                    stroke: { curve: 'smooth', width: 2.5 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.02,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    grid: {
                        borderColor: '#e6eeff',
                        strokeDashArray: 4,
                        padding: { left: 0, right: 0 }
                    },
                    xaxis: {
                        categories: data.labels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { fontSize: '10px' } }
                    },
                    yaxis: {
                        tickAmount: 3,
                        labels: {
                            style: { fontSize: '10px' },
                            formatter: function(value) {
                                return '$' + Number(value).toLocaleString('es-ES', {
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return formatMoney(value);
                            }
                        }
                    },
                    legend: { show: false },
                    series: [
                        { name: 'Ventas', data: data.sales },
                        { name: 'Ganancias', data: data.profits }
                    ]
                };

                if (salesChart) {
                    salesChart.updateOptions(options);
                } else {
                    salesChart = new ApexCharts(document.querySelector('#chart-sales'), options);
                    salesChart.render();
                }
            }

            function renderTopProducts(products) {
                const container = document.getElementById('top-products');

                if (!products || products.length === 0) {
                    container.innerHTML =
                        '<div class="rounded-2xl bg-surface-container-lowest p-6 border border-outline-variant/20 text-center">' +
                        '<span class="material-symbols-outlined text-3xl text-outline mb-1">inventory_2</span>' +
                        '<p class="text-xs text-on-surface-variant">Sin productos vendidos en este periodo</p></div>';
                    return;
                }

                const maxQuantity = Math.max.apply(null, products.map(function(product) {
                    return Number(product.quantity) || 0;
                }).concat([1]));

                const barColors = ['bg-secondary', 'bg-primary-fixed-dim', 'bg-error'];
                const badgeColors = [
                    'bg-secondary-container text-on-secondary-container',
                    'bg-surface-container-high text-on-surface',
                    'bg-error-container text-on-error-container'
                ];

                container.innerHTML = products.map(function(product, index) {
                    const quantity = Number(product.quantity) || 0;
                    const unit = product.unit || 'ventas';
                    const width = Math.max(6, Math.round((quantity / maxQuantity) * 100));
                    const barColor = barColors[index % barColors.length];
                    const badgeColor = badgeColors[index % badgeColors.length];

                    return `
                        <div class="rounded-2xl bg-surface-container-lowest p-3.5 shadow-sm border border-outline-variant/20 flex flex-col gap-2.5">
                            <div class="flex items-center gap-3">
                                <div class="relative w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[22px] text-on-surface-variant">inventory_2</span>
                                    <span class="absolute -top-1.5 -left-1.5 w-5 h-5 rounded-full ${badgeColor} text-[10px] font-label font-bold flex items-center justify-center shadow-sm">${index + 1}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-sm font-bold font-headline text-on-surface truncate">${escapeHtml(product.name)}</h4>
                                        <span class="text-xs font-bold font-label text-secondary whitespace-nowrap">${formatNumber(quantity)} ${escapeHtml(unit)}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-0.5">
                                        <span class="text-[11px] text-on-surface-variant">Ventas registradas</span>
                                        <span class="text-[11px] font-label text-on-surface-variant">#${index + 1}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-surface-container overflow-hidden">
                                <div class="h-full rounded-full ${barColor}" style="width: ${width}%"></div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            function formatMoney(value) {
                const number = Number(value) || 0;
                return '$' + number.toLocaleString('es-ES', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function formatNumber(value) {
                return (Number(value) || 0).toLocaleString('es-ES');
            }

            function escapeHtml(value) {
                return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, function(character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [character];
                });
            }

            function openModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('modal-open');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('modal-open');
            }
        })();
    </script>
@endsection
