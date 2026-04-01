@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6">
        <div>
            <h2 class="text-3xl font-extrabold text-primary tracking-tight mb-2 font-headline">Nueva Salida de Mercancía</h2>
            <p class="text-outline font-body">Registra ventas o egresos de productos con control de inventario.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <button type="button" onclick="window.location.href='{{ route('outputs') }}'" 
                    class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-3 rounded-full border border-outline-variant text-primary font-bold hover:bg-surface-container transition-all active:scale-95">
                <span class="material-symbols-outlined">close</span>
                Cancelar
            </button>
            <button type="submit" form="output-form" id="btn-create-output" disabled
                    class="flex-1 md:flex-none flex items-center justify-center gap-2 px-8 py-3 rounded-full bg-primary text-white font-bold shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 disabled:pointer-events-none">
                <span class="material-symbols-outlined">shopping_bag</span>
                Confirmar Venta
            </button>
        </div>
    </header>

    <div class="grid grid-cols-12 gap-8">
        
        <!-- Sidebar: Client & Search -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            
            <!-- Client Selection Card -->
            <section class="bg-surface-container-lowest p-8 rounded-[2.5rem] border-t-8 border-secondary shadow-sm">
                <h3 class="text-lg font-bold text-primary font-headline mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">person_add</span>
                    Identificación del Cliente
                </h3>

                <div class="space-y-6">
                    <!-- Toggle Type -->
                    <div class="bg-surface-container-low p-1.5 rounded-2xl flex gap-1">
                        <button type="button" onclick="toggleClientType('registered')" id="tab-registered" 
                                class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all bg-white text-primary shadow-sm">
                            Registrado
                        </button>
                        <button type="button" onclick="toggleClientType('casual')" id="tab-casual" 
                                class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all text-outline hover:text-primary">
                            Casual / Nuevo
                        </button>
                    </div>

                    <!-- Registered Client Select -->
                    <div id="section-registered" class="space-y-2">
                        <label class="block text-[10px] font-black text-outline uppercase tracking-widest ml-1">Seleccionar Cliente</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline/60">group</span>
                            <select id="client_id_select" class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-secondary/40 text-on-surface font-bold text-sm appearance-none transition-all">
                                <option value="">-- Seleccione un cliente --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->ci }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Casual Client Input -->
                    <div id="section-casual" class="hidden space-y-2">
                        <label class="block text-[10px] font-black text-outline uppercase tracking-widest ml-1">Nombre del Cliente</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline/60">person_edit</span>
                            <input type="text" id="client_name_input" placeholder="Nombre completo..." 
                                   class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-secondary/40 text-on-surface font-bold text-sm transition-all">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Product Search Card -->
            <section class="bg-surface-container-lowest p-8 rounded-[2.5rem] border-t-8 border-primary shadow-sm">
                <h3 class="text-lg font-bold text-primary font-headline mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">search</span>
                    Buscador de Stock
                </h3>

                <div class="relative group">
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-4 text-outline/60">barcode_scanner</span>
                        <input class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary/40 text-on-surface placeholder-outline/40 transition-all font-body" 
                               placeholder="Escanea o busca producto..." 
                               type="text" 
                               oninput="searchProduct()" 
                               id="html5-search-input">
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-outline-variant/10 bg-white">
                    <table class="w-full text-left border-collapse">
                        <tbody id="search-results" class="text-sm font-body divide-y divide-surface-container">
                            <tr>
                                <td class="px-6 py-10 text-center opacity-30 italic text-xs">Busca productos en stock...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Main Content: Basket -->
        <div class="col-span-12 lg:col-span-8">
            <section class="bg-surface-container-lowest p-8 rounded-[3rem] border-t-8 border-primary shadow-sm min-h-[600px] flex flex-col relative overflow-hidden">
                <!-- Watermark Icon -->
                <div class="absolute -top-10 -right-10 opacity-[0.03] pointer-events-none">
                    <span class="material-symbols-outlined text-[20rem]">shopping_basket</span>
                </div>

                <div class="flex items-center gap-3 mb-8 relative z-10">
                    <div class="w-12 h-12 bg-primary-fixed rounded-2xl flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-3xl">local_mall</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary font-headline">Carrito de Salida</h3>
                </div>

                <form action="{{ route('outputs.store') }}" method="POST" id="output-form" class="flex-1 flex flex-col relative z-10">
                    @csrf
                    <!-- Hidden inputs for backend logic -->
                    <input type="hidden" name="client_id" id="final_client_id">
                    <input type="hidden" name="client_name" id="final_client_name">
                    <input type="hidden" name="total_sold" id="final_total_sold">

                    <div class="overflow-x-auto flex-1 -mx-4">
                        <table class="w-full text-left border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-outline text-[10px] uppercase tracking-[0.15em] font-black px-4">
                                    <th class="pb-2 pl-6">Producto / Lote</th>
                                    <th class="pb-2 text-center w-24">Cantidad</th>
                                    <th class="pb-2 text-right w-32">Precio Unit.</th>
                                    <th class="pb-2 text-right pr-6 w-32">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="added-products" class="text-sm font-body">
                                <!-- JS Injection -->
                            </tbody>
                        </table>

                        <div id="empty-state" class="py-32 flex flex-col items-center justify-center text-outline/20">
                            <span class="material-symbols-outlined text-7xl mb-4">production_quantity_limits</span>
                            <p class="font-black text-xs uppercase tracking-[0.2em]">Selecciona productos del inventario</p>
                        </div>
                    </div>

                    <!-- Totals Panel -->
                    <div id="total-summary" class="mt-auto pt-8 border-t border-outline-variant/30 hidden">
                        <div class="bg-surface-container-low p-8 rounded-[2.5rem] flex flex-col sm:flex-row justify-between items-center gap-8">
                            <div class="flex gap-12">
                                <div>
                                    <p class="text-[10px] font-black text-outline uppercase tracking-widest mb-1">Ítems</p>
                                    <p class="text-4xl font-black text-primary font-headline" id="items-count">0</p>
                                </div>
                                <div class="h-14 w-px bg-outline-variant/30"></div>
                                <div>
                                    <p class="text-[10px] font-black text-outline uppercase tracking-widest mb-1">Total a Cobrar</p>
                                    <div class="flex flex-col">
                                        <p class="text-4xl font-black text-secondary font-headline" id="total-sold-preview">0.00$</p>
                                        <p class="text-xs font-bold text-primary/40" id="total-sold-bs">≈ 0.00 Bs</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-full sm:w-auto">
                                <div class="bg-white/60 backdrop-blur-sm p-4 rounded-2xl border border-white flex flex-col gap-1 shadow-sm">
                                    <div class="flex justify-between gap-6">
                                        <span class="text-[9px] font-bold text-outline uppercase">Utilidad Estimada:</span>
                                        <span class="text-xs font-black text-primary" id="total-profit-preview">0.00$</span>
                                    </div>
                                    <div class="h-1 bg-surface-container rounded-full overflow-hidden">
                                        <div class="h-full bg-secondary w-full opacity-30"></div>
                                    </div>
                                </div>
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
    const usdRate = parseFloat("{{ $usdRate ?? 0 }}");
    let currentClientType = 'registered';

    function toggleClientType(type) {
        currentClientType = type;
        const regTab = document.getElementById('tab-registered');
        const casTab = document.getElementById('tab-casual');
        const regSec = document.getElementById('section-registered');
        const casSec = document.getElementById('section-casual');

        if (type === 'registered') {
            regTab.className = "flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all bg-white text-primary shadow-sm";
            casTab.className = "flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all text-outline hover:text-primary";
            regSec.classList.remove('hidden');
            casSec.classList.add('hidden');
            document.getElementById('client_name_input').value = "";
        } else {
            casTab.className = "flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all bg-white text-primary shadow-sm";
            regTab.className = "flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all text-outline hover:text-primary";
            casSec.classList.remove('hidden');
            regSec.classList.add('hidden');
            document.getElementById('client_id_select').value = "";
        }
        syncClientData();
    }

    function syncClientData() {
        document.getElementById('final_client_id').value = document.getElementById('client_id_select').value;
        document.getElementById('final_client_name').value = document.getElementById('client_name_input').value;
    }

    document.getElementById('client_id_select').addEventListener('change', syncClientData);
    document.getElementById('client_name_input').addEventListener('input', syncClientData);

    function searchProduct() {
        let searchInput = document.getElementById('html5-search-input').value;
        if (searchInput.length < 1) {
            document.getElementById('search-results').innerHTML = '<tr><td class="px-6 py-10 text-center opacity-30 italic text-xs">Busca productos en stock...</td></tr>';
            return;
        }

        fetch(`/home/inventario/search-lots/${encodeURIComponent(searchInput)}`)
            .then(response => response.json())
            .then(data => {
                if (data.inventories.length === 0) {
                    document.getElementById('search-results').innerHTML = '<tr><td class="px-6 py-10 text-center text-error/60 text-xs font-black uppercase tracking-widest">Sin stock disponible</td></tr>';
                    return;
                }

                document.getElementById('search-results').innerHTML = data.inventories.map(inv => {
                    const invJson = JSON.stringify(inv).replace(/"/g, '&quot;');
                    let isAdded = productsAdded.some(p => p.inventoryID === inv.id);
                    
                    // Formatear fecha de vencimiento
                    let expiredInfo = 'Sin Vencimiento';
                    if(inv.expired_date) {
                        const date = new Date(inv.expired_date);
                        expiredInfo = date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                    }

                    return `<tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-6 py-4 cursor-pointer" onclick="${isAdded ? '' : `addProduct(${invJson})`}">
                            <div class="flex flex-col">
                                <span class="font-bold text-primary text-sm">${inv.product.name}</span>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="text-[8px] font-black bg-secondary-container/40 text-secondary px-1.5 py-0.5 rounded uppercase tracking-tighter">Lote: ${inv.lote_number || 'S/L'}</span>
                                    <span class="text-[8px] font-bold text-outline bg-surface-container-low px-1.5 py-0.5 rounded uppercase">Vence: ${expiredInfo}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-black text-primary">${inv.stock} uds.</span>
                                <button type="button" ${isAdded ? 'disabled' : ''} onclick="addProduct(${invJson})" 
                                        class="mt-1 p-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-all disabled:opacity-20">
                                    <span class="material-symbols-outlined text-xs">add_shopping_cart</span>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                }).join('');
            });
    }

    function addProduct(inventory) {
        if (!productsAdded.some(p => p.inventoryID === inventory.id)) {
            productsAdded.unshift({
                inventoryID: inventory.id,
                productID: inventory.product_id,
                name: inventory.product.name,
                barcode: inventory.product.barcode,
                lote: inventory.lote_number,
                maxStock: inventory.stock,
                quantity: 1,
                sell_price: parseFloat(inventory.product.sell_price),
                cost_per_unit: parseFloat(inventory.cost_per_unit)
            });
        }
        refreshBasket();
    }

    function refreshBasket() {
        const createBtn = document.getElementById('btn-create-output');
        const emptyState = document.getElementById('empty-state');
        const summary = document.getElementById('total-summary');
        
        if (productsAdded.length > 0) {
            createBtn.disabled = false;
            emptyState.classList.add('hidden');
            summary.classList.remove('hidden');
            updateBasketTotals();
        } else {
            createBtn.disabled = true;
            emptyState.classList.remove('hidden');
            summary.classList.add('hidden');
        }

        document.getElementById('added-products').innerHTML = productsAdded.map((p, index) => {
            return `<tr class="bg-white hover:shadow-md transition-all group border border-outline-variant/10">
                <td class="py-4 pl-6 rounded-l-3xl">
                    <input type="hidden" name="products[${index}][productID]" value="${p.productID}">
                    <input type="hidden" name="products[${index}][inventoryID]" value="${p.inventoryID}">
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="removeProduct(${p.inventoryID})" class="text-outline/30 hover:text-error transition-all scale-100 hover:scale-125">
                            <span class="material-symbols-outlined text-xl">remove_circle</span>
                        </button>
                        <div class="flex flex-col">
                            <span class="font-black text-primary text-base leading-none">${p.name}</span>
                            <span class="text-[9px] text-outline font-mono mt-1 uppercase tracking-tighter">Lote: ${p.lote || 'S/L'} • SKU: ${p.barcode || '---'}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-2">
                    <div class="flex flex-col items-center gap-1">
                        <input class="w-20 bg-surface-container-low border-none rounded-xl py-2 text-sm text-center focus:ring-2 focus:ring-primary/40 font-black text-primary" 
                               required type="number" oninput="updateQty(${p.inventoryID}, this)" min="1" max="${p.maxStock}" 
                               name="products[${index}][quantity]" value="${p.quantity}">
                        <span class="text-[8px] font-bold text-outline/40">Max: ${p.maxStock}</span>
                    </div>
                </td>
                <td class="py-4 px-2 text-right">
                    <span class="text-sm font-black text-primary/60">${p.sell_price.toFixed(2)}$</span>
                </td>
                <td class="py-4 pr-6 rounded-r-3xl text-right">
                    <span class="text-base font-black text-primary">${(p.sell_price * p.quantity).toFixed(2)}$</span>
                </td>
            </tr>`;
        }).join('');
    }

    function updateQty(id, element) {
        const p = productsAdded.find(x => x.inventoryID === id);
        if (p) {
            p.quantity = parseInt(element.value) || 0;
            if (p.quantity > p.maxStock) {
                p.quantity = p.maxStock;
                element.value = p.maxStock;
            }
            refreshBasket();
        }
    }

    function removeProduct(id) {
        const idx = productsAdded.findIndex(x => x.inventoryID === id);
        if (idx !== -1) productsAdded.splice(idx, 1);
        refreshBasket();
    }

    function updateBasketTotals() {
        const totalSold = productsAdded.reduce((sum, p) => sum + (p.sell_price * p.quantity), 0);
        const totalCost = productsAdded.reduce((sum, p) => sum + (p.cost_per_unit * p.quantity), 0);
        const profit = totalSold - totalCost;

        document.getElementById('items-count').innerText = productsAdded.length;
        document.getElementById('total-sold-preview').innerText = totalSold.toFixed(2) + '$';
        document.getElementById('total-sold-bs').innerText = `≈ ${(totalSold * usdRate).toFixed(2)} Bs`;
        document.getElementById('total-profit-preview').innerText = profit.toFixed(2) + '$';
        document.getElementById('final_total_sold').value = totalSold.toFixed(2);
    }
</script>
@endsection
