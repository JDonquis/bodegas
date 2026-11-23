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
    </style>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-4">

        <!-- Cabecera -->
        <section class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                    <span class="text-[11px] font-label font-semibold uppercase tracking-wider text-secondary">Bodega
                        Central • Activa</span>
                </div>
                <h1 class="text-3xl font-extrabold font-headline tracking-tight text-on-surface">Productos</h1>
                <p class="text-sm text-on-surface-variant mt-0.5">Catálogo maestro de artículos en inventario</p>
            </div>
            <a href="{{ route('products.export') }}"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-primary bg-primary-fixed/50 border border-primary-fixed-dim/60 hover:bg-primary-fixed transition-colors">
                <span class="material-symbols-outlined text-[16px]">download</span>
                Exportar
            </a>
        </section>

        <!-- CTA nuevo producto -->
        <a href="{{ route('products.create') }}"
            class="w-full h-12 bg-primary active:bg-primary-container text-on-primary rounded-xl font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-primary/15 active:scale-[0.99] transition-all">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Nuevo Producto</span>
        </a>

        <!-- KPIs -->
        <div class="grid grid-cols-3 gap-2.5">
            <div class="bg-surface-container-lowest p-3 rounded-2xl border border-outline-variant/20 shadow-sm">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-label font-bold uppercase tracking-wider text-on-surface-variant">Total</span>
                    <div class="p-1 rounded bg-primary-fixed/60 text-primary">
                        <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                    </div>
                </div>
                <div class="text-xl font-extrabold font-headline text-on-surface mt-1">{{ number_format($totalProducts) }}</div>
                <p class="text-[10px] text-on-surface-variant font-medium mt-0.5">Artículos reg.</p>
            </div>

            <div class="bg-surface-container-lowest p-3 rounded-2xl border border-outline-variant/20 shadow-sm">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-label font-bold uppercase tracking-wider text-secondary">Activos</span>
                    <div class="p-1 rounded bg-secondary-container/40 text-secondary">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                    </div>
                </div>
                <div class="text-xl font-extrabold font-headline text-secondary mt-1">{{ number_format($activeProducts) }}</div>
                <p class="text-[10px] text-secondary/80 font-medium mt-0.5">
                    {{ $totalProducts > 0 ? round(($activeProducts / $totalProducts) * 100) : 0 }}% con stock
                </p>
            </div>

            <div class="bg-error-container/10 p-3 rounded-2xl border border-error-container/50 shadow-sm">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-label font-bold uppercase tracking-wider text-error">Alerta</span>
                    <div class="p-1 rounded bg-error-container/50 text-error">
                        <span class="material-symbols-outlined text-[14px]">warning</span>
                    </div>
                </div>
                <div class="text-xl font-extrabold font-headline text-error mt-1">{{ number_format($lowStockProducts) }}</div>
                <p class="text-[10px] text-error/80 font-medium mt-0.5">Bajo stock</p>
            </div>
        </div>

        <!-- Búsqueda y filtros -->
        <section
            class="sticky top-[calc(4rem+env(safe-area-inset-top))] z-20 -mx-4 space-y-3 bg-surface/95 px-4 pb-2 pt-2 backdrop-blur-md lg:top-20 lg:mx-0 lg:px-0">
            <form action="{{ route('products') }}" method="GET" id="search-form" class="relative flex items-center">
                @if (request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <span
                    class="material-symbols-outlined absolute left-3.5 text-on-surface-variant text-[18px] pointer-events-none">search</span>
                <input type="search" name="search" id="html5-search-input" value="{{ request('search') }}"
                    placeholder="Buscar por nombre o código..."
                    class="w-full h-11 pl-10 pr-24 bg-surface-container-lowest border border-outline-variant/30 rounded-xl text-xs text-on-surface placeholder:text-on-surface-variant/60 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary shadow-sm transition-all">
                <div class="absolute inset-y-0 right-1.5 flex items-center">
                    <button type="button" id="scan-button" title="Escanear código de barras"
                        class="h-8 px-2 bg-primary-fixed/50 hover:bg-primary-fixed text-primary border border-primary-fixed-dim/60 rounded-lg flex items-center gap-1 text-[11px] font-semibold active:scale-95 transition-transform">
                        <span class="material-symbols-outlined text-[16px]">barcode_scanner</span>
                        <span>Scan</span>
                    </button>
                </div>
            </form>

            <div id="product-filters" class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5 text-xs font-semibold">
                @php
                    $currentType = request('type');
                    $chips = [
                        ['value' => null, 'label' => 'Todos', 'count' => $totalProducts],
                        ['value' => 'unit', 'label' => 'Por unidad', 'count' => $unitProducts],
                        ['value' => 'weight', 'label' => 'Por peso', 'count' => $weightProducts],
                        ['value' => 'low', 'label' => 'Bajo stock', 'count' => $lowStockProducts],
                    ];
                @endphp

                @foreach ($chips as $chip)
                    @php
                        $isActive = $currentType === $chip['value'];
                        $isDanger = $chip['value'] === 'low';

                        $activeClasses = $isDanger
                            ? 'bg-error text-on-error border-error shadow-sm'
                            : 'bg-primary text-on-primary border-primary shadow-sm';
                        $inactiveClasses = $isDanger
                            ? 'bg-error-container/30 text-on-error-container border-error-container hover:bg-error-container/50'
                            : 'bg-surface-container-lowest text-on-surface-variant border-outline-variant/30 hover:bg-surface-container';
                    @endphp
                    <a href="{{ route('products', array_filter(['search' => request('search'), 'type' => $chip['value']])) }}"
                        class="shrink-0 inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full border whitespace-nowrap transition-colors {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        @if ($isDanger)
                            <span class="w-1.5 h-1.5 rounded-full bg-error {{ $isActive ? '' : 'animate-pulse' }}"></span>
                        @endif
                        {{ $chip['label'] }} ({{ $chip['count'] }})
                    </a>
                @endforeach
            </div>
        </section>

        <div id="products-results" class="flex flex-col gap-4">
        <!-- Lista de productos -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
            @forelse ($products as $product)
                @php
                    $stock = (int) ($product->inventoryGeneral->stock ?? 0);
                    $unit = $product->unit_label;
                    $isWeight = $product->sale_type === 'weight';
                    $isOut = $stock <= 0;
                    $threshold = (int) ($product->low_stock_threshold ?? \App\Models\Product::DEFAULT_LOW_STOCK_THRESHOLD);
                    $isLow = $stock <= $threshold;
                @endphp

                <article
                    class="bg-surface-container-lowest rounded-2xl p-3.5 shadow-sm border flex flex-col gap-3 transition-colors {{ $isOut ? 'border-error-container' : ($isLow ? 'border-amber-200/80' : 'border-outline-variant/20') }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div
                                class="w-11 h-11 rounded-xl border flex items-center justify-center shrink-0 shadow-sm {{ $isWeight ? 'bg-secondary-fixed/30 text-on-secondary-container border-secondary-fixed/60' : 'bg-primary-fixed/50 text-primary border-primary-fixed-dim/50' }}">
                                <span class="material-symbols-outlined text-[22px]">{{ $isWeight ? 'scale' : 'inventory_2' }}</span>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-sm font-bold font-headline text-on-surface leading-snug">{{ $product->name }}</h2>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span
                                        class="text-[10px] font-label font-semibold px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant uppercase">{{ $isWeight ? 'Por peso' : 'Por unidad' }}</span>
                                    @if ($product->isMarginPricing())
                                        <span
                                            class="text-[10px] font-label font-semibold px-2 py-0.5 rounded-md bg-secondary-container/40 text-on-secondary-container uppercase">Margen {{ number_format((float) $product->profit_margin, 2) }}%</span>
                                    @endif
                                    <span class="font-mono text-[11px] text-on-surface-variant">{{ $product->barcode ?? 'Sin código' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-on-surface-variant block font-label">Precio{{ $isWeight ? ' / kg' : '' }}</span>
                            <span class="text-sm font-extrabold text-on-surface">{{ number_format((float) $product->display_price, 2) }}$</span>
                            <span class="text-[10px] text-on-surface-variant block">
                                {{ $product->sell_price_bs ? number_format((float) $product->sell_price_bs, 2) . ' Bs' : '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-outline-variant/20 flex items-center justify-between gap-2">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $isOut ? 'bg-error-container/50 text-on-error-container border-error-container' : ($isLow ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-secondary-container/30 text-on-secondary-container border-secondary-container') }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $isOut ? 'bg-error' : ($isLow ? 'bg-amber-500' : 'bg-secondary') }}"></span>
                            @if ($isOut)
                                Agotado
                            @else
                                Stock: {{ number_format($stock) }} {{ $unit }}{{ $isLow ? ' (Bajo)' : '' }}
                            @endif
                        </span>

                        <div class="flex items-center gap-1">
                            <a href="{{ route('entries.create', ['product' => $product->id]) }}"
                                title="Registrar compra de este producto"
                                class="p-1.5 text-secondary hover:text-on-secondary-container hover:bg-secondary-container/40 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                            </a>
                            <a href="{{ route('products.edit', ['product' => $product->id]) }}" title="Editar producto"
                                class="p-1.5 text-primary hover:text-primary-container hover:bg-primary-fixed/40 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <button type="button"
                                onclick="deleteProduct('{{ route('products.delete', ['product' => $product->id]) }}')"
                                title="Eliminar producto"
                                class="p-1.5 text-error hover:text-on-error-container hover:bg-error-container/40 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div
                    class="lg:col-span-2 rounded-2xl bg-surface-container-lowest border border-outline-variant/20 p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-outline">inventory_2</span>
                    <p class="mt-2 text-sm font-semibold text-on-surface">No se encontraron productos</p>
                    <p class="text-xs text-on-surface-variant mt-1">Ajusta la búsqueda o los filtros.</p>
                </div>
            @endforelse
        </section>

        <!-- Paginación -->
        @if ($products->hasPages())
            <div id="products-pagination" class="flex flex-col items-center gap-3 pt-1">
                <p class="text-center text-xs text-on-surface-variant font-medium">
                    Mostrando <span class="font-bold text-on-surface">{{ $products->firstItem() }} a
                        {{ $products->lastItem() }}</span> de <span
                        class="font-bold text-on-surface">{{ $products->total() }}</span> productos
                </p>
                <div class="flex items-center gap-1.5">
                    <a href="{{ $products->url(1) }}"
                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors {{ $products->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                        <span class="material-symbols-outlined text-[16px]">keyboard_double_arrow_left</span>
                    </a>
                    <a href="{{ $products->previousPageUrl() ?? '#' }}"
                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors {{ $products->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    </a>

                    @foreach ($products->getUrlRange(max(1, $products->currentPage() - 1), min($products->lastPage(), $products->currentPage() + 1)) as $page => $url)
                        <a href="{{ $url }}"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-semibold border transition-colors {{ $page == $products->currentPage() ? 'bg-primary text-on-primary border-primary shadow-sm' : 'bg-surface-container-lowest text-on-surface-variant border-outline-variant/30 hover:bg-surface-container' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    <a href="{{ $products->nextPageUrl() ?? '#' }}"
                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-surface-container-lowest text-primary border border-outline-variant/30 hover:bg-surface-container transition-colors {{ !$products->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </a>
                    <a href="{{ $products->url($products->lastPage()) }}"
                        class="w-8 h-8 rounded-xl flex items-center justify-center bg-surface-container-lowest text-primary border border-outline-variant/30 hover:bg-surface-container transition-colors {{ !$products->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                        <span class="material-symbols-outlined text-[16px]">keyboard_double_arrow_right</span>
                    </a>
                </div>
            </div>
        @endif
        </div>
    </div>

    <form action="" id="actions-form-delete" class="hidden" method="POST">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('html5-search-input');
            const searchForm = document.getElementById('search-form');
            const scanButton = document.getElementById('scan-button');
            let results = document.getElementById('products-results');
            let timeout = null;
            let controller = null;
            let requestCounter = 0;

            function buildUrl() {
                const url = new URL(window.location.href);
                const value = searchInput.value.trim();

                if (value) {
                    url.searchParams.set('search', value);
                } else {
                    url.searchParams.delete('search');
                }

                url.searchParams.delete('page');

                return url;
            }

            function syncFilterLinks() {
                const filters = document.getElementById('product-filters');

                if (!filters) return;

                const value = searchInput.value.trim();

                filters.querySelectorAll('a[href]').forEach(function(link) {
                    const url = new URL(link.href, window.location.origin);

                    if (value) {
                        url.searchParams.set('search', value);
                    } else {
                        url.searchParams.delete('search');
                    }

                    url.searchParams.delete('page');
                    link.href = url.toString();
                });
            }

            function renderProductsSkeleton() {
                if (!results) return;

                const currentCards = results.querySelectorAll('article, [data-skeleton-card]').length;
                const skeletonCount = currentCards || 1;
                const hadPagination = Boolean(results.querySelector('#products-pagination'));

                results.innerHTML = `<section class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
                    ${Array.from({ length: skeletonCount }).map(function() {
                        return `<div data-skeleton-card class="bg-surface-container-lowest rounded-2xl p-3.5 border border-outline-variant/20 flex flex-col gap-3">
                            <div class="flex min-h-[50px] items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                    <div class="skeleton w-11 h-11 rounded-xl shrink-0"></div>
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="skeleton h-3.5 w-2/3 rounded"></div>
                                        <div class="skeleton h-2.5 w-1/2 rounded"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 space-y-1.5">
                                    <div class="skeleton h-3 w-14 rounded ml-auto"></div>
                                    <div class="skeleton h-2.5 w-10 rounded ml-auto"></div>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-outline-variant/20 flex items-center justify-between gap-2">
                                <div class="skeleton h-5 w-24 rounded-full"></div>
                                <div class="flex items-center gap-1">
                                    <div class="skeleton w-[30px] h-[30px] rounded-lg"></div>
                                    <div class="skeleton w-[30px] h-[30px] rounded-lg"></div>
                                    <div class="skeleton w-[30px] h-[30px] rounded-lg"></div>
                                </div>
                            </div>
                        </div>`;
                    }).join('')}
                </section>
                ${hadPagination ? '<div class="skeleton h-14 w-full rounded-2xl"></div>' : ''}`;
            }

            function loadResults(url) {
                if (!results) return;

                const requestId = ++requestCounter;

                if (controller) controller.abort();
                controller = new AbortController();

                const currentHeight = results.offsetHeight;
                renderProductsSkeleton();

                if (currentHeight > 0) {
                    results.style.minHeight = currentHeight + 'px';
                }

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: controller.signal
                    })
                    .then(function(response) {
                        if (!response.ok) throw new Error('Error al buscar productos');
                        return response.text();
                    })
                    .then(function(html) {
                        if (requestId !== requestCounter) return;

                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const newResults = doc.getElementById('products-results');
                        const currentFilters = document.getElementById('product-filters');
                        const newFilters = doc.getElementById('product-filters');

                        if (newResults && results) {
                            results.replaceWith(newResults);
                            results = newResults;
                        }

                        if (currentFilters && newFilters) {
                            currentFilters.replaceWith(newFilters);
                        }
                    })
                    .catch(function(error) {
                        if (error.name === 'AbortError') return;
                        window.location.href = url.toString();
                    });
            }

            function runSearch() {
                const url = buildUrl();
                history.replaceState({}, '', url);
                syncFilterLinks();
                loadResults(url);
            }

            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(runSearch, 400);
                });

                searchForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    clearTimeout(timeout);
                    runSearch();
                });
            }

            document.addEventListener('click', function(event) {
                const filterLink = event.target.closest('#product-filters a[href]');

                if (filterLink) {
                    event.preventDefault();

                    const url = new URL(filterLink.href, window.location.origin);
                    const value = searchInput.value.trim();

                    if (value) {
                        url.searchParams.set('search', value);
                    } else {
                        url.searchParams.delete('search');
                    }

                    url.searchParams.delete('page');
                    history.replaceState({}, '', url);
                    loadResults(url);

                    return;
                }

                const link = event.target.closest('#products-pagination a[href]');

                if (!link) return;

                event.preventDefault();

                const url = new URL(link.href, window.location.origin);
                history.replaceState({}, '', url);
                syncFilterLinks();
                loadResults(url);
            });

            if (scanButton && searchInput) {
                scanButton.addEventListener('click', function() {
                    if (window.startBarcodeScanner) {
                        window.startBarcodeScanner(searchInput);
                    } else {
                        searchInput.focus();
                    }
                });
            }
        });

        function deleteProduct(url) {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                const form = document.getElementById('actions-form-delete');
                form.action = url;
                form.submit();
            }
        }
    </script>
@endsection
