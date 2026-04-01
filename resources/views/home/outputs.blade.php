@extends('layout.app')

@section('content')
<!-- Outputs Header Section -->
<section class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2 font-headline">Registro de Salidas</h1>
        <p class="text-on-surface-variant font-body">Historial de ventas y movimientos de egreso de mercancía.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
        <a href="{{ route('outputs.create') }}" class="bg-primary hover:bg-primary-container text-white px-6 py-3 rounded-full flex items-center justify-center gap-2 transition-all font-headline font-bold shadow-lg shadow-primary/20 transform active:scale-95">
            <span class="material-symbols-outlined text-lg">sell</span>
            Nueva Salida
        </a>
    </div>
</section>

<!-- Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-surface-container-low p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Total Ventas</p>
            <p class="text-3xl font-black font-headline text-primary">{{ number_format($outputs->sum('total_sold'), 2) }}$</p>
        </div>
        <div class="bg-primary/5 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-primary text-3xl">shopping_cart_checkout</span>
        </div>
    </div>
    <div class="bg-secondary-container/20 p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-secondary-container mb-1">Utilidad Neta</p>
            <p class="text-3xl font-black font-headline text-secondary">{{ number_format($outputs->sum('total_profit'), 2) }}$</p>
        </div>
        <div class="bg-secondary/10 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-secondary text-3xl">trending_up</span>
        </div>
    </div>
    <div class="bg-tertiary-fixed/20 p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-tertiary-fixed-variant mb-1">Ítems Egresados</p>
            <p class="text-3xl font-black font-headline text-tertiary">{{ number_format($outputs->sum('quantity_products'), 0) }}</p>
        </div>
        <div class="bg-tertiary/5 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-tertiary text-3xl">outbox</span>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="bg-surface-container-lowest rounded-[2.5rem] p-6 shadow-sm border border-outline-variant/30 mb-6">
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline/40">search</span>
            <input type="text" id="search-input" value="{{ $search ?? '' }}" placeholder="Buscar por producto, código de barras o cliente..." 
                   class="w-full pl-12 pr-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
        </div>
        <button type="button" id="btn-limpiar" class="px-6 py-3 bg-surface-container text-on-surface-variant rounded-2xl font-black text-sm hover:bg-surface-container-high transition-all flex items-center justify-center gap-2 {{ !$search ? 'hidden' : '' }}">
            <span class="material-symbols-outlined text-lg">close</span>
            Limpiar
        </button>
    </div>
</div>

<!-- Outputs Table Canvas -->
<div class="bg-surface-container-lowest rounded-[2.5rem] overflow-hidden shadow-sm border border-outline-variant/30">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-[10px] uppercase tracking-[0.2em] font-black">
                    <th class="px-8 py-5 text-center">Detalle</th>
                    <th class="px-4 py-5">Fecha de Venta</th>
                    <th class="px-4 py-5">Cliente</th>
                    <th class="px-4 py-5 text-center">Productos</th>
                    <th class="px-4 py-5 text-right">Total Venta</th>
                    <th class="px-8 py-5 text-right">Factura</th>
                </tr>
            </thead>
            <tbody id="outputs-table-body" class="text-sm font-body divide-y divide-surface-container">
                @foreach ($outputs as $output)
                <tr class="hover:bg-surface-container/30 transition-all group">
                    <td class="px-8 py-5 text-center">
                        <button type="button" onclick="showDetailOutput(this)" output="{{ $output->id }}" 
                                class="p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                    </td>
                    <td class="px-4 py-5">
                        <div class="flex flex-col">
                            <span class="font-black text-primary">{{ $output->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-outline font-mono">{{ $output->created_at->format('g:i A') }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-5">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-outline/40">person</span>
                            <span class="font-bold text-on-surface-variant">
                                {{ $output->client->name ?? ($output->client_name ?? 'Cliente Genérico') }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-5 text-center">
                        <span class="px-3 py-1 bg-surface-container rounded-full text-xs font-black text-primary">
                            {{ $output->quantity_products }} uds.
                        </span>
                    </td>
                    <td class="px-4 py-5 text-right">
                        <span class="text-base font-black text-primary">
                            {{ number_format($output->total_sold, 2) }}$
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <a href="{{ route('invoice', ['outputID' => $output->id]) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-secondary-container/30 text-secondary rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-secondary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                            PDF
                        </a>
                    </td>
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-surface-container-low flex flex-col sm:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant border-t border-outline-variant/20" id="pagination-container">
        <p>Mostrando {{ $outputs->firstItem() ?? 0 }} - {{ $outputs->lastItem() ?? 0 }} de {{ $outputs->total() }} salidas</p>
        
        <div class="flex items-center gap-1">
            <a href="{{ $outputs->url(1) }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-surface-container transition-colors {{ $outputs->onFirstPage() ? 'opacity-30 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_left</span>
            </a>
            @foreach ($outputs->getUrlRange(max(1, $outputs->currentPage() - 2), min($outputs->lastPage(), $outputs->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all {{ $page == $outputs->currentPage() ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-110' : 'hover:bg-surface-container' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $outputs->url($outputs->lastPage()) }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-surface-container transition-colors {{ !$outputs->hasMorePages() ? 'opacity-30 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_right</span>
            </a>
        </div>
    </div>
</div>

<!-- Modal Detalle de Salida -->
<div id="modalScrollable" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-primary/60 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>

    <div class="relative w-full max-w-5xl bg-surface rounded-[3rem] shadow-2xl border border-outline-variant/30 overflow-hidden flex flex-col max-h-[85vh]">
        
        <div class="px-10 py-8 bg-surface-container-lowest border-b border-outline-variant/20 flex flex-col sm:flex-row justify-between items-center gap-4 shrink-0">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-primary-fixed rounded-2xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-4xl">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-primary font-headline" id="modalTitle">Detalle de Venta</h3>
                    <p id="modal-client-info" class="text-[10px] font-black text-outline uppercase tracking-[0.2em]"></p>
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
                            <th class="py-5 pl-10">Producto</th>
                            <th class="py-5 text-center">Cant.</th>
                            <th class="py-5 text-right">Precio Venta</th>
                            <th class="py-5 text-center">Venc. Lote</th>
                            <th class="py-5 pr-10 text-right">Ganancia</th>
                        </tr>
                    </thead>
                    <tbody id="outputs-details" class="text-sm font-body divide-y divide-surface-container-high/30">
                        <!-- JS Injection -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-10 py-8 bg-surface-container-lowest border-t border-outline-variant/20 shrink-0 flex justify-between items-center">
            <div class="flex gap-3">
                <button type="button" id="delete-btn" onclick="deleteOutput(this)" data-outputID="" 
                        class="flex items-center gap-2 px-6 py-3 rounded-2xl border-2 border-error/20 text-error font-black hover:bg-error hover:text-white transition-all transform active:scale-95 text-xs uppercase tracking-widest">
                    <span class="material-symbols-outlined text-lg">delete_forever</span>
                    Eliminar Venta
                </button>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-outline uppercase tracking-widest leading-none mb-1">Monto de la Venta</p>
                <p id="modal-total-sold" class="text-3xl font-black text-primary font-headline">0.00$</p>
            </div>
        </div>
    </div>
</div>

<form action="" id="actions-form-delete" class="hidden" method="POST">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const btnLimpiar = document.getElementById('btn-limpiar');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(this.value);
        }, 300);
    });

    btnLimpiar.addEventListener('click', function() {
        searchInput.value = '';
        performSearch('');
        btnLimpiar.classList.add('hidden');
    });
});

function performSearch(query) {
    const tbody = document.getElementById('outputs-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    const btnLimpiar = document.getElementById('btn-limpiar');
    
    if (query.length > 0) {
        btnLimpiar.classList.remove('hidden');
    }

    fetch(`/home/salidas/search?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
    })
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = renderOutputs(data.outputs);
        if (paginationContainer) {
            paginationContainer.style.display = 'none';
        }
    })
    .catch(error => console.error(error));
}

function renderOutputs(outputs) {
    if (outputs.length === 0) {
        return `<tr><td colspan="6" class="px-8 py-12 text-center text-outline">No se encontraron resultados</td></tr>`;
    }
    
    return outputs.map(output => `
        <tr class="hover:bg-surface-container/30 transition-all group">
            <td class="px-8 py-5 text-center">
                <button type="button" onclick="showDetailOutput(this)" output="${output.id}" 
                        class="p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary hover:text-white transition-all">
                    <span class="material-symbols-outlined text-xl">visibility</span>
                </button>
            </td>
            <td class="px-4 py-5">
                <div class="flex flex-col">
                    <span class="font-black text-primary">${formatDate(output.created_at)}</span>
                    <span class="text-[10px] text-outline font-mono">${formatTime(output.created_at)}</span>
                </div>
            </td>
            <td class="px-4 py-5">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-outline/40">person</span>
                    <span class="font-bold text-on-surface-variant">
                        ${output.client ? output.client.name : (output.client_name || 'Cliente Genérico')}
                    </span>
                </div>
            </td>
            <td class="px-4 py-5 text-center">
                <span class="px-3 py-1 bg-surface-container rounded-full text-xs font-black text-primary">
                    ${output.quantity_products} uds.
                </span>
            </td>
            <td class="px-4 py-5 text-right">
                <span class="text-base font-black text-primary">
                    ${parseFloat(output.total_sold).toFixed(2)}$
                </span>
            </td>
            <td class="px-8 py-5 text-right">
                <a href="/home/invoice/${output.id}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-secondary-container/30 text-secondary rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-secondary hover:text-white transition-all">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    PDF
                </a>
            </td>
        </tr>
    `).join('');
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function formatTime(dateStr) {
    const date = new Date(dateStr);
    let hours = date.getHours();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes} ${ampm}`;
}

function showDetailOutput($btn) {
    let outputID = $btn.getAttribute('output');
    const modal = document.getElementById('modalScrollable');
    
    fetch(`/home/salidas/${outputID}`, {
        method: 'GET',
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
    })
    .then(response => response.json())
    .then(data => {
        buildModal(data.outputs);
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

function buildModal($outputs) {
    let tableBody = document.getElementById('outputs-details');
    let deleteBtn = document.getElementById('delete-btn');
    let totalSoldLabel = document.getElementById('modal-total-sold');
    let clientInfo = document.getElementById('modal-client-info');

    deleteBtn.setAttribute('data-outputID', $outputs[0].output_general_id);
    
    let total = 0;
    let results = $outputs.map(output => {
        total += parseFloat(output.product.sell_price * output.quantity);
        return `<tr class="hover:bg-white transition-colors">
                    <td class="py-5 pl-10">
                        <div class="flex flex-col">
                            <span class="font-black text-primary text-base">${output.product.name}</span>
                            <span class="text-[10px] font-mono text-outline/60 uppercase tracking-tighter">Lote: ${output.inventory.lote_number || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="py-5 text-center">
                        <span class="px-3 py-1 bg-primary-fixed text-primary rounded-lg font-black text-sm">${output.quantity}</span>
                    </td>
                    <td class="py-5 text-right font-bold text-primary/80">
                        ${parseFloat(output.product.sell_price).toFixed(2)}$
                    </td>
                    <td class="py-5 text-center">
                        <span class="text-xs font-bold text-outline">${output.expired_date || '---'}</span>
                    </td>
                    <td class="py-5 pr-10 text-right font-black text-secondary">
                        +${parseFloat(output.profit).toFixed(2)}$
                    </td>
                </tr>`;
    }).join('');

    tableBody.innerHTML = results;
    totalSoldLabel.innerHTML = total.toFixed(2) + '$';
    clientInfo.innerHTML = "Detalle de transaccion registrada";
}

function deleteOutput($element) {
    const outputID = $element.getAttribute('data-outputID');
    if (confirm('¿Está seguro de eliminar esta salida? El stock se devolverá automáticamente.')) {
        const form = document.getElementById('actions-form-delete'); 
        form.action = `/home/salidas/${outputID}`; 
        form.submit();
    }
}
</script>
@endsection
