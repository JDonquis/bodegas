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
        <section class="space-y-2">
            <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Registro de Ventas</h1>
            <p class="text-xs text-slate-500 font-normal leading-relaxed">
                Historial de ventas y movimientos de egreso de mercancía.
            </p>
            <div class="pt-1">
                <a href="{{ route('outputs.create') }}"
                    class="w-full bg-brand-navy active:bg-brand-navy-dark text-white font-bold py-3.5 px-5 rounded-2xl flex items-center justify-center gap-2.5 shadow-lg shadow-brand-navy/25 hover:shadow-xl transition-all active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[20px] text-brand-accent">sell</span>
                    <span class="text-sm tracking-wide">Nueva Venta</span>
                </a>
            </div>
        </section>

        <section class="space-y-2.5">
            <div class="grid grid-cols-2 gap-3">
                <article
                    class="bg-white rounded-2xl p-3.5 border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-1">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Ventas</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-xl font-extrabold text-brand-navy">${{ number_format($totalSales, 2) }}</span>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[16px]">shopping_cart_checkout</span>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-100/80 flex items-center justify-between text-[11px] gap-1">
                        <span class="text-slate-400 font-medium">Equiv:</span>
                        <span class="font-semibold text-slate-600 truncate">Bs. {{ number_format($totalSalesBs, 2) }}</span>
                    </div>
                </article>

                <article
                    class="bg-emerald-50/60 rounded-2xl p-3.5 border border-emerald-100 shadow-sm flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-1">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-800/80">Utilidad Neta</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-xl font-extrabold text-emerald-700">${{ number_format($totalProfit, 2) }}</span>
                            </div>
                            <p class="text-[10px] font-semibold text-emerald-700/80">Bs.
                                {{ number_format($totalProfitBs, 2) }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[16px]">trending_up</span>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-emerald-200/50 flex items-center justify-between text-[11px] gap-1">
                        <span class="text-emerald-800 font-medium">Margen</span>
                        <span
                            class="font-bold text-emerald-700 bg-emerald-100/90 px-1.5 py-0.5 rounded-full text-[10px]">{{ $totalMargin }}%</span>
                    </div>
                </article>
            </div>

            <div class="bg-white rounded-2xl px-4 py-2.5 border border-slate-100 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[16px]">outbox</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ítems Despachados</p>
                        <p class="text-xs font-bold text-brand-navy">{{ $todayOutputs }} transacciones
                            <span class="font-medium text-slate-500">({{ number_format($todayUnits) }} unidades)</span>
                        </p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600">Hoy</span>
            </div>
        </section>

        <section>
            <div
                class="relative bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center px-3.5 py-1 focus-within:ring-2 focus-within:ring-brand-accent/30 focus-within:border-brand-accent transition-all">
                <span class="material-symbols-outlined text-[18px] text-slate-400 mr-2 flex-shrink-0">search</span>
                <input type="text" id="search-input" value="{{ $search ?? '' }}"
                    placeholder="Buscar por cliente, ticket o producto..."
                    class="w-full text-xs font-medium text-brand-navy placeholder-slate-400 bg-transparent border-0 focus:ring-0 p-1.5 leading-snug">
                <button type="button" id="btn-limpiar"
                    class="{{ $search ? '' : 'hidden' }} p-1.5 rounded-lg text-slate-400 hover:text-brand-navy active:scale-90 transition flex-shrink-0"
                    aria-label="Limpiar búsqueda">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between text-xs px-1">
                <span class="font-extrabold text-[11px] uppercase tracking-wider text-slate-400">Historial de
                    Ventas</span>
                <span class="text-slate-400 text-[11px]">Más reciente</span>
            </div>

            <div id="outputs-feed" class="space-y-3">
                @forelse ($outputs as $output)
                    @php
                        $unitQuantity = $output->outputs->filter(function ($item) {
                            return optional($item->product)->sale_type !== 'weight';
                        })->sum('quantity');
                        $weightQuantity = $output->outputs->filter(function ($item) {
                            return optional($item->product)->sale_type === 'weight';
                        })->sum('quantity');
                        $qtyLabel = trim(
                            ($unitQuantity > 0 ? number_format($unitQuantity) . ' uds' : '') .
                                ($weightQuantity > 0 ? ' ' . number_format($weightQuantity) . ' g' : ''),
                        );
                        if ($qtyLabel === '') {
                            $qtyLabel = '0';
                        }
                        $names = $output->outputs
                            ->take(2)
                            ->map(function ($item) {
                                return (optional($item->product)->name ?? 'Producto') . ' (x' . $item->quantity . ')';
                            })
                            ->implode(', ');
                        if ($output->outputs->count() > 2) {
                            $names .= ' +' . ($output->outputs->count() - 2) . ' más';
                        }
                        $clientName = optional($output->client)->name ?? ($output->client_name ?: 'Cliente Genérico');
                        $isGeneric = !$output->client && !$output->client_name;
                        $paymentMethod = $output->payment_method ?: 'Sin método';
                        $totalBs = $output->total_sold * $usdRate;
                    @endphp

                    <article
                        class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:border-slate-200 transition-all cursor-pointer flex flex-col gap-3"
                        onclick="showDetailOutput({{ $output->id }})" role="button" tabindex="0"
                        onkeydown="if (event.key === 'Enter') showDetailOutput({{ $output->id }})">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    #</div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-xs font-extrabold text-brand-navy">Ticket #{{ $output->id }}</h3>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">Completada</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700 mt-0.5 flex items-center gap-1 min-w-0">
                                        <span class="truncate">{{ $clientName }}</span>
                                        @if ($isGeneric)
                                            <span class="text-[10px] font-normal text-slate-400">(Mostrador)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="text-sm font-extrabold text-brand-navy tracking-tight">${{ number_format($output->total_sold, 2) }}</span>
                                <p class="text-[10px] font-medium text-slate-400">Bs.
                                    {{ number_format($totalBs, 2) }}</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between text-xs bg-slate-50/80 rounded-xl px-3 py-2 border border-slate-100 gap-2">
                            <div class="flex items-center gap-1.5 text-slate-600 text-[11px] font-medium min-w-0">
                                <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">inventory_2</span>
                                <span class="truncate">{{ $names ?: 'Sin productos' }}</span>
                            </div>
                            <span class="text-[11px] font-bold text-brand-navy shrink-0">{{ $qtyLabel }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-1 text-xs gap-2">
                            <div class="flex items-center gap-2 text-slate-400 text-[11px] min-w-0">
                                <span class="flex items-center gap-1 shrink-0">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    {{ $output->created_at->translatedFormat('j M · h:i A') }}
                                </span>
                                <span class="w-1 h-1 rounded-full bg-slate-300 shrink-0"></span>
                                <span
                                    class="font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded text-[10px] truncate">{{ $paymentMethod }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 font-bold text-xs text-blue-600 shrink-0">
                                <span>Detalle</span>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No hay ventas registradas</p>
                        <p class="text-[11px] text-slate-400 mt-1">Registra tu primera venta para verla aquí.</p>
                    </div>
                @endforelse
            </div>

            @if ($outputs->hasPages())
                <div id="pagination-container"
                    class="bg-white rounded-2xl p-3 border border-slate-100 shadow-sm flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Página
                        <strong class="text-brand-navy font-bold">{{ $outputs->currentPage() }}</strong> de
                        {{ $outputs->lastPage() }}
                        <span class="text-slate-400">({{ $outputs->total() }} reg.)</span>
                    </span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $outputs->previousPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ $outputs->onFirstPage() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        @foreach ($outputs->getUrlRange(max(1, $outputs->currentPage() - 1), min($outputs->lastPage(), $outputs->currentPage() + 1)) as $page => $url)
                            <a href="{{ $url }}"
                                class="{{ $page == $outputs->currentPage() ? 'w-7 h-7 rounded-lg bg-brand-navy text-white text-xs font-bold flex items-center justify-center shadow-sm' : 'w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 text-xs font-semibold flex items-center justify-center hover:bg-slate-50' }}">{{ $page }}</a>
                        @endforeach
                        <a href="{{ $outputs->nextPageUrl() ?? '#' }}"
                            class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center {{ !$outputs->hasMorePages() ? 'opacity-40 pointer-events-none' : 'hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
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
                        Detalle de Venta</h3>
                    <p class="text-[11px] font-semibold text-gray-500 tracking-wide mt-0.5" id="detail-subtitle">
                        Ticket #---</p>
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
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Cliente</span>
                            <span class="font-extrabold text-xs text-brand-navy" id="detail-client">Cliente Genérico</span>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-brand-accent text-[10px] font-extrabold border border-emerald-100">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        Completada
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-0.5">
                    <div class="bg-[#f5f8ff] p-2.5 rounded-xl border border-[#e1e9fa]">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Total
                            Productos</span>
                        <span class="text-xs font-extrabold text-brand-navy mt-0.5 block" id="detail-items">0 ítems</span>
                    </div>
                    <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Monto
                            Total</span>
                        <span class="text-xs font-extrabold text-brand-accent mt-0.5 block" id="detail-total">$0.00 / Bs
                            0.00</span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-[10px] text-gray-400 pt-0.5 px-0.5 gap-2">
                    <span class="truncate">Pago: <span class="font-bold text-brand-navy" id="detail-payment">Sin
                            método</span></span>
                    <span class="font-mono font-bold text-brand-navy shrink-0" id="detail-rate">Bs. 0.00 / USD</span>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Productos
                        Vendidos (<span id="detail-count">0</span>)</span>
                    <span
                        class="text-[10px] font-semibold text-brand-accent bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">Venta
                        confirmada</span>
                </div>
                <div id="detail-products" class="space-y-2"></div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-brand-border shadow-inner">
            <div class="grid grid-cols-2 gap-3">
                <a id="detail-invoice-link" href="#" target="_blank"
                    class="py-3 px-3 rounded-2xl border border-[#dfe7f6] bg-[#f8faff] hover:bg-[#eef4fc] active:bg-[#e2ecff] text-brand-navy flex items-center justify-center gap-1.5 text-xs font-bold transition active:scale-95 shadow-sm">
                    <span class="material-symbols-outlined text-[18px] text-[#2b5fd9]">receipt_long</span>
                    <span>Ver Comprobante</span>
                </a>
                <button type="button" id="delete-btn" onclick="deleteOutput(this)" data-outputID=""
                    class="py-3 px-3 rounded-2xl border border-red-200 text-red-600 bg-red-50/50 hover:bg-red-100 active:bg-red-200 flex items-center justify-center gap-1.5 text-xs font-bold transition active:scale-95">
                    <span class="material-symbols-outlined text-[18px] text-red-500">delete</span>
                    <span>Anular Venta</span>
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

        function quantityLabel(outputs) {
            const unitQuantity = (outputs || [])
                .filter(item => item.product && item.product.sale_type !== 'weight')
                .reduce((sum, item) => sum + (parseFloat(item.quantity) || 0), 0);
            const weightQuantity = (outputs || [])
                .filter(item => item.product && item.product.sale_type === 'weight')
                .reduce((sum, item) => sum + (parseFloat(item.quantity) || 0), 0);
            const parts = [];

            if (unitQuantity > 0) parts.push(`${unitQuantity} uds`);
            if (weightQuantity > 0) parts.push(`${weightQuantity} g`);

            return parts.join(' ') || '0';
        }

        function productNames(outputs) {
            const items = outputs || [];
            let names = items.slice(0, 2).map(item => {
                return `${item.product ? item.product.name : 'Producto'} (x${item.quantity})`;
            }).join(', ');

            if (items.length > 2) {
                names += ` +${items.length - 2} más`;
            }

            return names || 'Sin productos';
        }

        function performSearch(query) {
            const feed = document.getElementById('outputs-feed');
            const pagination = document.getElementById('pagination-container');
            const clearButton = document.getElementById('btn-limpiar');

            if (query.length > 0) {
                clearButton.classList.remove('hidden');
            } else {
                clearButton.classList.add('hidden');
            }

            fetch(`${window.tenantBase}/home/salidas/search?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    feed.innerHTML = renderOutputs(data.outputs || []);

                    if (pagination) {
                        pagination.style.display = 'none';
                    }
                })
                .catch(error => console.error(error));
        }

        function renderOutputs(outputs) {
            if (outputs.length === 0) {
                return `<div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">search_off</span>
                    <p class="mt-2 text-xs font-bold text-brand-navy">No se encontraron ventas</p>
                    <p class="text-[11px] text-slate-400 mt-1">Prueba con otro cliente o producto.</p>
                </div>`;
            }

            return outputs.map(output => {
                const totalSold = parseFloat(output.total_sold) || 0;
                const totalBs = totalSold * usdRate;
                const clientName = output.client ? output.client.name : (output.client_name || 'Cliente Genérico');
                const isGeneric = !output.client && !output.client_name;
                const payment = output.payment_method || 'Sin método';
                const items = output.outputs || [];
                const qty = quantityLabel(items);
                const names = productNames(items);

                return `<article class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:border-slate-200 transition-all cursor-pointer flex flex-col gap-3"
                    onclick="showDetailOutput(${output.id})" role="button" tabindex="0"
                    onkeydown="if (event.key === 'Enter') showDetailOutput(${output.id})">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">#</div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xs font-extrabold text-brand-navy">Ticket #${output.id}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">Completada</span>
                                </div>
                                <p class="text-xs font-bold text-slate-700 mt-0.5 flex items-center gap-1 min-w-0">
                                    <span class="truncate">${escapeHtml(clientName)}</span>
                                    ${isGeneric ? '<span class="text-[10px] font-normal text-slate-400">(Mostrador)</span>' : ''}
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-sm font-extrabold text-brand-navy tracking-tight">$${totalSold.toFixed(2)}</span>
                            <p class="text-[10px] font-medium text-slate-400">Bs. ${totalBs.toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs bg-slate-50/80 rounded-xl px-3 py-2 border border-slate-100 gap-2">
                        <div class="flex items-center gap-1.5 text-slate-600 text-[11px] font-medium min-w-0">
                            <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">inventory_2</span>
                            <span class="truncate">${escapeHtml(names)}</span>
                        </div>
                        <span class="text-[11px] font-bold text-brand-navy shrink-0">${qty}</span>
                    </div>
                    <div class="flex items-center justify-between pt-1 text-xs gap-2">
                        <div class="flex items-center gap-2 text-slate-400 text-[11px] min-w-0">
                            <span class="flex items-center gap-1 shrink-0">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                ${formatShortDateTime(output.created_at)}
                            </span>
                            <span class="w-1 h-1 rounded-full bg-slate-300 shrink-0"></span>
                            <span class="font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded text-[10px] truncate">${escapeHtml(payment)}</span>
                        </div>
                        <span class="inline-flex items-center gap-1 font-bold text-xs text-blue-600 shrink-0">
                            <span>Detalle</span>
                            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        </span>
                    </div>
                </article>`;
            }).join('');
        }

        function showDetailOutput(outputID) {
            fetch(`${window.tenantBase}/home/salidas/${outputID}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    buildDetailSheet(data.outputs || [], data.outputGeneral || {});
                    openDetailSheet();
                })
                .catch(error => console.error(error));
        }

        function buildDetailSheet(outputs, general) {
            const totalSold = parseFloat(general.total_sold) || 0;
            const totalBs = totalSold * usdRate;
            const clientName = general.client ? general.client.name : (general.client_name || 'Cliente Genérico');
            const isGeneric = !general.client && !general.client_name;
            const payment = general.payment_method || 'Sin método';
            const items = outputs || [];
            const qty = quantityLabel(items);
            const createdAt = general.created_at || (items[0] ? items[0].created_at : new Date());

            setText('detail-subtitle', `Ticket #${general.id || '---'} · ${formatFullDate(createdAt)}`);
            setText('detail-client', clientName + (isGeneric ? ' (Mostrador)' : ''));
            setText('detail-items', `${items.length} ítems / ${qty}`);
            setText('detail-total', `$${totalSold.toFixed(2)} / Bs ${totalBs.toFixed(2)}`);
            setText('detail-payment', payment);
            setText('detail-rate', `Bs. ${Number(usdRate).toFixed(2)} / USD`);
            setText('detail-count', items.length);

            const invoiceLink = document.getElementById('detail-invoice-link');

            if (invoiceLink && general.id) {
                invoiceLink.href = `${window.tenantBase}/home/invoice/${general.id}`;
            }

            const deleteBtn = document.getElementById('delete-btn');

            if (deleteBtn) {
                deleteBtn.setAttribute('data-outputID', general.id || '');
            }

            document.getElementById('detail-products').innerHTML = items.map(item => {
                const product = item.product || {};
                const inventory = item.inventory || {};
                const isWeight = product.sale_type === 'weight';
                const pricePerKg = parseFloat(product.price_per_kg) || 0;
                const sellPrice = parseFloat(product.sell_price) || 0;
                const effectivePrice = isWeight && pricePerKg > 0 ? pricePerKg / 1000 : sellPrice;
                const displayPrice = isWeight && pricePerKg > 0 ? pricePerKg : sellPrice;
                const quantity = parseFloat(item.quantity) || 0;
                const subtotal = effectivePrice * quantity;
                const subtotalBs = subtotal * usdRate;
                const profit = parseFloat(item.profit) || 0;
                const unitLabel = isWeight ? 'g' : 'uds';
                const unitPriceBs = displayPrice * usdRate;
                const unitPriceLabel = isWeight ? `$${displayPrice.toFixed(2)}/kg` : `$${displayPrice.toFixed(2)}`;
                const unitPriceBsLabel = isWeight ? `Bs ${unitPriceBs.toFixed(2)}/kg` : `Bs ${unitPriceBs.toFixed(2)}`;

                return `<div class="bg-white rounded-2xl p-3.5 border border-brand-border shadow-sm flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5 min-w-0">
                            <h4 class="text-xs font-extrabold text-brand-navy leading-snug truncate">${escapeHtml(product.name || 'Producto')}</h4>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-mono text-gray-400">SKU: ${escapeHtml(product.barcode || 'SIN SKU')}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span class="text-[10px] font-mono text-gray-500 bg-[#f0f4fc] px-1.5 py-0.5 rounded border border-[#dfe7f6]">${escapeHtml(inventory.lote_number || 'Sin lote')}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs font-extrabold text-brand-navy font-mono block">$${subtotal.toFixed(2)}</span>
                            <p class="text-[9px] text-gray-400">Bs. ${subtotalBs.toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-50 gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-emerald-50 text-brand-accent text-xs font-extrabold border border-emerald-100">${quantity} ${unitLabel}</span>
                            <span class="text-[10px] font-semibold text-gray-500 truncate">P.Unit: ${unitPriceLabel} <span class="text-gray-400">(${unitPriceBsLabel})</span></span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] font-bold text-emerald-600 block">+$${profit.toFixed(2)}</span>
                            <span class="text-[9px] text-gray-400">${escapeHtml(formatExpiry(item.expired_date))}</span>
                        </div>
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

        function deleteOutput(element) {
            const outputID = element.getAttribute('data-outputID');

            if (!outputID) return;

            if (confirm('¿Está seguro de eliminar esta venta? El stock se devolverá automáticamente.')) {
                const form = document.getElementById('actions-form-delete');
                form.action = `${window.tenantBase}/home/salidas/${outputID}`;
                form.submit();
            }
        }
    </script>
@endsection
