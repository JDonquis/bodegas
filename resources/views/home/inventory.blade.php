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

        #detail-sheet {
            transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #detail-backdrop {
            transition: opacity 0.3s ease-out;
        }
    </style>
@endsection

@section('content')
    @php
        Carbon\Carbon::setLocale('es');

        $filterOptions = [
            ['value' => 'all', 'label' => 'Todos', 'count' => $totalCount],
            ['value' => 'low', 'label' => 'Stock bajo', 'count' => $lowStockCount],
            ['value' => 'critical', 'label' => 'Críticos', 'count' => $criticalCount],
            ['value' => 'rotation', 'label' => 'Mayor rotación', 'count' => null],
        ];

        $activeFilter = in_array($filter, ['low', 'critical', 'rotation'], true) ? $filter : 'all';
    @endphp

    <div class="mx-auto flex w-full max-w-md flex-col gap-5 font-jakarta">

        <section class="space-y-1">
            <div class="flex items-center justify-between gap-2">
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Estado del Inventario</h1>
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80 shrink-0">
                    <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-500 animate-pulse"></span>
                    En línea
                </span>
            </div>
            <p class="text-[13px] text-slate-500 font-medium leading-relaxed">
                Monitoreo global de existencias y rendimiento financiero.
            </p>
        </section>

        <section class="space-y-3">
            <div class="relative flex items-center">
                <span
                    class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[18px] pointer-events-none">search</span>
                <input type="search" id="inventory-search" value="{{ $search ?? '' }}"
                    placeholder="Buscar por producto o código..."
                    class="w-full pl-10 pr-11 py-2.5 bg-white border border-slate-200/90 rounded-xl text-xs sm:text-[13px] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-navy/15 focus:border-brand-navy transition-all shadow-sm font-medium">
                <button type="button" aria-label="Escanear código de barras"
                    onclick="scanInventoryBarcode()"
                    class="absolute right-2 p-1.5 text-slate-500 hover:text-brand-navy bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">barcode_scanner</span>
                </button>
            </div>

            <div id="inventory-filters"
                class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5 text-xs font-semibold">
                @foreach ($filterOptions as $option)
                    @php
                        $isActive = $activeFilter === $option['value'];
                        $url = $option['value'] === 'all' ? route('inventory') : route('inventory', ['filter' => $option['value']]);
                    @endphp
                    <button type="button" data-filter="{{ $option['value'] }}" data-url="{{ $url }}"
                        class="inventory-filter-btn px-3.5 py-1.5 rounded-full whitespace-nowrap transition-colors {{ $isActive ? 'bg-brand-navy text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                        {{ $option['label'] }}@if ($option['count'] !== null) ({{ $option['count'] }})@endif
                    </button>
                @endforeach
            </div>
        </section>

        <section class="space-y-3">
            <div
                class="bg-gradient-to-r from-[#eff6ff] to-[#f5f9ff] border border-blue-100 rounded-2xl p-4 shadow-sm relative overflow-hidden flex items-center justify-between">
                <div class="space-y-0.5 min-w-0">
                    <span class="text-[11px] font-bold text-blue-800/80 uppercase tracking-wider block">Inversión
                        Total</span>
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <span class="text-2xl font-black text-brand-navy tracking-tight">${{ number_format($totalExpense, 2) }}</span>
                        <span class="text-[11px] font-medium text-slate-400">≈
                            {{ number_format($totalExpenseBs, 2) }} Bs.</span>
                    </div>
                    <p class="text-[10px] text-blue-600 font-medium">Costo retenido en almacén</p>
                </div>
                <div
                    class="w-12 h-12 rounded-xl bg-blue-600/10 border border-blue-200/50 flex items-center justify-center text-blue-700 flex-shrink-0">
                    <span class="material-symbols-outlined text-[26px]">account_balance</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div
                    class="bg-gradient-to-b from-[#eafaf1] to-[#e1f7eb] border border-emerald-200/70 rounded-2xl p-3.5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Ganancias
                            Acum.</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-600/15 flex items-center justify-center text-emerald-700 shrink-0">
                            <span class="material-symbols-outlined text-[16px]">payments</span>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <span class="text-xl font-black text-emerald-700 block leading-tight">${{ number_format($totalProfits, 2) }}</span>
                        <span class="text-[10px] text-emerald-700/80 font-semibold inline-flex items-center mt-1">
                            <span class="material-symbols-outlined text-[12px] mr-0.5">trending_up</span>
                            {{ $profitMargin }}% margen
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200/90 rounded-2xl p-3.5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Stock Global</span>
                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                            <span class="material-symbols-outlined text-[16px]">analytics</span>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="text-xl font-black text-brand-navy tracking-tight">
                            {{ number_format($totalStock) }} <span class="text-xs font-semibold text-slate-400">uds.</span>
                        </div>
                        @if ($criticalCount > 0)
                            <span class="text-[10px] text-rose-600 font-semibold inline-flex items-center mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1 animate-pulse"></span>
                                {{ $criticalCount }} crítico(s)
                            </span>
                        @else
                            <span class="text-[10px] text-emerald-600 font-semibold inline-flex items-center mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                                Estado: Óptimo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-extrabold text-brand-navy tracking-tight">Detalle de Existencias</h2>
                <span id="inventory-record-count" class="text-xs font-semibold text-slate-500">{{ $inventories->total() }} registros</span>
            </div>

            <div id="inventory-feed" class="space-y-3">
                @forelse ($inventories as $inventory)
                    @php
                        $product = $inventory->product;

                        if (! $product) {
                            continue;
                        }

                        $isWeight = $product->sale_type === 'weight';
                        $unit = $isWeight ? 'g' : 'uds';
                        $stock = (float) $inventory->stock;
                        $entries = (float) $inventory->entries;
                        $outputs = (float) $inventory->outputs;
                        $threshold = (int) ($product->low_stock_threshold ?? 10);
                        $isCritical = $stock <= 0;
                        $isLow = !$isCritical && $stock <= $threshold;
                        $soldPercent = $entries > 0 ? min(100, max(0, ($outputs / $entries) * 100)) : 0;
                        $remainingPercent = max(0, 100 - $soldPercent);
                        $stockValue = $entries > 0 ? (($inventory->expense / $entries) * $stock) : 0;
                        $stockValueBs = $stockValue * $usdRate;
                        $borderClass = $isCritical ? 'border-rose-200' : ($isLow ? 'border-amber-200' : 'border-slate-200/90');
                    @endphp

                    <article
                        class="bg-white rounded-2xl p-4 border {{ $borderClass }} shadow-sm space-y-3 relative hover:border-blue-300 transition-colors cursor-pointer"
                        onclick="showDetail({{ $inventory->id }})" role="button" tabindex="0"
                        onkeydown="if (event.key === 'Enter') showDetail({{ $inventory->id }})">
                        <div class="flex items-start justify-between gap-2">
                            <div class="space-y-0.5 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <h3 class="font-bold text-sm text-brand-navy truncate">{{ $product->name }}</h3>
                                    <span
                                        class="text-[10px] bg-slate-100 text-slate-500 font-medium px-1.5 py-0.5 rounded shrink-0">{{ $isWeight ? 'Granel' : 'Unidad' }}</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-400">SKU: <span
                                        class="text-slate-600 font-mono">{{ $product->barcode ?? 'SIN SKU' }}</span></p>
                            </div>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold flex-shrink-0 border {{ $isCritical ? 'bg-rose-50 text-rose-700 border-rose-200/80' : ($isLow ? 'bg-amber-50 text-amber-700 border-amber-200/80' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80') }}">
                                @if ($isCritical || $isLow)
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $isCritical ? 'bg-rose-500' : 'bg-amber-500' }} mr-1.5 animate-pulse"></span>
                                @endif
                                {{ number_format($stock) }} {{ $unit }}
                            </span>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <div class="flex justify-between items-center text-xs gap-2">
                                <span class="text-slate-500 text-[11px] font-medium">Relación Compras / Ventas:</span>
                                <span class="font-bold tracking-tight text-[11px]">
                                    <span class="text-blue-900 font-extrabold">{{ number_format($entries) }}</span>
                                    <span class="text-slate-300 font-normal">/</span>
                                    <span class="text-amber-600 font-extrabold">{{ number_format($outputs) }}
                                        {{ $unit }}</span>
                                </span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden flex">
                                <div class="{{ $isCritical ? 'bg-rose-500' : ($isLow ? 'bg-amber-500' : 'bg-brand-navy') }} h-full"
                                    style="width: {{ $remainingPercent }}%"></div>
                                <div class="{{ $isCritical ? 'bg-rose-300' : 'bg-amber-500' }} h-full"
                                    style="width: {{ $soldPercent }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs gap-2">
                            <div>
                                <span class="text-slate-400 text-[10px] block">Valuación en stock</span>
                                <span class="font-extrabold text-brand-navy text-xs">${{ number_format($stockValue, 2) }}
                                    <span class="font-medium text-slate-400">/ Bs.
                                        {{ number_format($stockValueBs, 2) }}</span></span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if ($isCritical)
                                    <span
                                        class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200/70 px-2 py-0.5 rounded-md">Crítico</span>
                                @elseif ($isLow)
                                    <span
                                        class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/70 px-2 py-0.5 rounded-md flex items-center">
                                        <span class="material-symbols-outlined text-[12px] mr-0.5">warning</span>
                                        Reposición requerida
                                    </span>
                                @else
                                    <span
                                        class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Saludable</span>
                                @endif
                                <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300">inventory_2</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No hay existencias para mostrar</p>
                        <p class="text-[11px] text-slate-400 mt-1">Prueba con otro filtro o término de búsqueda.</p>
                    </div>
                @endforelse
            </div>

            <div id="inventory-pagination-wrapper">
                @if ($inventories->hasPages())
                    <div id="inventory-pagination" class="pt-2 pb-2 space-y-3.5 text-center">
                    <p class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase">
                        Total: {{ $inventories->total() }} productos registrados
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ $inventories->previousPageUrl() ?? '#' }}"
                            class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center {{ $inventories->onFirstPage() ? 'text-slate-300 pointer-events-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </a>
                        <span
                            class="w-10 h-10 rounded-xl bg-brand-navy text-white font-extrabold text-sm shadow-md shadow-brand-navy/20 flex items-center justify-center">{{ $inventories->currentPage() }}</span>
                        <a href="{{ $inventories->nextPageUrl() ?? '#' }}"
                            class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center {{ !$inventories->hasMorePages() ? 'text-slate-300 pointer-events-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div id="detail-backdrop"
        class="fixed inset-0 z-[60] bg-brand-navy/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"
        onclick="closeDetailSheet()"></div>

    <section id="detail-sheet"
        class="fixed inset-x-0 bottom-0 z-[70] mx-auto max-w-md bg-white rounded-t-[34px] shadow-brand-sheet border-t border-white/60 transform translate-y-full flex flex-col max-h-[92vh] overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="detail-title">
        <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeDetailSheet()">
            <div class="w-12 h-1.5 rounded-full bg-gray-300"></div>
        </div>

        <div class="px-5 pt-2 pb-3.5 border-b border-brand-border flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">inventory</span>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-brand-navy tracking-tight leading-tight" id="detail-title">
                        Desglose de Lotes</h3>
                    <p class="text-[11px] font-semibold text-gray-500 tracking-wide mt-0.5" id="detail-subtitle">
                        Existencias por vencimiento</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailSheet()" aria-label="Cerrar ventana"
                class="w-9 h-9 rounded-full bg-[#f3f5fa] hover:bg-gray-200 text-gray-500 hover:text-brand-navy flex items-center justify-center active:scale-90 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-5 py-4 overflow-y-auto space-y-3.5 no-scrollbar bg-[#f8f9ff]">
            <div class="bg-white rounded-2xl p-4 border border-brand-border shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-[#f0f4fc]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-accent"></span>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Producto</span>
                            <span class="font-extrabold text-xs text-brand-navy" id="detail-product-name">Detalle de
                                lotes</span>
                        </div>
                    </div>
                    <span
                        class="text-[10px] font-bold text-brand-navy bg-slate-100 px-2 py-0.5 rounded-md" id="detail-lot-count">0 lotes</span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-0.5">
                    <div class="bg-[#f5f8ff] p-2.5 rounded-xl border border-[#e1e9fa]">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Stock
                            Total</span>
                        <span class="text-xs font-extrabold text-brand-navy mt-0.5 block" id="detail-stock">0</span>
                    </div>
                    <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Inversión
                            Lotes</span>
                        <span class="text-xs font-extrabold text-brand-accent mt-0.5 block" id="detail-cost">$0.00</span>
                    </div>
                </div>
            </div>

            <div id="detail-lots" class="space-y-2"></div>
        </div>

        <div class="p-4 bg-white border-t border-brand-border shadow-inner">
            <button type="button" onclick="closeDetailSheet()"
                class="w-full py-3 rounded-2xl bg-brand-navy text-white text-xs font-bold active:scale-[0.98] transition">
                Entendido, cerrar
            </button>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        const usdRate = @json($usdRate ?? 0);
        let currentFilter = @json($activeFilter);
        let searchTimeout = null;

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('inventory-search');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value;

                    searchTimeout = setTimeout(function() {
                        if (query.length < 1) {
                            loadFilteredInventory(getFilterUrl(currentFilter));
                            return;
                        }

                        performSearch(query);
                    }, 400);
                });
            }

            document.querySelectorAll('.inventory-filter-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    const url = this.dataset.url;

                    currentFilter = filter;
                    updateFilterButtons();
                    history.replaceState({}, '', url);

                    const query = searchInput ? searchInput.value.trim() : '';

                    if (query.length > 0) {
                        performSearch(query);
                    } else {
                        loadFilteredInventory(url);
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeDetailSheet();
                }
            });
        });

        function getFilterUrl(filter) {
            const button = document.querySelector(`.inventory-filter-btn[data-filter="${filter}"]`);

            return button ? button.dataset.url : window.location.href;
        }

        function updateFilterButtons() {
            const activeClasses = ['bg-brand-navy', 'text-white', 'shadow-sm'];
            const inactiveClasses = ['bg-white', 'text-slate-600', 'border', 'border-slate-200', 'hover:bg-slate-50'];

            document.querySelectorAll('.inventory-filter-btn').forEach(function(button) {
                button.classList.remove(...activeClasses);
                button.classList.remove(...inactiveClasses);

                if (button.dataset.filter === currentFilter) {
                    button.classList.add(...activeClasses);
                } else {
                    button.classList.add(...inactiveClasses);
                }
            });
        }

        function loadFilteredInventory(url) {
            const feed = document.getElementById('inventory-feed');
            const paginationWrapper = document.getElementById('inventory-pagination-wrapper');
            const countLabel = document.getElementById('inventory-record-count');

            if (!feed) return;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newFeed = doc.getElementById('inventory-feed');
                    const newPagination = doc.getElementById('inventory-pagination-wrapper');
                    const newCount = doc.getElementById('inventory-record-count');

                    if (newFeed) {
                        feed.innerHTML = newFeed.innerHTML;
                    }

                    if (paginationWrapper) {
                        paginationWrapper.innerHTML = newPagination ? newPagination.innerHTML : '';
                        paginationWrapper.style.display = '';
                    }

                    if (countLabel && newCount) {
                        countLabel.textContent = newCount.textContent;
                    }
                })
                .catch(error => console.error(error));
        }

        function scanInventoryBarcode() {
            const input = document.getElementById('inventory-search');

            if (window.startBarcodeScanner) {
                window.startBarcodeScanner(input);
            } else {
                input.focus();
            }
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function(char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [char];
            });
        }

        function performSearch(query) {
            const feed = document.getElementById('inventory-feed');
            const paginationWrapper = document.getElementById('inventory-pagination-wrapper');
            const countLabel = document.getElementById('inventory-record-count');

            feed.innerHTML = `<div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center">
                <span class="material-symbols-outlined text-4xl text-slate-300">search</span>
                <p class="mt-2 text-xs font-bold text-brand-navy">Buscando existencias...</p>
            </div>`;

            fetch(`${window.tenantBase}/home/inventario/search/${encodeURIComponent(query)}?filter=${encodeURIComponent(currentFilter)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    feed.innerHTML = (data.inventories || []).length > 0 ?
                        data.inventories.map(renderInventoryCard).join('') :
                        `<div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-300">search_off</span>
                            <p class="mt-2 text-xs font-bold text-brand-navy">No se encontraron resultados</p>
                            <p class="text-[11px] text-slate-400 mt-1">Prueba con otro producto o código.</p>
                        </div>`;

                    if (paginationWrapper) {
                        paginationWrapper.style.display = 'none';
                    }

                    if (countLabel) {
                        countLabel.textContent = `${(data.inventories || []).length} resultados`;
                    }
                })
                .catch(error => console.error(error));
        }

        function renderInventoryCard(inventory) {
            const product = inventory.product || {};
            const isWeight = product.sale_type === 'weight';
            const unit = isWeight ? 'g' : 'uds';
            const stock = parseFloat(inventory.stock) || 0;
            const entries = parseFloat(inventory.entries) || 0;
            const outputs = parseFloat(inventory.outputs) || 0;
            const threshold = parseInt(product.low_stock_threshold, 10) || 10;
            const isCritical = stock <= 0;
            const isLow = !isCritical && stock <= threshold;
            const soldPercent = entries > 0 ? Math.min(100, Math.max(0, (outputs / entries) * 100)) : 0;
            const remainingPercent = Math.max(0, 100 - soldPercent);
            const stockValue = entries > 0 ? (parseFloat(inventory.expense) / entries) * stock : 0;
            const stockValueBs = stockValue * usdRate;
            const borderClass = isCritical ? 'border-rose-200' : (isLow ? 'border-amber-200' :
                'border-slate-200/90');
            const stockClass = isCritical ? 'bg-rose-50 text-rose-700 border-rose-200/80' : (isLow ?
                'bg-amber-50 text-amber-700 border-amber-200/80' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80');
            const statusBadge = isCritical ?
                '<span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200/70 px-2 py-0.5 rounded-md">Crítico</span>' :
                (isLow ?
                    '<span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/70 px-2 py-0.5 rounded-md flex items-center"><span class="material-symbols-outlined text-[12px] mr-0.5">warning</span>Reposición requerida</span>' :
                    '<span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Saludable</span>'
                    );

            return `<article class="bg-white rounded-2xl p-4 border ${borderClass} shadow-sm space-y-3 relative hover:border-blue-300 transition-colors cursor-pointer"
                onclick="showDetail(${inventory.id})" role="button" tabindex="0"
                onkeydown="if (event.key === 'Enter') showDetail(${inventory.id})">
                <div class="flex items-start justify-between gap-2">
                    <div class="space-y-0.5 min-w-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <h3 class="font-bold text-sm text-brand-navy truncate">${escapeHtml(product.name || 'Producto')}</h3>
                            <span class="text-[10px] bg-slate-100 text-slate-500 font-medium px-1.5 py-0.5 rounded shrink-0">${isWeight ? 'Granel' : 'Unidad'}</span>
                        </div>
                        <p class="text-[11px] font-medium text-slate-400">SKU: <span class="text-slate-600 font-mono">${escapeHtml(product.barcode || 'SIN SKU')}</span></p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold flex-shrink-0 border ${stockClass}">
                        ${(isCritical || isLow) ? `<span class="w-1.5 h-1.5 rounded-full ${isCritical ? 'bg-rose-500' : 'bg-amber-500'} mr-1.5 animate-pulse"></span>` : ''}
                        ${stock} ${unit}
                    </span>
                </div>
                <div class="space-y-1.5 pt-1">
                    <div class="flex justify-between items-center text-xs gap-2">
                        <span class="text-slate-500 text-[11px] font-medium">Relación Compras / Ventas:</span>
                        <span class="font-bold tracking-tight text-[11px]">
                            <span class="text-blue-900 font-extrabold">${entries}</span>
                            <span class="text-slate-300 font-normal">/</span>
                            <span class="text-amber-600 font-extrabold">${outputs} ${unit}</span>
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden flex">
                        <div class="${isCritical ? 'bg-rose-500' : (isLow ? 'bg-amber-500' : 'bg-brand-navy')} h-full" style="width: ${remainingPercent}%"></div>
                        <div class="${isCritical ? 'bg-rose-300' : 'bg-amber-500'} h-full" style="width: ${soldPercent}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs gap-2">
                    <div>
                        <span class="text-slate-400 text-[10px] block">Valuación en stock</span>
                        <span class="font-extrabold text-brand-navy text-xs">$${stockValue.toFixed(2)} <span class="font-medium text-slate-400">/ Bs. ${stockValueBs.toFixed(2)}</span></span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        ${statusBadge}
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </div>
            </article>`;
        }

        function showDetail(inventoryID) {
            fetch(`${window.tenantBase}/home/inventario/${inventoryID}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    buildDetailSheet(data.details || []);
                    openDetailSheet();
                })
                .catch(error => console.error(error));
        }

        function buildDetailSheet(details) {
            const product = details[0] ? details[0].product : null;
            const isWeight = product && product.sale_type === 'weight';
            const unit = isWeight ? 'g' : 'uds';
            const totalStock = details.reduce((sum, item) => sum + (parseFloat(item.stock) || 0), 0);
            const totalCost = details.reduce((sum, item) => sum + (parseFloat(item.cost) || 0), 0);

            setText('detail-product-name', product ? product.name : 'Detalle de lotes');
            setText('detail-lot-count', `${details.length} ${details.length === 1 ? 'lote' : 'lotes'}`);
            setText('detail-stock', `${totalStock} ${unit}`);
            setText('detail-cost', `$${totalCost.toFixed(2)} / Bs ${(totalCost * usdRate).toFixed(2)}`);

            const container = document.getElementById('detail-lots');

            if (details.length === 0) {
                container.innerHTML = `<div class="bg-white rounded-2xl p-8 border border-brand-border shadow-sm text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">inventory_2</span>
                    <p class="mt-2 text-xs font-bold text-brand-navy">Sin stock disponible en lotes</p>
                </div>`;
                return;
            }

            container.innerHTML = details.map(detail => {
                const lotProduct = detail.product || {};
                const lotIsWeight = lotProduct.sale_type === 'weight';
                const lotUnit = lotIsWeight ? 'g' : 'uds';
                const expiry = formatExpiry(detail.expired_date);

                return `<div class="bg-white rounded-2xl p-3.5 border border-brand-border shadow-sm flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h4 class="text-xs font-extrabold text-brand-navy leading-snug">Lote: ${escapeHtml(detail.lote_number || 'S/L')}</h4>
                            <p class="text-[10px] font-medium mt-0.5 ${expiry.className}">${expiry.label}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 text-brand-accent text-xs font-extrabold border border-emerald-100">${parseFloat(detail.stock) || 0} ${lotUnit}</span>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-50 text-[11px]">
                        <span class="text-gray-400">Inversión del lote</span>
                        <span class="font-bold text-brand-navy">$${(parseFloat(detail.cost) || 0).toFixed(2)} <span class="font-medium text-gray-400">/ Bs. ${((parseFloat(detail.cost) || 0) * usdRate).toFixed(2)}</span></span>
                    </div>
                </div>`;
            }).join('');
        }

        function formatExpiry(dateString) {
            if (!dateString) {
                return {
                    label: 'Sin vencimiento',
                    className: 'text-slate-400'
                };
            }

            const date = new Date(dateString);
            const now = new Date();
            const threeMonths = new Date();
            threeMonths.setMonth(now.getMonth() + 3);
            const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

            let className = 'text-slate-500';
            if (date < now) className = 'text-rose-600 font-bold';
            else if (date < threeMonths) className = 'text-amber-600 font-bold';

            return {
                label: `Vence: ${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`,
                className: className
            };
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        }

        function openDetailSheet() {
            const sheet = document.getElementById('detail-sheet');
            const backdrop = document.getElementById('detail-backdrop');

            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailSheet() {
            const sheet = document.getElementById('detail-sheet');
            const backdrop = document.getElementById('detail-backdrop');

            sheet.classList.remove('translate-y-0');
            sheet.classList.add('translate-y-full');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }
    </script>
@endsection
