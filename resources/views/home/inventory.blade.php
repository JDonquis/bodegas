@extends('layout.app')

@section('content')
<!-- Inventory Header Section -->
<section class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2 font-headline">Estado del Inventario</h1>
        <p class="text-on-surface-variant font-body">Monitoreo global de existencias y rendimiento financiero.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
        <div class="relative flex-1 sm:w-80 group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
            <input class="w-full bg-surface-container-low border-none rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/60 font-body transition-all" 
                   placeholder="Buscar por producto o código..." 
                   type="text" 
                   id="inventory-search">
        </div>
    </div>
</section>

<!-- Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-surface-container-low p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Inversión Total</p>
            <p class="text-3xl font-black font-headline text-primary">{{ number_format($inventories->sum('expense'), 2) }}$</p>
        </div>
        <div class="bg-primary/5 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-primary text-3xl">account_balance</span>
        </div>
    </div>
    <div class="bg-secondary-container/20 p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-secondary-container mb-1">Ganancias Acum.</p>
            <p class="text-3xl font-black font-headline text-secondary">{{ number_format($inventories->sum('profits'), 2) }}$</p>
        </div>
        <div class="bg-secondary/10 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-secondary text-3xl">payments</span>
        </div>
    </div>
    <div class="bg-tertiary-fixed/20 p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-tertiary-fixed-variant mb-1">Stock Global</p>
            <p class="text-3xl font-black font-headline text-tertiary">{{ number_format($inventories->sum('stock'), 0) }} <span class="text-xs">uds.</span></p>
        </div>
        <div class="bg-tertiary/5 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-tertiary text-3xl">analytics</span>
        </div>
    </div>
</div>

<!-- Inventory Table Canvas -->
<div class="bg-surface-container-lowest rounded-[2.5rem] overflow-hidden shadow-sm border border-outline-variant/30">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-[10px] uppercase tracking-[0.2em] font-black">
                    <th class="px-8 py-5">Producto</th>
                    <th class="px-4 py-5 text-center">Stock Actual</th>
                    <th class="px-4 py-5 text-center">Entradas/Salidas</th>
                    <th class="px-4 py-5 text-right">Inversión</th>
                    <th class="px-4 py-5 text-right">Vendido</th>
                    <th class="px-8 py-5 text-right">Ganancia</th>
                </tr>
            </thead>
            <tbody id="inventory-table-body" class="text-sm font-body divide-y divide-surface-container">
                @foreach ($inventories as $inventory)
                <tr class="hover:bg-surface-container/30 transition-all group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <button type="button" onclick="showDetail(this)" inventory="{{ $inventory->id }}" 
                                    class="p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary hover:text-white transition-all">
                                <span class="material-symbols-outlined text-xl">layers</span>
                            </button>
                            <div class="flex flex-col">
                                <span class="font-black text-primary text-base leading-none">{{ $inventory->product->name }}</span>
                                <span class="text-[9px] text-outline font-mono mt-1 uppercase tracking-tighter">{{ $inventory->product->barcode ?? 'SIN SKU' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-5 text-center">
                        <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-black {{ $inventory->stock <= 5 ? 'bg-error-container text-error' : 'bg-secondary-container text-secondary' }}">
                            {{ $inventory->stock }} {{ $inventory->product->sale_type === 'weight' ? 'g' : 'uds' }}
                        </span>
                    </td>
                    <td class="px-4 py-5 text-center">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-[10px] font-bold text-outline uppercase tracking-tighter">
                                <span class="text-primary">{{ $inventory->entries }}</span> / <span class="text-error">{{ $inventory->outputs }}</span> {{ $inventory->product->sale_type === 'weight' ? 'g' : 'uds' }}
                            </span>
                            <div class="w-16 h-1 bg-surface-container rounded-full overflow-hidden flex">
                                @php 
                                    $total = $inventory->entries ?: 1;
                                    $percent = ($inventory->outputs / $total) * 100;
                                @endphp
                                <div class="h-full bg-error" style="width: {{ $percent }}%"></div>
                                <div class="h-full bg-primary" style="width: {{ 100 - $percent }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-5 text-right font-bold text-primary/60">
                        {{ number_format($inventory->expense, 2) }}$
                    </td>
                    <td class="px-4 py-5 text-right font-bold text-primary/80">
                        {{ number_format($inventory->sold, 2) }}$
                    </td>
                    <td class="px-8 py-5 text-right">
                        <span class="text-lg font-black text-secondary font-headline">
                            {{ number_format($inventory->profits, 2) }}$
                        </span>
                    </td>
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-surface-container-low flex flex-col sm:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant">
        <p>Total: {{ $inventories->total() }} productos registrados</p>
        
        <div class="flex items-center gap-1">
            <a href="{{ $inventories->url(1) }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-surface-container transition-colors {{ $inventories->onFirstPage() ? 'opacity-30 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_left</span>
            </a>
            @foreach ($inventories->getUrlRange(max(1, $inventories->currentPage() - 2), min($inventories->lastPage(), $inventories->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all {{ $page == $inventories->currentPage() ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-110' : 'hover:bg-surface-container' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $inventories->url($inventories->lastPage()) }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-surface-container transition-colors {{ !$inventories->hasMorePages() ? 'opacity-30 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_right</span>
            </a>
        </div>
    </div>
</div>

<!-- Modal Detalle de Lotes -->
<div id="modalScrollable" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-primary/60 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>

    <div class="relative w-full max-w-5xl bg-surface rounded-[3rem] shadow-2xl border border-outline-variant/30 overflow-hidden flex flex-col max-h-[85vh]">
        
        <div class="px-10 py-8 bg-surface-container-lowest border-b border-outline-variant/20 flex flex-col sm:flex-row justify-between items-center gap-4 shrink-0">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-secondary-container rounded-2xl flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-4xl">inventory</span>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-primary font-headline" id="modal-product-name">Desglose de Lotes</h3>
                    <p class="text-[10px] font-black text-outline uppercase tracking-[0.2em]">Existencias actuales por vencimiento</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" class="p-3 hover:bg-surface-container-high rounded-full transition-colors text-outline">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <div class="px-10 py-8 overflow-y-auto flex-1 bg-surface">
            <div class="rounded-3xl border border-outline-variant/10 overflow-hidden bg-surface-container-low/30">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-surface-container-low z-10">
                        <tr class="text-[10px] font-black text-outline uppercase tracking-[0.2em]">
                            <th class="py-5 pl-10">Número de Lote</th>
                            <th class="py-5 text-center">Stock</th>
                            <th class="py-5 text-right">Inversión</th>
                            <th class="py-5 pr-10 text-right uppercase">Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-details" class="text-sm font-body divide-y divide-surface-container-high/30">
                        <!-- Inyectado por JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-10 py-8 bg-surface-container-lowest border-t border-outline-variant/20 shrink-0 text-center sm:text-right">
            <button type="button" onclick="closeModal()" class="px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">
                Entendido, cerrar
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let searchTimeout = null;

document.getElementById('inventory-search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value;
    
    searchTimeout = setTimeout(() => {
        if (query.length < 1) {
            window.location.reload();
            return;
        }

        fetch(`/home/inventario/search/${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('inventory-table-body');
                if (data.inventories.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="py-20 text-center text-outline/40 font-black uppercase tracking-widest">No se encontraron resultados</td></tr>';
                    return;
                }

                tbody.innerHTML = data.inventories.map(inv => {
                    const total = inv.entries || 1;
                    const percent = (inv.outputs / total) * 100;
                    return `
                        <tr class="hover:bg-surface-container/30 transition-all group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="showDetail(this)" inventory="${inv.id}" 
                                            class="p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-xl">layers</span>
                                    </button>
                                    <div class="flex flex-col">
                                        <span class="font-black text-primary text-base leading-none">${inv.product.name}</span>
                                        <span class="text-[9px] text-outline font-mono mt-1 uppercase tracking-tighter">${inv.product.barcode || 'SIN SKU'}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-black ${inv.stock <= 5 ? 'bg-error-container text-error' : 'bg-secondary-container text-secondary'}">
                                    ${inv.stock}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-[10px] font-bold text-outline uppercase tracking-tighter">
                                        <span class="text-primary">${inv.entries}</span> / <span class="text-error">${inv.outputs}</span>
                                    </span>
                                    <div class="w-16 h-1 bg-surface-container rounded-full overflow-hidden flex">
                                        <div class="h-full bg-error" style="width: ${percent}%"></div>
                                        <div class="h-full bg-primary" style="width: ${100 - percent}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-right font-bold text-primary/60">${parseFloat(inv.expense).toFixed(2)}$</td>
                            <td class="px-4 py-5 text-right font-bold text-primary/80">${parseFloat(inv.sold).toFixed(2)}$</td>
                            <td class="px-8 py-5 text-right">
                                <span class="text-lg font-black text-secondary font-headline">${parseFloat(inv.profits).toFixed(2)}$</span>
                            </td>
                        </tr>`;
                }).join('');
            });
    }, 400);
});

function showDetail($btn){
    let inventoryID = $btn.getAttribute('inventory');
    const modal = document.getElementById('modalScrollable');

    fetch(`/home/inventario/${inventoryID}`, {
        method: 'GET',
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('modal-product-name').innerText = data.details[0]?.product.name || 'Detalle de Lotes';
        buildModal(data.details);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    })
    .catch(error => console.error(error));
}

function closeModal() {
    const modal = document.getElementById('modalScrollable');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function formatDate(dateString) {
    if (!dateString) return '<span class="opacity-20">SIN FECHA</span>';
    const date = new Date(dateString);
    const now = new Date();
    const threeMonths = new Date();
    threeMonths.setMonth(now.getMonth() + 3);

    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    let colorClass = 'text-outline';
    
    if (date < now) colorClass = 'text-error font-black';
    else if (date < threeMonths) colorClass = 'text-tertiary font-black';

    return `<span class="${colorClass}">${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}</span>`;
}

function buildModal($details){
    let tableBody = document.getElementById('inventory-details');
    
    if ($details.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="4" class="py-20 text-center text-outline/40 font-black uppercase tracking-widest">Sin stock disponible en lotes</td></tr>';
        return;
    }

    tableBody.innerHTML = $details.map(detail => {
        return `<tr class="hover:bg-white transition-colors">
                    <td class="py-6 pl-10">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-outline/40">label</span>
                            <span class="font-mono text-primary font-black tracking-tighter uppercase">${detail.lote_number || 'S/L'}</span>
                        </div>
                    </td>
                    <td class="py-6 text-center">
                        <span class="px-4 py-1.5 bg-primary-fixed text-primary rounded-xl font-black text-sm">${detail.stock} ${detail.product.sale_type === 'weight' ? 'g' : 'uds'}</span>
                    </td>
                    <td class="py-6 text-right font-bold text-primary/60">${parseFloat(detail.cost).toFixed(2)}$</td>
                    <td class="py-6 pr-10 text-right text-xs font-bold uppercase tracking-tight">
                        ${formatDate(detail.expired_date)}
                    </td>
                </tr>`;
    }).join('');
}
</script>
@endsection
