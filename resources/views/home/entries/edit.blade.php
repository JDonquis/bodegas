@extends('layout.app')

@section('content')
<!-- Header Section -->
<section class="mb-10 flex items-end gap-4">
    <a href="{{ route('entries') }}" class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-primary">
        <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2">Editar Entrada</h1>
        <p class="text-on-surface-variant font-body">Modifica los detalles de la entrada registrada.</p>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <!-- Search Section -->
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border border-outline-variant/30">
            <h3 class="text-lg font-bold text-primary mb-4 font-headline">1. Añadir más Productos</h3>
            <div class="relative mb-6">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input class="w-full bg-surface-container-low border-none rounded-xl py-3 pl-10 pr-12 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/60 font-body" 
                       placeholder="Nombre o código de barras..." 
                       type="search" 
                       oninput="searchProduct()" 
                       id="html5-search-input">
            </div>

            <div class="overflow-hidden rounded-xl border border-outline-variant/20">
                <table class="w-full text-left">
                    <tbody id="search-results" class="text-sm font-body divide-y divide-surface-container">
                        <tr>
                            <td class="px-4 py-8 text-center text-on-surface-variant/40 italic text-xs">Empieza a escribir para buscar...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Added Products Table -->
    <div class="lg:col-span-8">
        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm border border-outline-variant/30">
            <h3 class="text-lg font-bold text-primary mb-6 font-headline">2. Lista de Productos en Entrada</h3>
            
            <form action="{{ route('entries.update', ['entry' => $entryGeneral->id]) }}" method="POST" id="entry-form">
                @csrf
                @method('PUT')
                <div class="overflow-x-auto -mx-8">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant text-[10px] uppercase tracking-widest font-black">
                                <th class="px-8 py-3">Producto</th>
                                <th class="px-4 py-3 text-center w-24">Cantidad</th>
                                <th class="px-4 py-3 text-center w-32">Costo Tot. ($)</th>
                                <th class="px-4 py-3 text-center w-32">Nro. Lote</th>
                                <th class="px-8 py-3 text-right">Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody id="added-products" class="text-sm font-body divide-y divide-surface-container">
                            <!-- Populated via JS on load -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-10 pt-6 border-t border-outline-variant/30 flex justify-between items-center">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Resumen de Actualización</p>
                        <p class="text-xl font-headline font-extrabold text-primary"><span id="items-count">0</span> ítems en total</p>
                    </div>
                    <button type="submit" id="btn-create-entry" 
                            class="bg-secondary hover:bg-on-secondary-container text-white px-10 py-4 rounded-full flex items-center gap-2 transition-all font-headline font-bold shadow-lg shadow-secondary/20 transform active:scale-95 disabled:opacity-30">
                        <span class="material-symbols-outlined text-lg">sync</span>
                        Actualizar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const productsAdded = [];

    // Pre-populate with existing entries
    document.addEventListener('DOMContentLoaded', () => {
        const existingEntries = @json($entries);
        existingEntries.forEach(entry => {
            productsAdded.push({
                id: entry.product.id,
                name: entry.product.name,
                barcode: entry.product.barcode,
                quantity: entry.quantity,
                cost: entry.cost,
                lote_number: entry.lote_number,
                date: entry.expired_date ? entry.expired_date.split('T')[0] : ''
            });
        });
        refreshProducts();
    });

    function searchProduct() {
        let searchInput = document.getElementById('html5-search-input').value;
        if (searchInput.length < 1) {
            document.getElementById('search-results').innerHTML = '<tr><td class="px-4 py-8 text-center text-on-surface-variant/40 italic text-xs">Empieza a escribir para buscar...</td></tr>';
            return;
        }

        fetch(`/home/productos/search/${encodeURIComponent(searchInput)}`)
            .then(response => response.json())
            .then(data => {
                let results = data.products.map(product => {
                    const productJson = JSON.stringify(product).replace(/"/g, '&quot;');
                    let status = productsAdded.some(p => p.id === product.id) ? 'disabled opacity-20' : '';
                    
                    return `<tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-4 py-3 cursor-pointer" onclick="!${status} && addProduct(${productJson})">
                            <p class="font-bold text-primary text-xs">${product.name}</p>
                            <p class="text-[10px] text-on-surface-variant font-mono">${product.barcode || 'Sin código'}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" ${status} onclick="addProduct(${productJson})" 
                                    class="p-1.5 bg-secondary/10 text-secondary rounded-lg hover:bg-secondary hover:text-white transition-all transform active:scale-90" productID="${product.id}">
                                <span class="material-symbols-outlined text-sm">add</span>
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
            product.lote_number = "";
            productsAdded.unshift(product);
        }
        refreshProducts();
    }

    function refreshProducts() {
        const countSpan = document.getElementById('items-count');
        countSpan.innerText = productsAdded.length;

        let results = productsAdded.map((product, index) => {
            return `<tr class="hover:bg-surface-container/20 transition-colors">
                <td class="px-8 py-4">
                    <input type="hidden" name="products[${index}][productID]" value="${product.id}">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="cancelProduct(${product.id})" class="text-on-surface-variant/40 hover:text-error transition-colors">
                            <span class="material-symbols-outlined text-lg">cancel</span>
                        </button>
                        <div>
                            <p class="font-bold text-primary leading-tight">${product.name}</p>
                            <p class="text-[10px] text-on-surface-variant font-mono">${product.barcode || '---'}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <input class="w-full bg-surface-container-low border-none rounded-xl py-2 px-3 text-sm text-center focus:ring-1 focus:ring-primary/20 font-mono" 
                           required type="number" oninput="refreshData(${product.id}, 'quantity', this)" min="1" 
                           name="products[${index}][quantity]" value="${product.quantity}">
                </td>
                <td class="px-4 py-4">
                    <input class="w-full bg-surface-container-low border-none rounded-xl py-2 px-3 text-sm text-center focus:ring-1 focus:ring-secondary/20 font-bold text-secondary" 
                           required type="number" step="0.01" min="0" oninput="refreshData(${product.id}, 'cost', this)" 
                           name="products[${index}][cost]" value="${product.cost}">
                </td>
                <td class="px-4 py-4">
                    <input class="w-full bg-surface-container-low border-none rounded-xl py-2 px-3 text-sm text-center focus:ring-1 focus:ring-primary/20 font-mono" 
                           required type="text" oninput="refreshData(${product.id}, 'lote_number', this)" 
                           name="products[${index}][lote_number]" value="${product.lote_number}">
                </td>
                <td class="py-5 px-2">
                    <input class="w-full bg-surface-container-low border-none rounded-lg py-2 text-[10px] focus:ring-2 focus:ring-primary/20 font-black text-primary uppercase" 
                           type="date" oninput="refreshData(${product.id}, 'date', this)" 
                           value="${product.date}" name="products[${index}][expiredDate]">
                </td>
            </tr>`;
        }).join('');
        document.getElementById('added-products').innerHTML = results;
    }

    function refreshData(productID, type, element) {
        const product = productsAdded.find(p => p.id == productID);
        if (product) product[type === 'date' ? 'date' : type] = element.value;
    }

    function cancelProduct(productID) {
        const index = productsAdded.findIndex(p => p.id === productID);
        if (index !== -1) productsAdded.splice(index, 1);
        refreshProducts();
    }
</script>
@endsection
