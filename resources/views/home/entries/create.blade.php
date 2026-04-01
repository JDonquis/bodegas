@extends('layout.app')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6">
            <div>
                <h2 class="text-3xl font-extrabold text-primary tracking-tight mb-2 font-headline">Registro de Entrada</h2>
                <p class="text-outline font-body">Ingreso de mercancía con conversión de divisas en tiempo real.</p>
            </div>
            <div class="flex gap-4 w-full md:w-auto">
                <button type="button" onclick="window.location.href='{{ route('entries') }}'"
                    class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-3 rounded-full border border-outline-variant text-primary font-bold hover:bg-surface-container transition-all active:scale-95">
                    <span class="material-symbols-outlined">close</span>
                    Cancelar
                </button>
                <button type="submit" form="entry-form" id="btn-create-entry" disabled
                    class="flex-1 md:flex-none flex items-center justify-center gap-2 px-8 py-3 rounded-full bg-primary text-white font-bold shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 disabled:pointer-events-none">
                    <span class="material-symbols-outlined">inventory</span>
                    Finalizar Entrada
                </button>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-8">

            <!-- Search & Rate Info -->
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <!-- Rate Card -->
                <div
                    class="bg-secondary-container text-on-secondary-container p-6 rounded-3xl shadow-sm border border-secondary/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <span class="material-symbols-outlined text-6xl">trending_up</span>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-1 opacity-80">Tasa BCV Oficial</p>
                    <p class="text-3xl font-black font-headline">{{ number_format($usdRate ?? 0, 2) }} <span
                            class="text-sm font-bold opacity-70">Bs/USD</span></p>
                    <input type="hidden" id="usd-rate" value="{{ $usdRate ?? 0 }}">
                </div>

                <!-- Search Section -->
                <section class="bg-surface-container-lowest p-8 rounded-3xl border-t-8 border-primary shadow-sm">
                    <h3 class="text-lg font-bold text-primary font-headline mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined">search</span>
                        Buscador de Productos
                    </h3>

                    <div class="relative group">
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-outline/60">barcode_scanner</span>
                            <input
                                class="w-full pl-12 pr-14 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-secondary/40 text-on-surface placeholder-outline/40 transition-all font-body"
                                placeholder="Escanea o escribe..." type="text" oninput="searchProduct()"
                                id="html5-search-input" autofocus>

                            <div class="absolute right-2">
                                <button type="button" id="btnCreateProduct" disabled onclick="createProduct()"
                                    class="p-2 bg-primary text-white rounded-xl hover:bg-primary-container transition-all disabled:opacity-0 shadow-md">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-outline-variant/10 bg-white">
                        <table class="w-full text-left border-collapse">
                            <tbody id="search-results" class="text-sm font-body divide-y divide-surface-container">
                                <tr>
                                    <td class="px-6 py-10 text-center opacity-30 italic text-xs"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- Detail Carga Section -->
            <div class="col-span-12 lg:col-span-8">
                <section
                    class="bg-surface-container-lowest p-8 rounded-3xl border-t-8 border-secondary shadow-sm min-h-[600px] flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-secondary-container/30 rounded-2xl flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined text-3xl">fluid_med</span>
                            </div>
                            <h3 class="text-xl font-bold text-primary font-headline">Carga de Detalles</h3>
                        </div>
                    </div>

                    <form action="{{ route('entries.store') }}" method="POST" id="entry-form" class="flex-1 flex flex-col">
                        @csrf
                        <div class="overflow-x-auto flex-1 -mx-4">
                            <table class="w-full text-left border-separate border-spacing-y-3">
                                <thead>
                                    <tr class="text-outline text-[10px] uppercase tracking-[0.15em] font-black px-4">
                                        <th class="pb-2 pl-6">Producto Info</th>
                                        <th class="pb-2 text-center w-24">Cantidad</th>
                                        <th class="pb-2 text-center w-36">Costo ($)</th>
                                        <th class="pb-2 text-center w-36">Costo (Bs)</th>
                                        <th class="pb-2 text-center w-32">Lote *</th>
                                        <th class="pb-2 text-right pr-6">Vencimiento</th>
                                    </tr>
                                </thead>
                                <tbody id="added-products" class="text-sm font-body">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>

                            <div id="empty-state" class="py-32 flex flex-col items-center justify-center text-outline/20">
                                <div
                                    class="w-24 h-24 bg-surface-container-low rounded-full flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-6xl">add_shopping_cart</span>
                                </div>
                                <p class="font-black text-xs uppercase tracking-widest">Agrega productos para procesar</p>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div id="total-summary" class="mt-auto pt-8 border-t border-outline-variant/30 hidden">
                            <div class="flex justify-between items-center bg-surface-container-low p-6 rounded-3xl">
                                <div class="flex gap-10">
                                    <div>
                                        <p class="text-[10px] font-black text-outline uppercase tracking-widest mb-1">Items
                                        </p>
                                        <p class="text-3xl font-black text-primary font-headline" id="items-count">0</p>
                                    </div>
                                    <div class="h-12 w-px bg-outline-variant/30"></div>
                                    <div>
                                        <p class="text-[10px] font-black text-outline uppercase tracking-widest mb-1">Total
                                            Inversión</p>
                                        <p class="text-3xl font-black text-secondary font-headline" id="total-cost-preview">
                                            0.00$</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-outline uppercase tracking-widest mb-1 italic">
                                        Total en Bolívares</p>
                                    <p class="text-xl font-bold text-primary/60 font-headline" id="total-bs-preview">0.00 Bs
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const productsAdded = [];
    const usdRate = parseFloat(document.getElementById('usd-rate').value) || 0;

    document.addEventListener('DOMContentLoaded', function() {
        const oldProducts = @json($oldProducts ?? []);
        if (oldProducts.length > 0) {
            oldProducts.forEach(p => {
                productsAdded.push({
                    id: p.id,
                    name: p.name,
                    barcode: p.barcode,
                    quantity: p.quantity,
                    cost: p.cost,
                    cost_bs: p.cost_bs,
                    lote_number: p.lote_number,
                    date: p.date
                });
            });
            refreshProducts();
        }
    });

    function searchProduct() {            let searchInput = document.getElementById('html5-search-input').value;

            if (searchInput.length < 1) {
                document.getElementById('search-results').innerHTML =
                    '<tr><td class="px-6 py-10 text-center opacity-30 italic text-xs">Esperando entrada...</td></tr>';
                document.getElementById('btnCreateProduct').disabled = true;
                return;
            }

            fetch(`/home/productos/search/${encodeURIComponent(searchInput)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('btnCreateProduct').disabled = data.products.length !== 0;

                    if (data.products.length === 0) {
                        document.getElementById('search-results').innerHTML =
                            '<tr><td class="px-6 py-10 text-center text-error/60 text-xs font-black uppercase tracking-widest">Sin resultados</td></tr>';
                        return;
                    }

                    // Auto-add if exact barcode match (single result and matches input)
                    if (data.products.length === 1 && data.products[0].barcode === searchInput) {
                        addProduct(data.products[0]);
                        document.getElementById('html5-search-input').value = '';
                        document.getElementById('search-results').innerHTML =
                            '<tr><td class="px-6 py-10 text-center text-secondary text-xs font-black uppercase tracking-widest">¡Producto Detectado!</td></tr>';
                        return;
                    }

                    let results = data.products.map(product => {
                        const productJson = JSON.stringify(product).replace(/"/g, '&quot;');
                        let isAdded = productsAdded.some(p => p.id === product.id);

                        return `<tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-6 py-4 cursor-pointer" onclick="${isAdded ? '' : `addProduct(${productJson})`}">
                            <div class="flex flex-col">
                                <span class="font-bold text-primary text-sm group-hover:text-secondary transition-colors">${product.name}</span>
                                <span class="text-[9px] text-outline/60 font-mono mt-0.5">${product.barcode || 'SIN SKU'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" ${isAdded ? 'disabled' : ''} onclick="addProduct(${productJson})"
                                    class="p-2 bg-secondary/10 text-secondary rounded-xl hover:bg-secondary hover:text-white transition-all transform active:scale-90 disabled:opacity-20" productID="${product.id}">
                                <span class="material-symbols-outlined text-sm">add_circle</span>
                            </button>
                        </td>
                    </tr>`;
                    }).join('');
                    document.getElementById('search-results').innerHTML = results;
                })
                .catch(error => console.error(error));
        }

        function addProduct(product) {
            if (!productsAdded.some(p => p.id === product.id)) {
                product.quantity = 1;
                product.date = '';
                product.cost = 0;
                product.cost_bs = 0;
                product.lote_number = "";
                productsAdded.unshift(product);
            }
            refreshProducts();
        }

        function refreshProducts() {
            const createEntryBtn = document.getElementById('btn-create-entry');
            const emptyState = document.getElementById('empty-state');
            const summary = document.getElementById('total-summary');

            if (productsAdded.length > 0) {
                createEntryBtn.disabled = false;
                emptyState.classList.add('hidden');
                summary.classList.remove('hidden');
                updateTotalCalculations();
            } else {
                createEntryBtn.disabled = true;
                emptyState.classList.remove('hidden');
                summary.classList.add('hidden');
            }

            let results = productsAdded.map((product, index) => {
                return `<tr class="bg-white hover:shadow-md transition-all group ">
                <td class="py-4 pl-6 rounded-l-2xl">
                    <input type="hidden" name="products[${index}][productID]" value="${product.id}">
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="cancelProduct(${product.id})" class="text-outline/30 hover:text-error transition-all scale-100 hover:scale-125">
                            <span class="material-symbols-outlined text-xl">remove_circle</span>
                        </button>
                        <div class="flex flex-col">
                            <span class="font-black text-primary text-sm leading-none">${product.name}</span>
                            <span class="text-[9px] text-outline font-mono mt-1 uppercase tracking-tighter">${product.barcode || 'N/A'}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-2">
                    <input class="w-full p-3 bg-surface-container-low border-none rounded-xl py-2.5 text-sm text-center focus:ring-2 focus:ring-primary/40 font-black text-primary"
                           required type="number" oninput="refreshData(${product.id}, 'quantity', this)" min="1"
                           name="products[${index}][quantity]" value="${product.quantity}">
                </td>
                <td class="py-4 px-2">
                    <div class="relative">
                        <input class="w-full p-3 bg-surface-container-low border-none rounded-xl py-2.5 pl-6 text-sm text-center focus:ring-2 focus:ring-secondary/40 font-black text-secondary"
                               required type="number" step="0.01" min="0" oninput="refreshData(${product.id}, 'cost', this)"
                               name="products[${index}][cost]" value="${product.cost}">
                    </div>
                </td>
                <td class="py-4 px-2">
                    <div class="relative">
                        <input class="w-full p-3 bg-surface-container-low border-none rounded-xl py-2.5 pl-6 text-sm text-center focus:ring-2 focus:ring-primary/20 font-bold text-primary/60"
                               type="number" step="0.01" min="0" oninput="refreshData(${product.id}, 'cost_bs', this)"
                               value="${product.cost_bs}">
                    </div>
                </td>
                <td class="py-4 px-2">
                    <input class="w-full p-3 bg-surface-container-low border-none rounded-xl py-2.5 text-sm text-center focus:ring-2 focus:ring-primary/40 font-mono placeholder:text-outline/20"
                           required type="text" oninput="refreshData(${product.id}, 'lote_number', this)"
                           placeholder="LOTE-000"
                           name="products[${index}][lote_number]" value="${product.lote_number}">
                </td>
                <td class="py-4 pr-6 rounded-r-2xl">
                    <input class="w-full p-3 bg-surface-container-low border-none rounded-xl py-2.5 text-[10px] focus:ring-2 focus:ring-primary/40 font-black text-primary uppercase"
                           type="date" oninput="refreshData(${product.id}, 'date', this)"
                           value="${product.date}" name="products[${index}][expiredDate]">
                </td>
            </tr>`;
            }).join('');
            document.getElementById('added-products').innerHTML = results;
        }

        function refreshData(productID, type, element) {
            const product = productsAdded.find(p => p.id == productID);
            if (!product) return;

            if (type === 'cost') {
                product.cost = parseFloat(element.value) || 0;
                if (usdRate > 0) {
                    product.cost_bs = (product.cost * usdRate).toFixed(2);
                    // Update the BS input in the same row
                    const row = element.closest('tr');
                    const bsInput = row.querySelector('input[oninput*="cost_bs"]');
                    if (bsInput) bsInput.value = product.cost_bs;
                }
            } else if (type === 'cost_bs') {
                product.cost_bs = parseFloat(element.value) || 0;
                if (usdRate > 0) {
                    product.cost = (product.cost_bs / usdRate).toFixed(2);
                    // Update the USD input in the same row
                    const row = element.closest('tr');
                    const usdInput = row.querySelector('input[oninput*="\'cost\'"]');
                    if (usdInput) usdInput.value = product.cost;
                }
            } else {
                product[type === 'date' ? 'date' : type] = element.value;
            }

            updateTotalCalculations();
        }

        function updateTotalCalculations() {
            const countSpan = document.getElementById('items-count');
            const costPreview = document.getElementById('total-cost-preview');
            const bsPreview = document.getElementById('total-bs-preview');

            let totalUsd = productsAdded.reduce((sum, p) => sum + parseFloat(p.cost || 0), 0);
            let totalBs = totalUsd * usdRate;

            countSpan.innerText = productsAdded.length;
            costPreview.innerText = totalUsd.toFixed(2) + '$';
            bsPreview.innerText = totalBs.toFixed(2) + ' Bs';
        }

        function cancelProduct(productID) {
            const index = productsAdded.findIndex(p => p.id === productID);
            if (index !== -1) productsAdded.splice(index, 1);

            const btn = document.querySelector(`button[productID="${productID}"]`);
            if (btn) btn.disabled = false;

            refreshProducts();
        }

        function createProduct() {
            if (!confirm('¿Deseas crear un nuevo producto con este nombre?')) return;

            let searchInput = document.getElementById('html5-search-input').value;
            fetch(`/home/productos`, {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({
                        _token: "{{ csrf_token() }}",
                        productName: searchInput,
                        sellPrice: 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    addProduct(data.product);
                    document.getElementById('html5-search-input').value = '';
                    searchProduct();
                })
                .catch(error => console.error(error));
        }
    </script>
@endsection
