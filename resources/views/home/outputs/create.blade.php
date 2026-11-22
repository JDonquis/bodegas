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
                <h1 class="text-2xl font-extrabold tracking-tight text-brand-navy">Nueva Venta de Mercancía</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Registra ventas o egresos de productos con control de inventario en tiempo real.
                </p>
            </div>

            <div
                class="flex items-center justify-between bg-white border border-emerald-100 rounded-xl px-3.5 py-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-semibold text-slate-600">Tasa BCV Oficial:</span>
                    <span class="text-xs font-bold text-brand-navy">{{ number_format($usdRate ?? 0, 2) }} Bs / USD</span>
                </div>
                <span
                    class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">En
                    Vivo</span>
            </div>

        </section>

        <section class="bg-white rounded-2xl border border-slate-100 shadow-brand-card p-4 space-y-3.5">
            <div class="flex items-center gap-2.5 border-b border-slate-50 pb-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                </div>
                <h2 class="text-sm font-bold text-brand-navy">Identificación del Cliente</h2>
            </div>

            <div class="grid grid-cols-2 p-1 bg-slate-100/80 rounded-xl text-xs font-semibold">
                <button type="button" onclick="toggleClientType('registered')" id="tab-registered"
                    class="py-1.5 rounded-lg bg-white text-brand-navy shadow-sm text-center transition">
                    REGISTRADO
                </button>
                <button type="button" onclick="toggleClientType('casual')" id="tab-casual"
                    class="py-1.5 rounded-lg text-slate-500 hover:text-slate-700 text-center transition">
                    NO REGISTRADO
                </button>
            </div>

            <div id="section-registered" class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Seleccionar Cliente</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <select id="client_id_select"
                        class="w-full pl-10 pr-9 py-2.5 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-brand-navy focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition appearance-none">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" data-debt="{{ $client->debt ?? 0 }}">
                                {{ $client->name }} ({{ $client->ci }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </div>
                </div>
            </div>

            <div id="section-casual" class="hidden space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nombre del Cliente <span class="normal-case font-normal text-slate-400">(Opcional)</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">person_edit</span>
                    </div>
                    <input type="text" id="client_name_input" placeholder="Nombre del cliente (opcional)"
                        class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
                </div>
            </div>

            <div id="client-info"
                class="hidden items-center justify-between text-[11px] bg-slate-50/75 rounded-lg px-2.5 py-1.5 border border-slate-100 text-slate-600">
                <span>Deuda actual: <strong id="client-debt" class="text-rose-600 font-bold">$0.00</strong></span>
                <span class="inline-flex items-center gap-1 font-medium text-slate-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Cliente registrado
                </span>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-slate-100 shadow-brand-card p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-50 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[16px]">search</span>
                    </div>
                    <h2 class="text-sm font-bold text-brand-navy">Buscador de Stock</h2>
                </div>
                <span class="text-[10px] font-semibold text-slate-400">Stock disponible</span>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">barcode_scanner</span>
                    </div>
                    <input
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition"
                        placeholder="Escanea o busca producto..." type="text" oninput="searchProduct()"
                        id="html5-search-input">
                </div>
                <button type="button" onclick="focusSearch()" aria-label="Escanear Código de Barras"
                    class="h-9 px-3 rounded-xl bg-slate-900 text-white flex items-center gap-1.5 text-xs font-semibold active:scale-95 shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-brand-accent">photo_camera</span>
                    Scan
                </button>
            </div>

            <div id="search-results" class="space-y-2">
                <p class="p-3 text-center text-[11px] text-slate-400">Busca productos en stock...</p>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-slate-100 shadow-brand-card p-4 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                    </div>
                    <h2 class="text-sm font-bold text-brand-navy">Carrito de Venta</h2>
                </div>
                <span id="cart-badge"
                    class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">0
                    ítems</span>
            </div>

            <form action="{{ route('outputs.store') }}" method="POST" id="output-form">
                @csrf
                <input type="hidden" name="client_id" id="final_client_id">
                <input type="hidden" name="client_name" id="final_client_name">
                <input type="hidden" name="total_sold" id="final_total_sold">
                <input type="hidden" name="payment_method" id="final_payment_method">

                <div id="added-products" class="space-y-3"></div>

                <div id="empty-state" class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-4xl text-slate-300">production_quantity_limits</span>
                    </div>
                    <p class="font-black text-[11px] uppercase tracking-widest text-slate-400">Selecciona productos del
                        inventario</p>
                    <p class="text-[11px] text-slate-400 mt-1">Usa el buscador para agregar mercancía.</p>
                </div>

                <div id="total-summary"
                    class="hidden mt-4 pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Subtotal en Divisas:</span>
                        <span id="total-sold-subtotal" class="text-brand-navy font-semibold">$0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Subtotal en Bolívares:</span>
                        <span id="total-sold-subtotal-bs" class="text-brand-navy font-semibold">Bs. 0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Utilidad estimada:</span>
                        <span class="text-right">
                            <span id="total-profit-preview" class="text-emerald-600 font-semibold">$0.00</span>
                            <span id="total-profit-bs-preview" class="block text-[10px] font-normal text-slate-400">Bs. 0.00</span>
                        </span>
                    </div>
                    <div
                        class="bg-gradient-to-r from-slate-900 to-brand-navy rounded-xl p-3 text-white flex items-center justify-between shadow-md">
                        <div>
                            <div class="text-[10px] font-bold text-brand-accent uppercase tracking-widest">TOTAL A COBRAR
                            </div>
                            <div class="text-xs text-slate-300 font-mono mt-0.5">Bs. <span
                                    id="total-sold-bs">0.00</span></div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black text-brand-accent tracking-tight">$<span
                                    id="total-sold-preview">0.00</span></span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Método de
                            Pago</label>
                        <div class="grid grid-cols-2 gap-2 text-center">
                            <button type="button" data-payment="Pago móvil" onclick="selectPaymentMethod(this)"
                                class="payment-method-btn flex flex-col items-center gap-1 py-2 px-1 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold text-[11px] transition">
                                <span class="material-symbols-outlined text-[18px] text-slate-500">smartphone</span>
                                Pago móvil
                            </button>
                            <button type="button" data-payment="Punto de venta" onclick="selectPaymentMethod(this)"
                                class="payment-method-btn flex flex-col items-center gap-1 py-2 px-1 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold text-[11px] transition">
                                <span class="material-symbols-outlined text-[18px] text-slate-500">point_of_sale</span>
                                Punto de venta
                            </button>
                            <button type="button" data-payment="Efectivo Bs" onclick="selectPaymentMethod(this)"
                                class="payment-method-btn flex flex-col items-center gap-1 py-2 px-1 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold text-[11px] transition">
                                <span class="material-symbols-outlined text-[18px] text-slate-500">payments</span>
                                Efectivo Bs
                            </button>
                            <button type="button" data-payment="Efectivo Dolares" onclick="selectPaymentMethod(this)"
                                class="payment-method-btn flex flex-col items-center gap-1 py-2 px-1 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold text-[11px] transition">
                                <span class="material-symbols-outlined text-[18px] text-slate-500">attach_money</span>
                                Efectivo Dolares
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ route('outputs') }}"
                class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl border border-slate-200 bg-white font-semibold text-xs text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-slate-400">close</span>
                Cancelar
            </a>
            <button type="submit" form="output-form" id="btn-create-output" disabled
                class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-brand-navy text-white font-bold text-xs shadow-md shadow-brand-navy/20 active:scale-[0.98] transition disabled:opacity-30 disabled:pointer-events-none">
                <span class="material-symbols-outlined text-[18px] text-brand-accent">shopping_bag</span>
                Confirmar Venta
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const productsAdded = [];
        const usdRate = parseFloat("{{ $usdRate ?? 0 }}") || 0;
        let currentClientType = 'registered';
        let searchInventories = [];

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
            document.getElementById('client_id_select').addEventListener('change', syncClientData);
            document.getElementById('client_name_input').addEventListener('input', syncClientData);
            syncClientData();
        });

        function toggleClientType(type) {
            currentClientType = type;
            const regTab = document.getElementById('tab-registered');
            const casTab = document.getElementById('tab-casual');
            const regSec = document.getElementById('section-registered');
            const casSec = document.getElementById('section-casual');

            if (type === 'registered') {
                regTab.className = 'py-1.5 rounded-lg bg-white text-brand-navy shadow-sm text-center transition';
                casTab.className =
                    'py-1.5 rounded-lg text-slate-500 hover:text-slate-700 text-center transition';
                regSec.classList.remove('hidden');
                casSec.classList.add('hidden');
                document.getElementById('client_name_input').value = '';
            } else {
                casTab.className = 'py-1.5 rounded-lg bg-white text-brand-navy shadow-sm text-center transition';
                regTab.className =
                    'py-1.5 rounded-lg text-slate-500 hover:text-slate-700 text-center transition';
                casSec.classList.remove('hidden');
                regSec.classList.add('hidden');
                document.getElementById('client_id_select').value = '';
            }

            syncClientData();
        }

        function syncClientData() {
            const select = document.getElementById('client_id_select');
            const nameInput = document.getElementById('client_name_input');
            const info = document.getElementById('client-info');

            document.getElementById('final_client_id').value = currentClientType === 'registered' ? select.value :
                '';
            document.getElementById('final_client_name').value = currentClientType === 'casual' ? nameInput.value :
                '';

            if (currentClientType === 'registered' && select.value) {
                const option = select.options[select.selectedIndex];
                document.getElementById('client-debt').textContent = '$' + (parseFloat(option.dataset.debt) || 0)
                    .toFixed(2);
                info.classList.remove('hidden');
                info.classList.add('flex');
            } else {
                info.classList.add('hidden');
                info.classList.remove('flex');
            }
        }

        function focusSearch() {
            const input = document.getElementById('html5-search-input');

            if (window.startBarcodeScanner) {
                window.startBarcodeScanner(input);
            } else {
                input.focus();
            }
        }

        function searchProduct() {
            const input = document.getElementById('html5-search-input');
            const term = input.value.trim();
            const resultsEl = document.getElementById('search-results');

            if (term.length < 1) {
                searchInventories = [];
                resultsEl.innerHTML =
                    '<p class="p-3 text-center text-[11px] text-slate-400">Busca productos en stock...</p>';
                return;
            }

            fetch(`${window.tenantBase}/home/inventario/search-lots/${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    searchInventories = data.inventories || [];

                    if (searchInventories.length === 0) {
                        resultsEl.innerHTML =
                            '<div class="p-3 text-center text-error/70 text-[11px] font-bold uppercase tracking-widest">Sin stock disponible</div>';
                        return;
                    }

                    resultsEl.innerHTML = searchInventories.map(inv => {
                        const product = inv.product || {};
                        const added = productsAdded.some(p => p.inventoryID === inv.id);
                        const isWeight = product.sale_type === 'weight';
                        const unitLabel = isWeight ? 'g' : 'uds';
                        const priceUsd = isWeight && product.price_per_kg ?
                            parseFloat(product.price_per_kg) :
                            parseFloat(product.sell_price || 0);
                        const priceUsdLabel = isWeight ? `$${priceUsd.toFixed(2)}/kg` :
                            `$${priceUsd.toFixed(2)}`;
                        const priceBsLabel = isWeight ? `Bs ${(priceUsd * usdRate).toFixed(2)}/kg` :
                            `Bs ${(priceUsd * usdRate).toFixed(2)}`;
                        const expired = inv.expired_date ? new Date(inv.expired_date).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : 'Sin vencimiento';

                        return `<div class="bg-slate-50/80 rounded-xl p-2.5 border border-dashed border-slate-200 flex items-center justify-between gap-2">
                            <div class="min-w-0 space-y-0.5">
                                <h3 class="text-xs font-bold text-brand-navy truncate">${escapeHtml(product.name)}</h3>
                                <p class="text-[10px] text-slate-400 font-mono truncate">Lote: ${escapeHtml(inv.lote_number || 'S/L')} • Vence: ${escapeHtml(expired)}</p>
                                <p class="text-[11px] text-slate-500 font-medium">Stock: ${parseFloat(inv.stock) || 0} ${unitLabel}</p>
                                <p class="text-[11px] font-bold text-brand-navy">${priceUsdLabel} <span class="font-medium text-slate-400">· ${priceBsLabel}</span></p>
                            </div>
                            <button type="button" ${added ? 'disabled' : ''} onclick="addProductById(${inv.id})"
                                class="w-7 h-7 shrink-0 rounded-lg bg-brand-accent text-white flex items-center justify-center font-bold text-sm shadow-brand-float active:scale-90 transition disabled:opacity-30">
                                <span class="material-symbols-outlined text-[16px]">${added ? 'check' : 'add'}</span>
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
            const inventory = searchInventories.find(inv => inv.id == id);

            if (inventory) {
                addProduct(inventory);
            }
        }

        function addProduct(inventory) {
            if (!productsAdded.some(p => p.inventoryID === inventory.id)) {
                const isWeight = inventory.product.sale_type === 'weight';
                const effectivePrice = isWeight && inventory.product.price_per_kg ?
                    inventory.product.price_per_kg / 1000 :
                    parseFloat(inventory.product.sell_price);

                productsAdded.unshift({
                    inventoryID: inventory.id,
                    productID: inventory.product_id,
                    name: inventory.product.name,
                    barcode: inventory.product.barcode,
                    lote: inventory.lote_number,
                    maxStock: inventory.stock,
                    quantity: 1,
                    sell_price: effectivePrice,
                    display_price: inventory.product.price_per_kg || inventory.product.sell_price,
                    cost_per_unit: parseFloat(inventory.cost_per_unit),
                    sale_type: isWeight ? 'weight' : 'unit'
                });
            }

            refreshBasket();
        }

        function refreshBasket() {
            const createBtn = document.getElementById('btn-create-output');
            const emptyState = document.getElementById('empty-state');
            const summary = document.getElementById('total-summary');
            const hasProducts = productsAdded.length > 0;

            createBtn.disabled = !hasProducts;
            emptyState.classList.toggle('hidden', hasProducts);
            summary.classList.toggle('hidden', !hasProducts);

            document.getElementById('cart-badge').textContent =
                `${productsAdded.length} ${productsAdded.length === 1 ? 'ítem' : 'ítems'}`;

            document.getElementById('added-products').innerHTML = productsAdded.map((p, index) =>
                renderCartItem(p, index)).join('');

            updateBasketTotals();
        }

        function renderCartItem(p, index) {
            const unitLabel = p.sale_type === 'weight' ? 'g' : 'uds';
            const subtotal = p.sell_price * p.quantity;
            const subtotalBs = subtotal * usdRate;
            const unitPriceUsd = p.sale_type === 'weight' ? parseFloat(p.display_price || 0) : parseFloat(p.sell_price);
            const unitPriceBs = unitPriceUsd * usdRate;
            const unitPriceUsdLabel = p.sale_type === 'weight' ?
                `$${unitPriceUsd.toFixed(2)}/kg` :
                `$${unitPriceUsd.toFixed(2)}`;
            const unitPriceBsLabel = p.sale_type === 'weight' ?
                `Bs ${unitPriceBs.toFixed(2)}/kg` :
                `Bs ${unitPriceBs.toFixed(2)}`;

            return `<article data-cart-row="${p.inventoryID}" class="p-3 bg-slate-50/70 border border-slate-200/80 rounded-xl space-y-2 relative">
                <input type="hidden" name="products[${index}][productID]" value="${p.productID}">
                <input type="hidden" name="products[${index}][inventoryID]" value="${p.inventoryID}">
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <h3 class="text-xs font-bold text-brand-navy leading-tight truncate">${escapeHtml(p.name)}</h3>
                        <p class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">SKU: ${escapeHtml(p.barcode || 'SIN SKU')} • Lote: ${escapeHtml(p.lote || 'S/L')}</p>
                    </div>
                    <button type="button" onclick="removeProduct(${p.inventoryID})" aria-label="Eliminar producto"
                        class="text-slate-400 hover:text-red-500 p-1 -mr-1 transition shrink-0">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 gap-2">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center border border-slate-200 bg-white rounded-lg p-0.5">
                            <button type="button" onclick="stepQuantity(${p.inventoryID}, -1)"
                                class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs font-bold active:scale-90">−</button>
                            <input id="qty-${p.inventoryID}" type="number" min="1" max="${p.maxStock}" required
                                name="products[${index}][quantity]" value="${p.quantity}"
                                oninput="updateQty(${p.inventoryID}, this)"
                                class="w-10 bg-transparent border-0 p-0 text-center font-bold text-xs text-brand-navy focus:ring-0">
                            <button type="button" onclick="stepQuantity(${p.inventoryID}, 1)"
                                class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs font-bold active:scale-90">+</button>
                        </div>
                        <span class="text-[8px] font-bold text-slate-400">${unitLabel} • Max: ${p.maxStock}</span>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-slate-500 font-medium">P. Unit: ${unitPriceUsdLabel}</div>
                        <div class="text-[9px] text-slate-400">${unitPriceBsLabel}</div>
                        <div class="text-[10px] text-slate-400 font-medium mt-1">Subtotal</div>
                        <div class="text-sm font-extrabold text-brand-navy">$<span id="subtotal-${p.inventoryID}">${subtotal.toFixed(2)}</span></div>
                        <div class="text-[10px] font-semibold text-slate-500">Bs. <span id="subtotal-bs-${p.inventoryID}">${subtotalBs.toFixed(2)}</span></div>
                    </div>
                </div>
            </article>`;
        }

        function updateQty(id, element) {
            const p = productsAdded.find(x => x.inventoryID === id);

            if (!p) return;

            let value = parseFloat(element.value) || 0;
            value = p.sale_type === 'weight' ? Math.max(1, Math.round(value)) : Math.max(1, Math.floor(value));

            if (value > p.maxStock) {
                value = p.maxStock;
            }

            p.quantity = value;
            element.value = value;

            const subtotal = p.sell_price * value;
            setText(`subtotal-${id}`, subtotal.toFixed(2));
            setText(`subtotal-bs-${id}`, (subtotal * usdRate).toFixed(2));
            updateBasketTotals();
        }

        function stepQuantity(id, delta) {
            const p = productsAdded.find(x => x.inventoryID === id);
            const input = document.getElementById(`qty-${id}`);

            if (!p || !input) return;

            const current = parseInt(input.value, 10) || 1;
            const next = Math.min(p.maxStock, Math.max(1, current + delta));
            input.value = next;
            updateQty(id, input);
        }

        function removeProduct(id) {
            const index = productsAdded.findIndex(x => x.inventoryID === id);

            if (index !== -1) {
                productsAdded.splice(index, 1);
            }

            refreshBasket();
        }

        function updateBasketTotals() {
            const totalSold = productsAdded.reduce((sum, p) => sum + (p.sell_price * p.quantity), 0);
            const totalCost = productsAdded.reduce((sum, p) => sum + (p.cost_per_unit * p.quantity), 0);
            const profit = totalSold - totalCost;

            setText('total-sold-subtotal', '$' + totalSold.toFixed(2));
            setText('total-sold-subtotal-bs', 'Bs. ' + (totalSold * usdRate).toFixed(2));
            setText('total-sold-preview', totalSold.toFixed(2));
            setText('total-sold-bs', (totalSold * usdRate).toFixed(2));
            setText('total-profit-preview', '$' + profit.toFixed(2));
            setText('total-profit-bs-preview', 'Bs. ' + (profit * usdRate).toFixed(2));
            document.getElementById('final_total_sold').value = totalSold.toFixed(2);
        }

        function selectPaymentMethod(button) {
            const activeClasses = ['border-brand-accent/50', 'bg-emerald-50/60', 'text-brand-navy', 'shadow-sm'];
            const inactiveClasses = ['border-slate-200', 'bg-white', 'text-slate-600'];

            document.querySelectorAll('.payment-method-btn').forEach(function(item) {
                item.classList.remove(...activeClasses);
                item.classList.add(...inactiveClasses);
            });

            button.classList.remove(...inactiveClasses);
            button.classList.add(...activeClasses);
            document.getElementById('final_payment_method').value = button.dataset.payment;
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        }
    </script>
@endsection
