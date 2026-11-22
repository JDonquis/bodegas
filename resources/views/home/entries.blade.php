@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #detail-sheet {
            transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #detail-backdrop {
            transition: opacity 0.3s ease-out;
        }
    </style>
@endsection

@section('content')
    @php
        Carbon\Carbon::setLocale('es');
    @endphp

    <div class="mx-auto flex w-full max-w-md flex-col gap-4 font-jakarta">

        <section class="px-1">
            <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight leading-tight">
                Compras de <br><span class="text-brand-navy">Inventario</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium mt-1 leading-relaxed">
                Registro histórico de ingresos de mercancía y reposición a la bodega.
            </p>

            <a href="{{ route('entries.create') }}"
                class="mt-4 w-full py-3.5 px-4 bg-brand-navy active:bg-[#071424] hover:shadow-lg text-white font-bold text-sm rounded-2xl flex items-center justify-center gap-2 shadow-brand-btn transition-transform active:scale-[0.98]">
                <span class="material-symbols-outlined text-[20px] text-brand-accent">add_circle</span>
                <span>Nueva Compra</span>
            </a>
        </section>

        <section class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl p-3.5 shadow-brand-card border border-brand-border flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1">
                        <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Total Registros</p>
                        <span
                            class="shrink-0 whitespace-nowrap text-[10px] font-semibold text-brand-accent bg-emerald-50 px-2 py-1 leading-none rounded-full border border-emerald-100">+{{ $todayEntries }}
                            hoy</span>
                    </div>
                    <div class="flex items-baseline gap-1.5 mt-1.5">
                        <span
                            class="text-2xl font-extrabold text-brand-navy tracking-tight">{{ number_format($entries->total()) }}</span>
                        <span class="text-[11px] font-medium text-gray-400">compras</span>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100 flex items-center gap-1 text-[10px] text-gray-500">
                    <span class="material-symbols-outlined text-[14px] text-brand-accent">check</span>
                    <span class="truncate">100% inventariadas</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-3.5 shadow-brand-card border border-brand-border flex flex-col justify-between">
                <div class="flex items-start justify-between gap-1">
                    <div>
                        <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Inversión Mes</p>
                        <div class="flex items-baseline gap-1 mt-1.5">
                            <span
                                class="text-2xl font-extrabold text-brand-navy tracking-tight">${{ number_format($monthInvestment, 2) }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="window.location.reload()" aria-label="Actualizar registros"
                        class="w-8 h-8 rounded-xl bg-[#f0f4ff] flex items-center justify-center text-[#2b5fd9] hover:bg-[#e2ecff] active:scale-95 transition-colors flex-shrink-0">
                        <span class="material-symbols-outlined text-[16px]">refresh</span>
                    </button>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100 flex items-center gap-1 text-[10px] text-gray-500 font-mono">
                    <span class="text-gray-400 font-sans">Aprox:</span>
                    <span class="font-bold text-brand-navy">Bs. {{ number_format($monthInvestmentBs, 2) }}</span>
                </div>
            </div>
        </section>

        <section>
            <div
                class="relative bg-white rounded-2xl border border-brand-border shadow-sm flex items-center px-3.5 py-1 focus-within:ring-2 focus-within:ring-brand-accent/30 focus-within:border-brand-accent transition-all">
                <span class="material-symbols-outlined text-[18px] text-gray-400 mr-2 flex-shrink-0">search</span>
                <input type="text" id="search-input" value="{{ $search ?? '' }}"
                    placeholder="Buscar por producto o código de barras..."
                    class="w-full text-xs font-medium text-brand-navy placeholder-gray-400 bg-transparent border-0 focus:ring-0 p-1.5 leading-snug">
                <button type="button" id="btn-limpiar"
                    class="{{ $search ? '' : 'hidden' }} p-1.5 rounded-lg text-gray-400 hover:text-brand-navy active:scale-90 transition flex-shrink-0"
                    aria-label="Limpiar búsqueda">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Historial de Ingresos</h3>
                <span class="text-[11px] font-medium text-gray-400">Fecha reciente</span>
            </div>

            <div id="entries-feed" class="space-y-3">
                @forelse ($entries as $entry)
                    <div class="bg-white rounded-2xl p-4 shadow-brand-card border border-brand-border hover:border-[#d6e4ff] cursor-pointer transition-all active:scale-[0.99] space-y-3"
                        onclick="showDetailEntry({{ $entry->id }})" role="button" tabindex="0"
                        onkeydown="if (event.key === 'Enter') showDetailEntry({{ $entry->id }})">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div
                                    class="w-9 h-9 rounded-xl bg-[#eef4ff] text-[#2454b5] flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-xs font-extrabold text-brand-navy font-mono tracking-tight">Compra
                                            #{{ $entry->id }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-emerald-50 text-brand-accent text-[9px] font-extrabold uppercase border border-emerald-100">Ingresado</span>
                                    </div>
                                    <p class="text-xs font-bold text-gray-700 mt-0.5">
                                        {{ ucfirst($entry->created_at->translatedFormat('F j, Y')) }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="text-sm font-extrabold text-brand-navy font-mono">${{ number_format($entry->total_expense, 2) }}</span>
                                <p class="text-[10px] text-gray-400 font-mono">Bs.
                                    {{ number_format($entry->total_expense * $usdRate, 2) }}</p>
                            </div>
                        </div>
                        <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-[#eaf0fc] text-brand-navy font-bold text-[10px] border border-[#d8e3fa]">
                                    <span class="material-symbols-outlined text-[14px] text-[#345ea8]">inventory_2</span>
                                    <span>{{ $entry->quantity_products }} productos
                                        ({{ $entry->entries->sum('quantity') }} uds)</span>
                                </span>
                                <span
                                    class="text-[10px] font-medium text-gray-400 truncate">{{ $entry->created_at->translatedFormat('j M · h:i A') }}</span>
                            </div>
                            <span class="flex items-center gap-1 text-[11px] font-bold text-[#2454b5] flex-shrink-0">
                                <span>Detalle</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-brand-border shadow-brand-card text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300">shopping_cart</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No hay compras registradas</p>
                        <p class="text-[11px] text-gray-400 mt-1">Registra tu primera compra para verla aquí.</p>
                    </div>
                @endforelse
            </div>

            @if ($entries->hasPages())
                <div id="pagination-container"
                    class="bg-white rounded-2xl p-3.5 border border-brand-border shadow-sm flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-gray-400">Página
                        <strong class="text-brand-navy">{{ $entries->currentPage() }}</strong> de
                        {{ $entries->lastPage() }} ({{ $entries->total() }} reg.)</p>
                    <div class="flex items-center gap-1">
                        <a href="{{ $entries->url(1) }}"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-brand-navy hover:bg-gray-100 transition {{ $entries->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                            <span class="material-symbols-outlined text-[16px]">keyboard_double_arrow_left</span>
                        </a>
                        <a href="{{ $entries->previousPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-brand-navy hover:bg-gray-100 transition {{ $entries->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        @foreach ($entries->getUrlRange(max(1, $entries->currentPage() - 1), min($entries->lastPage(), $entries->currentPage() + 1)) as $page => $url)
                            <a href="{{ $url }}"
                                class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-semibold transition {{ $page == $entries->currentPage() ? 'bg-brand-navy text-white shadow-sm shadow-brand-navy/20' : 'bg-white border border-brand-border text-gray-600 hover:bg-gray-50' }}">{{ $page }}</a>
                        @endforeach
                        <a href="{{ $entries->nextPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand-navy hover:bg-gray-100 transition {{ !$entries->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                        <a href="{{ $entries->url($entries->lastPage()) }}"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-brand-navy hover:bg-gray-100 transition {{ !$entries->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                            <span class="material-symbols-outlined text-[16px]">keyboard_double_arrow_right</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <div id="detail-backdrop"
        class="fixed inset-0 z-[60] bg-brand-navy/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"
        onclick="closeDetailSheet()"></div>

    <section id="detail-sheet"
        class="fixed inset-x-0 bottom-0 z-[70] mx-auto max-w-md bg-white rounded-t-[34px] shadow-brand-sheet border-t border-white/60 transform translate-y-full flex flex-col max-h-[92vh] overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="detail-title">
        <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeDetailSheet()">
            <div class="w-12 h-1.5 rounded-full bg-gray-300"></div>
        </div>

        <div class="px-5 pt-2 pb-3.5 border-b border-brand-border flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-2xl bg-[#eaf0fc] flex items-center justify-center text-[#2b5fd9] shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-brand-navy tracking-tight leading-tight" id="detail-title">
                        Detalle de Compra</h3>
                    <p class="text-[11px] font-semibold text-gray-500 tracking-wide mt-0.5" id="detail-subtitle">Compra
                        #---</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailSheet()" aria-label="Cerrar ventana"
                class="w-9 h-9 rounded-full bg-[#f3f5fa] hover:bg-gray-200 text-gray-500 hover:text-brand-navy flex items-center justify-center active:scale-90 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-5 py-4 overflow-y-auto space-y-3.5 no-scrollbar bg-[#f8f9ff]">
            <div class="bg-white rounded-2xl p-4 border border-brand-border shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-[#f0f4fc]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-accent"></span>
                        <div>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Registro</span>
                            <span class="font-extrabold text-xs text-brand-navy">Ingreso de mercancía</span>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-brand-accent text-[10px] font-extrabold border border-emerald-100">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        Verificado
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-0.5">
                    <div class="bg-[#f5f8ff] p-2.5 rounded-xl border border-[#e1e9fa]">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Total
                            Productos</span>
                        <span class="text-xs font-extrabold text-brand-navy mt-0.5 block" id="detail-items">0 ítems / 0
                            und</span>
                    </div>
                    <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Monto
                            Total</span>
                        <span class="text-xs font-extrabold text-brand-accent mt-0.5 block" id="detail-total">$0.00 / Bs
                            0.00</span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-[10px] text-gray-400 pt-0.5 px-0.5">
                    <span>Tasa oficial BCV referencial</span>
                    <span class="font-mono font-bold text-brand-navy" id="detail-rate">Bs. 0.00 / USD</span>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Productos
                        Registrados (<span id="detail-count">0</span>)</span>
                    <span
                        class="text-[10px] font-semibold text-brand-accent bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">Ingreso
                        confirmado</span>
                </div>
                <div id="detail-products" class="space-y-2"></div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-brand-border shadow-inner">
            <div class="grid grid-cols-2 gap-3">
                <button type="button" id="delete-btn" onclick="deleteEntry(this)" data-entryID=""
                    class="py-3 px-3 rounded-2xl border border-red-200 text-red-600 bg-red-50/50 hover:bg-red-100 active:bg-red-200 flex items-center justify-center gap-1.5 text-xs font-bold transition active:scale-95">
                    <span class="material-symbols-outlined text-[18px] text-red-500">delete</span>
                    <span>Anular Compra</span>
                </button>
                <button type="button" id="update-btn" onclick="updateEntry(this)" data-entryID=""
                    class="py-3 px-3 rounded-2xl bg-brand-navy hover:bg-[#071424] text-white flex items-center justify-center gap-1.5 text-xs font-bold transition shadow-brand-btn active:scale-95">
                    <span class="material-symbols-outlined text-[18px] text-brand-accent">edit</span>
                    <span>Editar Compra</span>
                </button>
            </div>
        </div>
    </section>

    <form action="" id="actions-form-delete" class="hidden" method="POST">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <script>
        const usdRate = @json($usdRate ?? 0);
        let searchTimeout = null;

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const clearButton = document.getElementById('btn-limpiar');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => performSearch(this.value), 300);
                });
            }

            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    clearButton.classList.add('hidden');
                    performSearch('');
                });
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeDetailSheet();
                }
            });
        });

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

        function formatFullDate(dateString) {
            const date = new Date(dateString);
            const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ];

            return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
        }

        function formatShortDateTime(dateString) {
            const date = new Date(dateString);
            const months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            const time = date.toLocaleTimeString('es-VE', {
                hour: '2-digit',
                minute: '2-digit'
            });

            return `${date.getDate()} ${months[date.getMonth()]} · ${time}`;
        }

        function formatExpiry(dateString) {
            if (!dateString) return 'Sin vencimiento';

            const date = new Date(dateString);
            const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

            return `Vence: ${date.getDate()} ${months[date.getMonth()]} ${String(date.getFullYear()).slice(-2)}`;
        }

        function performSearch(query) {
            const feed = document.getElementById('entries-feed');
            const pagination = document.getElementById('pagination-container');
            const clearButton = document.getElementById('btn-limpiar');

            if (query.length > 0) {
                clearButton.classList.remove('hidden');
            } else {
                clearButton.classList.add('hidden');
            }

            fetch(`${window.tenantBase}/home/entradas/search?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    feed.innerHTML = renderEntries(data.entries || []);

                    if (pagination) {
                        pagination.style.display = 'none';
                    }
                })
                .catch(error => console.error(error));
        }

        function renderEntries(entries) {
            if (entries.length === 0) {
                return `<div class="bg-white rounded-2xl p-8 border border-brand-border shadow-brand-card text-center">
                    <span class="material-symbols-outlined text-4xl text-gray-300">search_off</span>
                    <p class="mt-2 text-xs font-bold text-brand-navy">No se encontraron compras</p>
                    <p class="text-[11px] text-gray-400 mt-1">Prueba con otro producto o código.</p>
                </div>`;
            }

            return entries.map(entry => {
                const total = parseFloat(entry.total_expense) || 0;
                const totalBs = total * usdRate;
                const units = (entry.entries || []).reduce((sum, item) => sum + (parseFloat(item.quantity) || 0), 0);

                return `<div class="bg-white rounded-2xl p-4 shadow-brand-card border border-brand-border hover:border-[#d6e4ff] cursor-pointer transition-all active:scale-[0.99] space-y-3"
                    onclick="showDetailEntry(${entry.id})" role="button" tabindex="0"
                    onkeydown="if (event.key === 'Enter') showDetailEntry(${entry.id})">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-[#eef4ff] text-[#2454b5] flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-brand-navy font-mono tracking-tight">Compra #${entry.id}</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-brand-accent text-[9px] font-extrabold uppercase border border-emerald-100">Ingresado</span>
                                </div>
                                <p class="text-xs font-bold text-gray-700 mt-0.5">${formatFullDate(entry.created_at)}</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-sm font-extrabold text-brand-navy font-mono">$${total.toFixed(2)}</span>
                            <p class="text-[10px] text-gray-400 font-mono">Bs. ${totalBs.toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-[#eaf0fc] text-brand-navy font-bold text-[10px] border border-[#d8e3fa]">
                                <span class="material-symbols-outlined text-[14px] text-[#345ea8]">inventory_2</span>
                                <span>${entry.quantity_products} productos (${units} uds)</span>
                            </span>
                            <span class="text-[10px] font-medium text-gray-400 truncate">${formatShortDateTime(entry.created_at)}</span>
                        </div>
                        <span class="flex items-center gap-1 text-[11px] font-bold text-[#2454b5] flex-shrink-0">
                            <span>Detalle</span>
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </span>
                    </div>
                </div>`;
            }).join('');
        }

        function showDetailEntry(entryID) {
            fetch(`${window.tenantBase}/home/entradas/${entryID}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    buildDetailSheet(data.entries || []);
                    openDetailSheet();
                })
                .catch(error => console.error(error));
        }

        function buildDetailSheet(entries) {
            if (entries.length === 0) return;

            const first = entries[0];
            const totalUsd = entries.reduce((sum, entry) => sum + (parseFloat(entry.cost) || 0), 0);
            const totalUnits = entries.reduce((sum, entry) => sum + (parseFloat(entry.quantity) || 0), 0);
            let totalBs = entries.reduce((sum, entry) => sum + (parseFloat(entry.cost_bs) || 0), 0);

            if (totalBs === 0 && usdRate > 0) {
                totalBs = totalUsd * usdRate;
            }

            setText('detail-subtitle', `Compra #${first.entry_general_id} · ${formatFullDate(first.created_at)}`);
            setText('detail-items', `${entries.length} ítems / ${totalUnits} und`);
            setText('detail-total', `$${totalUsd.toFixed(2)} / Bs ${totalBs.toFixed(2)}`);
            setText('detail-rate', `Bs. ${Number(usdRate).toFixed(2)} / USD`);
            setText('detail-count', entries.length);

            const deleteBtn = document.getElementById('delete-btn');
            const updateBtn = document.getElementById('update-btn');

            deleteBtn.setAttribute('data-entryID', first.entry_general_id);
            updateBtn.setAttribute('data-entryID', first.entry_general_id);

            document.getElementById('detail-products').innerHTML = entries.map(entry => {
                const quantity = parseFloat(entry.quantity) || 0;
                const cost = parseFloat(entry.cost) || 0;
                const unitCost = quantity > 0 ? cost / quantity : 0;
                const product = entry.product || {};

                return `<div class="bg-white rounded-2xl p-3.5 border border-brand-border shadow-sm flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5 min-w-0">
                            <h4 class="text-xs font-extrabold text-brand-navy leading-snug truncate">${escapeHtml(product.name || 'Producto')}</h4>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-mono text-gray-400">SKU: ${escapeHtml(product.barcode || 'SIN SKU')}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span class="text-[10px] font-mono text-gray-500 bg-[#f0f4fc] px-1.5 py-0.5 rounded border border-[#dfe7f6]">${escapeHtml(entry.lote_number || 'Sin lote')}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs font-extrabold text-brand-navy font-mono block">$${cost.toFixed(2)}</span>
                            <p class="text-[9px] text-gray-400">Subtotal</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-emerald-50 text-brand-accent text-xs font-extrabold border border-emerald-100">+${quantity} und</span>
                            <span class="text-xs font-semibold text-gray-500">Costo Unit: <strong class="text-brand-navy font-mono">$${unitCost.toFixed(2)}</strong></span>
                        </div>
                        <span class="text-[10px] font-medium text-gray-400">${escapeHtml(formatExpiry(entry.expired_date))}</span>
                    </div>
                </div>`;
            }).join('');
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        }

        function openDetailSheet() {
            const sheet = document.getElementById('detail-sheet');
            const backdrop = document.getElementById('detail-backdrop');

            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailSheet() {
            const sheet = document.getElementById('detail-sheet');
            const backdrop = document.getElementById('detail-backdrop');

            sheet.classList.remove('translate-y-0');
            sheet.classList.add('translate-y-full');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }

        function deleteEntry(element) {
            const entryID = element.getAttribute('data-entryID');

            if (!entryID) return;

            if (confirm('¿Está seguro de eliminar esta compra? El stock se ajustará automáticamente.')) {
                const form = document.getElementById('actions-form-delete');
                form.action = `${window.tenantBase}/home/entradas/${entryID}`;
                form.submit();
            }
        }

        function updateEntry(element) {
            const entryID = element.getAttribute('data-entryID');

            if (entryID) {
                window.location.href = `${window.tenantBase}/home/entradas/editar/${entryID}`;
            }
        }
    </script>
@endsection
