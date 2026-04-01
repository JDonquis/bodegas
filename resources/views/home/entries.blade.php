@extends('layout.app')

@section('content')
<!-- Entries Header Section -->
<section class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2 font-headline">Entradas de Inventario</h1>
        <p class="text-on-surface-variant font-body">Registro histórico de ingresos de mercancía a la bodega.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
        <a href="{{ route('entries.create') }}" class="bg-primary hover:bg-primary-container text-white px-6 py-3 rounded-full flex items-center justify-center gap-2 transition-all font-headline font-bold shadow-lg shadow-primary/20 transform active:scale-95">
            <span class="material-symbols-outlined text-lg">add_circle</span>
            Nueva Entrada
        </a>
    </div>
</section>

<!-- Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-surface-container-low p-6 rounded-xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-1">Total Registros</p>
            <p class="text-3xl font-headline font-extrabold text-primary">{{ $entries->total() }}</p>
        </div>
        <div class="bg-primary/5 p-3 rounded-lg">
            <span class="material-symbols-outlined text-primary text-3xl">history</span>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/30 mb-6">
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline/40">search</span>
            <input type="text" id="search-input" value="{{ $search ?? '' }}" placeholder="Buscar por producto o código de barras..." 
                   class="w-full pl-12 pr-4 py-3 bg-surface rounded-xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
        </div>
        <button type="button" id="btn-limpiar" class="px-6 py-3 bg-surface-container text-on-surface-variant rounded-xl font-bold text-sm hover:bg-surface-container-high transition-all flex items-center justify-center gap-2 {{ !$search ? 'hidden' : '' }}">
            <span class="material-symbols-outlined text-lg">close</span>
            Limpiar
        </button>
    </div>
</div>

<!-- Entries Table Canvas -->
<div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant/30">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider font-bold">
                    <th class="px-6 py-4">Detalle</th>
                    <th class="px-6 py-4">Fecha de Registro</th>
                    <th class="px-6 py-4 text-center">Nro. Productos</th>
                    <th class="px-6 py-4 text-right">Costo Total</th>
                </tr>
            </thead>
            <tbody id="entries-table-body" class="text-sm font-body divide-y divide-surface-container">
                @php
                    Carbon\Carbon::setLocale('es');
                @endphp
                @foreach ($entries as $entry)
                <tr class="hover:bg-surface-container/30 transition-colors group">
                    <td class="px-6 py-4">
                        <button type="button" onclick="showDetailEntry(this)" entry="{{ $entry->id }}" 
                                class="p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary hover:text-white transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                    </td>
                    <td class="px-6 py-4 font-bold text-primary">
                        {{ ucfirst($entry->created_at->translatedFormat('F j, Y')) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-surface-container text-primary uppercase tracking-tighter">
                            {{ $entry->quantity_products }} productos
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-black text-secondary text-base">
                        {{ number_format($entry->total_expense, 2) }}$
                    </td>
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Table Footer Pagination -->
    <div class="px-6 py-4 bg-surface-container-low flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-medium text-on-surface-variant" id="pagination-container">
        <p>Mostrando {{ $entries->firstItem() }} a {{ $entries->lastItem() }} de {{ $entries->total() }} registros</p>
        
        <div class="flex items-center gap-1">
            <a href="{{ $entries->url(1) }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ $entries->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_left</span>
            </a>
            <a href="{{ $entries->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ $entries->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </a>
            @foreach ($entries->getUrlRange(max(1, $entries->currentPage() - 2), min($entries->lastPage(), $entries->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $page == $entries->currentPage() ? 'bg-primary text-white font-bold' : 'hover:bg-surface-container' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $entries->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ !$entries->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </a>
            <a href="{{ $entries->url($entries->lastPage()) }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ !$entries->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_right</span>
            </a>
        </div>
    </div>
</div>

<!-- Modal de Detalle (Rediseñado y Corregido) -->
<div id="modalScrollable" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    <!-- Overlay con efecto Blur -->
    <div class="fixed inset-0 bg-primary/60 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>

    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <!-- Contenido del Modal -->
        <div class="relative w-full max-w-6xl bg-surface rounded-[2.5rem] shadow-2xl shadow-black/40 border border-outline-variant/30 transform transition-all overflow-hidden flex flex-col max-h-[90vh]">            
            <!-- Header del Modal -->
            <div class="px-8 py-6 bg-surface-container-lowest border-b border-outline-variant/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-fixed rounded-2xl flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-3xl">receipt_long</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-primary font-headline" id="modalTitle">Entrada Detallada</h3>
                        <p id="date-entry" class="text-xs font-bold text-outline uppercase tracking-widest"></p>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-outline">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>

            <!-- Cuerpo del Modal (Scrolleable) -->
            <div class="px-8 py-6 overflow-y-auto flex-1 bg-surface">
                <div class="bg-surface-container-low/50 rounded-3xl border border-outline-variant/10 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-surface-container-low z-10">
                            <tr class="text-[10px] font-black text-outline uppercase tracking-[0.2em]">
                                <th class="py-4 pl-8">Producto</th>
                                <th class="py-4 text-center">Cant.</th>
                                <th class="py-4 text-right">Costo Unit.</th>
                                <th class="py-4 text-center">Lote</th>
                                <th class="py-4 pr-8 text-right">Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody id="entries-details" class="text-sm font-body divide-y divide-surface-container-high/30">
                            <!-- Inyectado por JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="px-8 py-6 bg-surface-container-lowest border-t border-outline-variant/20 flex flex-col sm:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <button type="button" id="delete-btn" onclick="deleteEntry(this)" data-entryID="" 
                            class="flex items-center gap-2 px-6 py-3 rounded-2xl border-2 border-error/20 text-error font-black hover:bg-error hover:text-white transition-all transform active:scale-95 group">
                        <span class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">delete_forever</span>
                        Eliminar Registro
                    </button>
                    <button type="button" id="update-btn" onclick="updateEntry(this)" data-entryID="" 
                            class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-primary text-white font-black hover:bg-primary-container shadow-lg shadow-primary/20 transition-all transform active:scale-95">
                        <span class="material-symbols-outlined text-xl">edit_square</span>
                        Editar
                    </button>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-outline uppercase tracking-widest leading-none mb-1">Total de la Operación</p>
                    <p id="modal-total-expense" class="text-3xl font-black text-secondary font-headline">0.00$</p>
                </div>
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
    const tbody = document.getElementById('entries-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    const btnLimpiar = document.getElementById('btn-limpiar');
    
    if (query.length > 0) {
        btnLimpiar.classList.remove('hidden');
    }

    fetch(`/home/entradas/search?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
    })
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = renderEntries(data.entries);
        if (paginationContainer) {
            paginationContainer.style.display = 'none';
        }
    })
    .catch(error => console.error(error));
}

function renderEntries(entries) {
    if (entries.length === 0) {
        return `<tr><td colspan="4" class="px-6 py-12 text-center text-outline">No se encontraron resultados</td></tr>`;
    }
    
    return entries.map(entry => {
        const date = new Date(entry.created_at);
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const formattedDate = `${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        
        return `<tr class="hover:bg-surface-container/30 transition-colors group">
            <td class="px-6 py-4">
                <button type="button" onclick="showDetailEntry(this)" entry="${entry.id}" 
                        class="p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary hover:text-white transition-all flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">visibility</span>
                </button>
            </td>
            <td class="px-6 py-4 font-bold text-primary">
                ${formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1)}
            </td>
            <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-surface-container text-primary uppercase tracking-tighter">
                    ${entry.quantity_products} productos
                </span>
            </td>
            <td class="px-6 py-4 text-right font-black text-secondary text-base">
                ${parseFloat(entry.total_expense).toFixed(2)}$
            </td>
        </tr>`;
    }).join('');
}

function showDetailEntry($btn) {
    let entryID = $btn.getAttribute('entry');
    const modal = document.getElementById('modalScrollable');
    
    fetch(`/home/entradas/${entryID}`, {
        method: 'GET',
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
    })
    .then(response => response.json())
    .then(data => {
        buildModal(data.entries);
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
    if (!dateString) return '---';
    const date = new Date(dateString);
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    return `${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
}

function buildModal($entries) {
    let dateEntry = document.getElementById('date-entry');
    let tableBody = document.getElementById('entries-details');
    let deleteBtn = document.getElementById('delete-btn');
    let updateBtn = document.getElementById('update-btn');
    let totalExpenseLabel = document.getElementById('modal-total-expense');

    dateEntry.innerHTML = $entries[0].created_at;
    deleteBtn.setAttribute('data-entryID', $entries[0].entry_general_id);
    updateBtn.setAttribute('data-entryID', $entries[0].entry_general_id);

    let total = 0;
    let results = $entries.map(entry => {
        total += parseFloat(entry.cost);
        const costPerUnit = (parseFloat(entry.cost) / parseInt(entry.quantity)).toFixed(2);
        
        return `<tr class="hover:bg-white transition-colors">
                    <td class="py-5 pl-8">
                        <div class="flex flex-col">
                            <span class="font-black text-primary text-base">${entry.product.name}</span>
                            <span class="text-[10px] font-mono text-outline/60 uppercase tracking-tighter">${entry.product.barcode || 'SIN SKU'}</span>
                        </div>
                    </td>
                    <td class="py-5 text-center">
                        <span class="px-3 py-1 bg-surface-container-high rounded-lg font-black text-primary text-sm">${entry.quantity}</span>
                    </td>
                    <td class="py-5 text-right font-bold text-primary/80">
                        <span class="text-xs text-outline font-medium mr-1">@</span>${costPerUnit}$
                    </td>
                    <td class="py-5 text-center">
                        <span class="font-mono text-xs text-on-surface-variant bg-surface-container px-2 py-0.5 rounded-full border border-outline-variant/20">${entry.lote_number || '---'}</span>
                    </td>
                    <td class="py-5 pr-8 text-right text-xs font-bold text-outline">
                        ${formatDate(entry.expired_date)}
                    </td>
                </tr>`;
    }).join('');

    tableBody.innerHTML = results;
    totalExpenseLabel.innerHTML = total.toFixed(2) + '$';
}

function deleteEntry($element) {
    const entryID = $element.getAttribute('data-entryID');
    if (confirm('¿Está seguro de eliminar esta entrada? El stock se ajustará automáticamente.')) {
        const form = document.getElementById('actions-form-delete'); 
        form.action = `/home/entradas/${entryID}`; 
        form.submit();
    }
}

function updateEntry($element) {
    const entryID = $element.getAttribute('data-entryID');
    window.location.href = `/home/entradas/editar/${entryID}`;
}
</script>
@endsection
