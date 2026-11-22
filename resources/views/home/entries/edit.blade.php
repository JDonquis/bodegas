@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-md flex-col gap-4 font-jakarta">

        <section class="space-y-3">
            <div>
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Editar Compra</h1>
                <p class="text-xs text-slate-500 mt-0.5 font-medium leading-relaxed">
                    Compra #{{ $entryGeneral->id }} · Actualiza la mercancía registrada.
                </p>
            </div>
        </section>

        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-secondary-container/40 via-secondary-fixed/20 to-surface-container-lowest border border-secondary-container/60 p-3.5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-secondary-container/50 border border-secondary-container flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[22px]">trending_up</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-label font-bold tracking-wider text-on-surface-variant uppercase">Tasa
                                oficial BCV</span>
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-on-secondary text-[9px] font-extrabold uppercase tracking-tight">Hoy</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant font-medium">Actualización automática</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-lg font-black font-headline text-secondary tracking-tight leading-none">
                        {{ number_format($usdRate ?? 0, 2) }} <span class="text-xs font-bold">Bs/$</span>
                    </div>
                    <span class="text-[10px] text-secondary/80 font-semibold">Tasa del día activa</span>
                </div>
            </div>
            <input type="hidden" id="usd-rate" value="{{ $usdRate ?? 0 }}">
        </section>

        <section class="bg-white rounded-2xl p-4 border border-brand-border shadow-brand-card space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[16px]">search</span>
                    </div>
                    <h2 class="text-sm font-bold text-brand-navy">Buscador de Productos</h2>
                </div>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Añadir
                    más</span>
            </div>

            <div class="relative flex items-center">
                <span
                    class="material-symbols-outlined absolute left-3 text-indigo-500 text-[20px] pointer-events-none">barcode_scanner</span>
                <input type="text" id="html5-search-input" oninput="searchProduct()"
                    placeholder="Buscar por nombre, SKU o código..."
                    class="w-full h-11 pl-10 pr-24 bg-slate-50 border border-slate-200/80 rounded-xl text-sm font-semibold text-brand-navy placeholder-slate-400 focus:bg-white focus:border-brand-accent focus:ring-2 focus:ring-brand-accent/20 transition-all outline-none">
                <button type="button" onclick="focusSearch()"
                    class="absolute right-2 px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-indigo-600 shadow-sm flex items-center gap-1 hover:bg-slate-50 active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                    <span>Scan</span>
                </button>
            </div>

            <div id="search-results" class="space-y-2">
                <p class="p-3 text-center text-[11px] text-slate-400">Busca un producto para agregarlo a la compra.</p>
            </div>

            <div id="create-product-wrapper"
                class="hidden rounded-xl border border-amber-200 bg-amber-50/70 p-3 space-y-2">
                <p class="text-[11px] text-amber-800 font-semibold">
                    No se encontró "<span id="create-product-name"></span>"
                </p>
                <button type="button" id="btnCreateProduct" onclick="createProduct()"
                    class="w-full h-9 rounded-lg bg-brand-navy hover:bg-brand-primary text-white text-[11px] font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[16px] text-brand-accent">add</span>
                    Crear producto
                </button>
            </div>
        </section>

        <section class="bg-white rounded-2xl p-4 border border-brand-border shadow-brand-card space-y-3.5">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-mint-soft text-brand-accent-dark flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-brand-navy">Carga de Detalles</h2>
                        <p class="text-[10px] text-slate-400 font-medium">Mercancía registrada en la compra</p>
                    </div>
                </div>
                <span
                    class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-brand-accent-dark border border-brand-mint-border">
                    <span id="items-count">0</span> ítems listos
                </span>
            </div>

            <form action="{{ route('entries.update', ['entry' => $entryGeneral->id]) }}" method="POST" id="entry-form">
                @csrf
                @method('PUT')

                <div id="added-products" class="space-y-3"></div>

                <div id="empty-state" class="hidden py-12 flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-4xl text-slate-300">add_shopping_cart</span>
                    </div>
                    <p class="font-black text-[11px] uppercase tracking-widest text-slate-400">Agrega productos para
                        procesar</p>
                    <p class="text-[11px] text-slate-400 mt-1">Usa el buscador para agregar mercancía.</p>
                </div>

                <div id="total-summary"
                    class="hidden mt-4 pt-3.5 border-t border-dashed border-slate-200 bg-slate-50/60 -mx-4 -mb-4 p-4 rounded-b-2xl space-y-2">
                    <div class="flex justify-between items-center text-xs font-medium text-slate-500">
                        <span>Total Cantidad:</span>
                        <span id="total-units-preview" class="font-bold text-brand-navy">0 (0 productos)</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-medium text-slate-500">
                        <span>Subtotal Divisas:</span>
                        <span id="total-subtotal-preview" class="font-bold text-slate-700">$0.00 USD</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                        <div>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-brand-accent-dark bg-emerald-100/60 px-1.5 py-0.5 rounded">
                                Monto Total a Pagar
                            </span>
                            <div class="text-lg font-black text-brand-navy tracking-tight mt-0.5">
                                $<span id="total-cost-preview">0.00</span>
                                <span class="text-xs font-semibold text-slate-400">USD</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase text-slate-400">En Bolívares (BCV)</span>
                            <div class="text-base font-extrabold text-emerald-600 tracking-tight">
                                Bs. <span id="total-bs-preview">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <div class="flex items-center justify-center gap-1.5 text-center text-slate-400 text-[11px] font-medium pt-1">
            <span class="material-symbols-outlined text-[16px]">info</span>
            <span>Los precios se actualizan de acuerdo al dólar BCV oficial del día.</span>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ route('entries') }}"
                class="flex items-center justify-center gap-2 h-11 px-4 bg-white border border-slate-200 hover:border-slate-300 text-slate-600 font-semibold text-xs rounded-xl shadow-sm active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px] text-slate-400">close</span>
                <span>Cancelar</span>
            </a>
            <button type="submit" form="entry-form" id="btn-create-entry"
                class="flex items-center justify-center gap-2 h-11 px-4 bg-brand-navy hover:bg-brand-primary text-white font-semibold text-xs rounded-xl shadow-md shadow-brand-navy/20 active:scale-95 transition-all disabled:opacity-30 disabled:pointer-events-none">
                <span class="material-symbols-outlined text-[18px] text-brand-accent">sync</span>
                <span>Actualizar Compra</span>
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const productsAdded = [];
        const usdRate = parseFloat(document.getElementById('usd-rate').value) || 0;
        let searchProducts = [];

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

        document.addEventListener('DOMContentLoaded', function() {
            const existingEntries = @json($entries ?? []);

            existingEntries.forEach(entry => {
                const product = entry.product || {};

                productsAdded.push({
                    id: product.id,
                    name: product.name,
                    barcode: product.barcode,
                    sale_type: product.sale_type || 'unit',
                    quantity: entry.quantity,
                    cost: entry.cost,
                    cost_bs: entry.cost_bs ?? 0,
                    lote_number: entry.lote_number,
                    date: entry.expired_date ? entry.expired_date.split('T')[0] : ''
                });
            });

            refreshProducts();
        });

        function focusSearch() {
            const input = document.getElementById('html5-search-input');
            input.focus();
            const length = input.value.length;
            input.setSelectionRange(length, length);
        }

        function searchProduct() {
            const input = document.getElementById('html5-search-input');
            const term = input.value.trim();
            const resultsEl = document.getElementById('search-results');
            const createWrapper = document.getElementById('create-product-wrapper');
            const createName = document.getElementById('create-product-name');

            if (term.length < 1) {
                searchProducts = [];
                resultsEl.innerHTML =
                    '<p class="p-3 text-center text-[11px] text-slate-400">Busca un producto para agregarlo a la compra.</p>';
                createWrapper.classList.add('hidden');
                return;
            }

            fetch(`${window.tenantBase}/home/productos/search/${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    searchProducts = data.products || [];

                    if (searchProducts.length === 0) {
                        resultsEl.innerHTML =
                            '<div class="p-3 text-center text-error/70 text-[11px] font-bold uppercase tracking-widest">Sin resultados</div>';
                        createName.textContent = term;
                        createWrapper.classList.remove('hidden');
                        return;
                    }

                    createWrapper.classList.add('hidden');

                    if (searchProducts.length === 1 && searchProducts[0].barcode === term) {
                        addProductById(searchProducts[0].id);
                        input.value = '';
                        resultsEl.innerHTML =
                            '<div class="p-3 text-center text-brand-accent text-[11px] font-bold uppercase tracking-widest">¡Producto detectado!</div>';
                        return;
                    }

                    resultsEl.innerHTML = searchProducts.map(product => {
                        const added = productsAdded.some(p => p.id === product.id);
                        const name = escapeHtml(product.name);
                        const barcode = escapeHtml(product.barcode || 'SIN SKU');
                        const price = product.sale_type === 'weight' && product.price_per_kg ?
                            `$${parseFloat(product.price_per_kg).toFixed(2)}/kg` :
                            (product.sell_price ? `$${parseFloat(product.sell_price).toFixed(2)}` : '');
                        const priceBlock = price ?
                            `<span class="inline-block mt-0.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Ref: ${price}</span>` :
                            '';

                        return `<div class="p-3 bg-slate-50/90 rounded-xl border border-dashed border-slate-200 hover:border-brand-accent/60 transition-colors flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center text-brand-primary shadow-sm">
                                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-brand-navy truncate">${name}</h3>
                                    <p class="text-[10px] font-semibold text-slate-400 font-mono tracking-wide">Cód: ${barcode}</p>
                                    ${priceBlock}
                                </div>
                            </div>
                            <button type="button" ${added ? 'disabled' : ''} onclick="addProductById(${product.id})"
                                class="flex-shrink-0 w-9 h-9 rounded-xl bg-brand-accent/15 border border-brand-accent/30 text-brand-accent flex items-center justify-center active:scale-90 hover:bg-brand-accent hover:text-white transition-all disabled:opacity-30">
                                <span class="material-symbols-outlined text-[20px]">${added ? 'check' : 'add'}</span>
                            </button>
                        </div>`;
                    }).join('');
                })
                .catch(error => {
                    console.error(error);
                    resultsEl.innerHTML =
                        '<div class="p-3 text-center text-error/70 text-[11px] font-bold uppercase tracking-widest">Error al buscar</div>';
                });
        }

        function addProductById(id) {
            const product = searchProducts.find(p => p.id == id);

            if (product) {
                addProduct(product);
            }
        }

        function addProduct(product) {
            if (!productsAdded.some(p => p.id === product.id)) {
                product.quantity = 1;
                product.date = '';
                product.cost = 0;
                product.cost_bs = 0;
                product.lote_number = '';
                productsAdded.unshift(product);
            }

            refreshProducts();
        }

        function generateLote(productID) {
            const product = productsAdded.find(p => p.id == productID);

            if (!product) return;

            const now = new Date();
            const date = [
                now.getFullYear(),
                String(now.getMonth() + 1).padStart(2, '0'),
                String(now.getDate()).padStart(2, '0')
            ].join('');

            let lote = '';

            do {
                lote = `L-${date}-${Math.floor(1000 + Math.random() * 9000)}`;
            } while (productsAdded.some(p => p.lote_number === lote));

            product.lote_number = lote;

            const input = document.getElementById(`lote-${productID}`);

            if (input) {
                input.value = lote;
            }
        }

        function setExpiryOneYear(productID) {
            const product = productsAdded.find(p => p.id == productID);

            if (!product) return;

            const now = new Date();
            const nextYear = new Date(now.getFullYear() + 1, now.getMonth(), now.getDate());
            const date = [
                nextYear.getFullYear(),
                String(nextYear.getMonth() + 1).padStart(2, '0'),
                String(nextYear.getDate()).padStart(2, '0')
            ].join('-');

            product.date = date;

            const input = document.getElementById(`expiry-${productID}`);

            if (input) {
                input.value = date;
            }
        }

        function refreshProducts() {
            const createEntryBtn = document.getElementById('btn-create-entry');
            const emptyState = document.getElementById('empty-state');
            const summary = document.getElementById('total-summary');
            const hasProducts = productsAdded.length > 0;

            createEntryBtn.disabled = !hasProducts;
            emptyState.classList.toggle('hidden', hasProducts);
            emptyState.classList.toggle('flex', !hasProducts);
            summary.classList.toggle('hidden', !hasProducts);

            document.getElementById('added-products').innerHTML = productsAdded.map((product, index) =>
                renderItem(product, index)).join('');

            updateTotalCalculations();
        }

        function renderItem(product, index) {
            const isWeight = product.sale_type === 'weight';
            const cost = parseFloat(product.cost) || 0;
            const costBs = parseFloat(product.cost_bs) || 0;
            const quantity = parseFloat(product.quantity) || 1;
            const unitUsd = quantity > 0 ? cost / quantity : 0;
            const unitBs = quantity > 0 ? costBs / quantity : 0;

            return `<div data-product-row="${product.id}" class="p-3 bg-slate-50/70 border border-slate-200/80 rounded-xl space-y-2.5 transition-all hover:border-slate-300">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-brand-navy truncate">${escapeHtml(product.name)}</h4>
                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">Cód: ${escapeHtml(product.barcode || 'SIN SKU')}</p>
                    </div>
                    <button type="button" onclick="cancelProduct(${product.id})" class="text-slate-300 hover:text-rose-500 p-1 transition-colors" title="Eliminar ítem">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>

                <input type="hidden" name="products[${index}][productID]" value="${product.id}">

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Lote *</label>
                            <button type="button" onclick="generateLote(${product.id})"
                                class="text-[9px] font-bold text-brand-accent-dark hover:text-brand-accent flex items-center gap-0.5 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[12px]">autorenew</span>
                                Auto
                            </button>
                        </div>
                        <input type="text" required id="lote-${product.id}" name="products[${index}][lote_number]"
                            value="${escapeHtml(product.lote_number || '')}" placeholder="LOTE-000"
                            oninput="refreshData(${product.id}, 'lote_number', this)"
                            class="w-full h-9 px-2.5 bg-white border border-slate-200 rounded-lg text-[11px] font-mono text-brand-navy placeholder:text-slate-300 focus:border-brand-accent focus:ring-2 focus:ring-brand-accent/20 transition-all">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Vence</label>
                            <button type="button" onclick="setExpiryOneYear(${product.id})"
                                class="text-[9px] font-bold text-brand-accent-dark hover:text-brand-accent flex items-center gap-0.5 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[12px]">event_available</span>
                                1 año
                            </button>
                        </div>
                        <input type="date" id="expiry-${product.id}" name="products[${index}][expiredDate]" value="${escapeHtml(product.date || '')}"
                            oninput="refreshData(${product.id}, 'date', this)"
                            class="w-full h-9 px-2.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-brand-navy uppercase focus:border-brand-accent focus:ring-2 focus:ring-brand-accent/20 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Costo total ($)</label>
                        <input type="number" step="0.01" min="0" required data-cost-usd
                            name="products[${index}][cost]" value="${product.cost}"
                            oninput="refreshData(${product.id}, 'cost', this)"
                            class="w-full h-9 px-2.5 bg-white border border-slate-200 rounded-lg text-[11px] font-bold text-brand-navy focus:border-brand-accent focus:ring-2 focus:ring-brand-accent/20 transition-all">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Costo total (Bs)</label>
                        <input type="number" step="0.01" min="0" data-cost-bs
                            name="products[${index}][cost_bs]" value="${product.cost_bs}"
                            oninput="refreshData(${product.id}, 'cost_bs', this)"
                            class="w-full h-9 px-2.5 bg-white border border-slate-200 rounded-lg text-[11px] font-bold text-brand-primary/70 focus:border-brand-accent focus:ring-2 focus:ring-brand-accent/20 transition-all">
                    </div>
                </div>

                <div class="flex justify-between text-[9px] text-slate-400 px-1">
                    <span>${isWeight ? 'Costo por gramo' : 'Costo unitario'}</span>
                    <span>
                        <span id="unit-usd-${product.id}">$${unitUsd.toFixed(2)}</span>
                        ·
                        <span id="unit-bs-${product.id}">${unitBs.toFixed(2)} Bs</span>
                    </span>
                </div>

                <div class="grid grid-cols-12 gap-2 items-end pt-1 border-t border-slate-200/60">
                    <div class="col-span-6">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Cantidad (${isWeight ? 'g' : 'uds'})</label>
                        <div class="flex items-center bg-white border border-slate-200 rounded-lg p-0.5">
                            <button type="button" onclick="stepQuantity(${product.id}, -1)"
                                class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded text-xs font-bold active:scale-90">−</button>
                            <input id="qty-${product.id}" type="number" min="1" required
                                name="products[${index}][quantity]" value="${product.quantity}"
                                oninput="refreshData(${product.id}, 'quantity', this)"
                                class="flex-1 min-w-0 bg-transparent border-0 p-0 text-center text-xs font-extrabold text-brand-navy focus:ring-0">
                            <button type="button" onclick="stepQuantity(${product.id}, 1)"
                                class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded text-xs font-bold active:scale-90">+</button>
                        </div>
                    </div>
                    <div class="col-span-6 text-right bg-emerald-50/60 py-1.5 px-2 rounded-lg border border-emerald-100/70">
                        <span class="text-[9px] font-bold text-emerald-800 uppercase tracking-wider block">Subtotal</span>
                        <div id="subtotal-usd-${product.id}" class="text-xs font-extrabold text-emerald-700">$${cost.toFixed(2)}</div>
                        <div id="subtotal-bs-${product.id}" class="text-[9px] font-medium text-emerald-600">${costBs.toFixed(2)} Bs</div>
                    </div>
                </div>
            </div>`;
        }

        function refreshData(productID, type, element) {
            const product = productsAdded.find(p => p.id == productID);

            if (!product) return;

            if (type === 'cost') {
                product.cost = parseFloat(element.value) || 0;

                if (usdRate > 0) {
                    product.cost_bs = (product.cost * usdRate).toFixed(2);
                    const row = element.closest('[data-product-row]');
                    const bsInput = row ? row.querySelector('[data-cost-bs]') : null;

                    if (bsInput) bsInput.value = product.cost_bs;
                }
            } else if (type === 'cost_bs') {
                product.cost_bs = parseFloat(element.value) || 0;

                if (usdRate > 0) {
                    product.cost = (product.cost_bs / usdRate).toFixed(2);
                    const row = element.closest('[data-product-row]');
                    const usdInput = row ? row.querySelector('[data-cost-usd]') : null;

                    if (usdInput) usdInput.value = product.cost;
                }
            } else {
                product[type === 'date' ? 'date' : type] = element.value;
            }

            updateRowDisplay(product);
            updateTotalCalculations();
        }

        function stepQuantity(productID, delta) {
            const product = productsAdded.find(p => p.id == productID);

            if (!product) return;

            const input = document.getElementById(`qty-${productID}`);
            const current = parseInt(input ? input.value : product.quantity, 10) || 1;
            const next = Math.max(1, current + delta);

            if (input) input.value = next;

            product.quantity = next;
            updateRowDisplay(product);
            updateTotalCalculations();
        }

        function updateRowDisplay(product) {
            const quantity = parseFloat(product.quantity) || 0;
            const cost = parseFloat(product.cost) || 0;
            const costBs = parseFloat(product.cost_bs) || 0;
            const unitUsd = quantity > 0 ? cost / quantity : 0;
            const unitBs = quantity > 0 ? costBs / quantity : 0;

            setText(`unit-usd-${product.id}`, `$${unitUsd.toFixed(2)}`);
            setText(`unit-bs-${product.id}`, `${unitBs.toFixed(2)} Bs`);
            setText(`subtotal-usd-${product.id}`, `$${cost.toFixed(2)}`);
            setText(`subtotal-bs-${product.id}`, `${costBs.toFixed(2)} Bs`);
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        }

        function updateTotalCalculations() {
            const totalUnits = productsAdded
                .filter(p => p.sale_type !== 'weight')
                .reduce((sum, p) => sum + (parseFloat(p.quantity) || 0), 0);
            const totalWeight = productsAdded
                .filter(p => p.sale_type === 'weight')
                .reduce((sum, p) => sum + (parseFloat(p.quantity) || 0), 0);
            const totalUsd = productsAdded.reduce((sum, p) => sum + (parseFloat(p.cost) || 0), 0);
            let totalBs = productsAdded.reduce((sum, p) => sum + (parseFloat(p.cost_bs) || 0), 0);

            if (totalBs === 0 && usdRate > 0) {
                totalBs = totalUsd * usdRate;
            }

            const quantityParts = [];

            if (totalUnits > 0) {
                quantityParts.push(`${totalUnits} uds`);
            }

            if (totalWeight > 0) {
                quantityParts.push(`${totalWeight} g`);
            }

            setText('items-count', productsAdded.length);
            setText('total-units-preview', `${quantityParts.join(' · ') || '0'} (${productsAdded.length} productos)`);
            setText('total-subtotal-preview', `$${totalUsd.toFixed(2)} USD`);
            setText('total-cost-preview', totalUsd.toFixed(2));
            setText('total-bs-preview', totalBs.toFixed(2));
        }

        function cancelProduct(productID) {
            const index = productsAdded.findIndex(p => p.id == productID);

            if (index !== -1) {
                productsAdded.splice(index, 1);
            }

            refreshProducts();
        }

        function createProduct() {
            const input = document.getElementById('html5-search-input');
            const name = input.value.trim();

            if (!name) return;

            if (!confirm('¿Deseas crear un nuevo producto con este nombre?')) return;

            fetch("{{ route('products.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _token: "{{ csrf_token() }}",
                        productName: name,
                        sellPrice: 0,
                        sellPriceBs: 0,
                        saleType: 'unit',
                        lowStockThreshold: {{ \App\Models\Product::DEFAULT_LOW_STOCK_THRESHOLD }}
                    })
                })
                .then(async response => {
                    if (!response.ok) {
                        const error = await response.json().catch(() => ({}));
                        throw new Error(error.message || 'No se pudo crear el producto.');
                    }

                    return response.json();
                })
                .then(data => {
                    if (data.product) {
                        searchProducts = [data.product];
                        addProduct(data.product);
                        input.value = '';
                        searchProduct();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert(error.message);
                });
        }
    </script>
@endsection
