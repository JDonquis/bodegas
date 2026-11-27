@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <style>
        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
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
    <div class="mx-auto flex w-full max-w-md flex-col gap-4 font-jakarta">

        <section class="space-y-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-brand-navy">Registrar Gasto</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Selecciona los productos que el dueño tomará del inventario para consumo propio.
                </p>
            </div>

            <div
                class="flex items-center justify-between bg-white border border-rose-100 rounded-xl px-3.5 py-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span class="text-xs font-semibold text-slate-600">Tasa BCV Oficial:</span>
                    <span class="text-xs font-bold text-brand-navy">{{ number_format($usdRate ?? 0, 2) }} Bs / USD</span>
                </div>
                <span
                    class="text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-200/60 px-2 py-0.5 rounded-full">Consumo</span>
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
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[16px]">shopping_basket</span>
                    </div>
                    <h2 class="text-sm font-bold text-brand-navy">Productos del Gasto</h2>
                </div>
                <span id="cart-badge"
                    class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">0
                    ítems</span>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" id="expense-form">
                @csrf

                <div class="space-y-1.5">
                    <label for="expense-description"
                        class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Descripción</label>
                    <textarea name="description" id="expense-description" rows="2" maxlength="2000"
                        placeholder="Motivo del gasto (opcional)..."
                        class="w-full py-2.5 px-3 text-xs font-medium rounded-xl bg-slate-50 border border-slate-200 text-brand-navy placeholder:text-slate-400 focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition resize-none">{{ old('description') }}</textarea>
                </div>

                <div id="added-products" class="space-y-3 mt-3"></div>

                <div id="empty-state" class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-4xl text-slate-300">production_quantity_limits</span>
                    </div>
                    <p class="font-black text-[11px] uppercase tracking-widest text-slate-400">Selecciona productos del
                        inventario</p>
                    <p class="text-[11px] text-slate-400 mt-1">Usa el buscador para agregar mercancía.</p>
                </div>

                <div id="total-summary" class="hidden mt-4 pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Costo del gasto:</span>
                        <span id="total-cost" class="text-rose-600 font-bold">$0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Costo en Bolívares:</span>
                        <span id="total-cost-bs" class="text-brand-navy font-semibold">Bs. 0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Valor a precio de venta:</span>
                        <span id="total-retail" class="text-brand-navy font-semibold">$0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 font-medium">
                        <span>Ganancia dejada de percibir:</span>
                        <span id="total-lost" class="text-amber-600 font-bold">$0.00</span>
                    </div>
                </div>
            </form>
        </section>

        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ route('expenses') }}"
                class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl border border-slate-200 bg-white font-semibold text-xs text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-slate-400">close</span>
                Cancelar
            </a>
            <button type="submit" form="expense-form" id="btn-create-expense" disabled
                class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-brand-navy text-white font-bold text-xs shadow-md shadow-brand-navy/20 active:scale-[0.98] transition disabled:opacity-30 disabled:pointer-events-none">
                <span class="material-symbols-outlined text-[18px] text-brand-accent">save</span>
                Guardar Gasto
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const productsAdded = [];
        const usdRate = parseFloat("{{ $usdRate ?? 0 }}") || 0;
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
                        const unitLabel = isWeight ? 'kg' : 'uds';
                        const priceUsd = isWeight && product.price_per_kg ?
                            parseFloat(product.price_per_kg) :
                            parseFloat(product.sell_price || 0);
                        const priceUsdLabel = isWeight ? `$${priceUsd.toFixed(2)}/kg` :
                            `$${priceUsd.toFixed(2)}`;
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
                                <p class="text-[11px] font-bold text-brand-navy">${priceUsdLabel}</p>
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
                    parseFloat(inventory.product.price_per_kg) :
                    parseFloat(inventory.product.sell_price);

                productsAdded.unshift({
                    inventoryID: inventory.id,
                    productID: inventory.product_id,
                    name: inventory.product.name,
                    barcode: inventory.product.barcode,
                    lote: inventory.lote_number,
                    maxStock: parseFloat(inventory.stock),
                    quantity: 1,
                    cost_per_unit: parseFloat(inventory.cost_per_unit),
                    sell_price: effectivePrice,
                    display_price: inventory.product.price_per_kg || inventory.product.sell_price,
                    sale_type: isWeight ? 'weight' : 'unit'
                });
            }

            refreshBasket();
        }

        function refreshBasket() {
            const createBtn = document.getElementById('btn-create-expense');
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
            const unitLabel = p.sale_type === 'weight' ? 'kg' : 'uds';
            const minQuantity = p.sale_type === 'weight' ? '0.001' : '1';
            const quantityStep = p.sale_type === 'weight' ? '0.001' : '1';
            const subtotalCost = p.cost_per_unit * p.quantity;
            const subtotalRetail = p.sell_price * p.quantity;
            const unitPriceUsd = p.sale_type === 'weight' ? parseFloat(p.display_price || 0) : parseFloat(p.sell_price);
            const unitPriceLabel = p.sale_type === 'weight' ?
                `$${unitPriceUsd.toFixed(2)}/kg` :
                `$${unitPriceUsd.toFixed(2)}`;

            return `<article data-cart-row="${p.inventoryID}" class="p-3 bg-slate-50/70 border border-slate-200/80 rounded-xl space-y-2 relative">
                <input type="hidden" name="items[${index}][productID]" value="${p.productID}">
                <input type="hidden" name="items[${index}][inventoryID]" value="${p.inventoryID}">
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
                            <input id="qty-${p.inventoryID}" type="number" min="${minQuantity}" step="${quantityStep}" max="${p.maxStock}" required
                                name="items[${index}][quantity]" value="${p.quantity}"
                                oninput="updateQty(${p.inventoryID}, this)"
                                class="w-12 bg-transparent border-0 p-0 text-center font-bold text-xs text-brand-navy focus:ring-0">
                            <button type="button" onclick="stepQuantity(${p.inventoryID}, 1)"
                                class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs font-bold active:scale-90">+</button>
                        </div>
                        <span class="text-[8px] font-bold text-slate-400">${unitLabel} • Max: ${p.maxStock}</span>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-slate-500 font-medium">P. Venta: ${unitPriceLabel}</div>
                        <div class="text-[10px] text-slate-400">${p.sale_type === 'weight' ? 'Costo/kg' : 'Costo unit'}: $${parseFloat(p.cost_per_unit).toFixed(2)}</div>
                        <div class="text-[10px] text-slate-400 font-medium mt-1">Costo</div>
                        <div class="text-sm font-extrabold text-rose-600">$<span id="subtotal-cost-${p.inventoryID}">${subtotalCost.toFixed(2)}</span></div>
                        <div class="text-[10px] font-semibold text-slate-500">Venta: $<span id="subtotal-retail-${p.inventoryID}">${subtotalRetail.toFixed(2)}</span></div>
                    </div>
                </div>
            </article>`;
        }

        function updateQty(id, element) {
            const p = productsAdded.find(x => x.inventoryID === id);

            if (!p) return;

            let value = parseFloat(element.value) || 0;

            if (p.sale_type === 'weight') {
                value = Math.max(0.001, Math.round(value * 1000) / 1000);
            } else {
                value = Math.max(1, Math.floor(value));
            }

            if (value > p.maxStock) {
                value = p.sale_type === 'weight'
                    ? Math.floor(p.maxStock * 1000) / 1000
                    : Math.floor(p.maxStock);
            }

            p.quantity = value;
            element.value = value;

            const subtotalCost = p.cost_per_unit * value;
            const subtotalRetail = p.sell_price * value;
            setText(`subtotal-cost-${id}`, subtotalCost.toFixed(2));
            setText(`subtotal-retail-${id}`, subtotalRetail.toFixed(2));
            updateBasketTotals();
        }

        function stepQuantity(id, delta) {
            const p = productsAdded.find(x => x.inventoryID === id);
            const input = document.getElementById(`qty-${id}`);

            if (!p || !input) return;

            const step = p.sale_type === 'weight' ? 0.1 : 1;
            const minimum = p.sale_type === 'weight' ? 0.001 : 1;
            const current = parseFloat(input.value) || minimum;
            const next = Math.min(p.maxStock, Math.max(minimum, current + (delta * step)));
            input.value = p.sale_type === 'weight' ? Math.round(next * 1000) / 1000 : Math.floor(next);
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
            const totalCost = productsAdded.reduce((sum, p) => sum + (p.cost_per_unit * p.quantity), 0);
            const totalRetail = productsAdded.reduce((sum, p) => sum + (p.sell_price * p.quantity), 0);
            const lostProfit = totalRetail - totalCost;

            setText('total-cost', '$' + totalCost.toFixed(2));
            setText('total-cost-bs', 'Bs. ' + (totalCost * usdRate).toFixed(2));
            setText('total-retail', '$' + totalRetail.toFixed(2));
            setText('total-lost', '$' + lostProfit.toFixed(2));
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        }
    </script>
@endsection
