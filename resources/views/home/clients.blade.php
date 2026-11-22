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

        #client-history-sheet {
            transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #client-history-backdrop {
            transition: opacity 0.3s ease-out;
        }
    </style>
@endsection

@section('content')
    @php
        $filterOptions = [
            ['value' => 'all', 'label' => 'Todos', 'count' => $totalClients],
            ['value' => 'debt', 'label' => 'Con Deuda', 'count' => $debtorsCount],
            ['value' => 'solvent', 'label' => 'Solventes', 'count' => $solventCount],
        ];
    @endphp

    <div class="mx-auto flex w-full max-w-md flex-col gap-5 font-jakarta">

        <section class="space-y-3">
            <div>
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Cartera de Clientes</h1>
                <p class="text-xs text-slate-500 font-normal mt-0.5">Gestión de contactos y control de cuentas por cobrar.
                </p>
            </div>

            <a href="{{ route('clients.create') }}"
                class="w-full bg-brand-navy hover:bg-brand-navy-light text-white font-semibold py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-brand-navy/15 active:scale-[0.98] transition-all duration-150">
                <span class="material-symbols-outlined text-[20px] text-brand-accent">person_add</span>
                <span class="text-sm tracking-wide">Nuevo Cliente</span>
            </a>
        </section>

        <section class="grid grid-cols-3 gap-2.5">
            <div
                class="bg-gradient-to-br from-blue-50/90 to-indigo-50/50 p-3 rounded-2xl border border-blue-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-blue-900 uppercase tracking-wider leading-none">Clientes</span>
                    <div class="w-6 h-6 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">group</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-brand-navy">{{ $totalClients }}</span>
                    <span class="block text-[9px] text-blue-700 font-semibold mt-0.5">+{{ $newThisMonth }} este
                        mes</span>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-rose-50/90 to-red-50/40 p-3 rounded-2xl border border-rose-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span class="text-[10px] font-bold text-rose-900 uppercase tracking-wider leading-none">Por
                        Cobrar</span>
                    <div class="w-6 h-6 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">payments</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-rose-600 tracking-tight">${{ number_format($totalDebt, 2) }}</span>
                    <span class="block text-[9px] text-rose-500 font-semibold mt-0.5">{{ $debtorsCount }} cliente(s)
                        pend.</span>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-emerald-50/90 to-teal-50/50 p-3 rounded-2xl border border-emerald-100 flex flex-col justify-between shadow-brand-card">
                <div class="flex items-center justify-between mb-1 gap-1">
                    <span
                        class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider leading-none">Solventes</span>
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-emerald-700 tracking-tight">{{ $solventPercentage }}%</span>
                    <span class="block text-[9px] text-emerald-600 font-semibold mt-0.5">{{ $solventCount }}
                        clientes</span>
                </div>
            </div>
        </section>

        <section class="space-y-2.5">
            <div class="relative">
                <span
                    class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] pointer-events-none">search</span>
                <input type="text" id="client-search" value="{{ request('search') }}"
                    placeholder="Buscar por cliente, cédula o teléfono..."
                    class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200/80 rounded-2xl text-xs text-brand-navy placeholder-slate-400 font-medium focus:ring-2 focus:ring-brand-navy focus:border-brand-navy transition shadow-sm">
                <button type="button" aria-label="Limpiar búsqueda" id="client-clear-search"
                    class="{{ request('search') ? '' : 'hidden' }} absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 pt-0.5">
                @foreach ($filterOptions as $option)
                    @php
                        $isActive = $activeFilter === $option['value'];
                    @endphp
                    <button type="button" data-filter="{{ $option['value'] }}"
                        class="client-filter-btn px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ $isActive ? 'bg-brand-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        {{ $option['label'] }} ({{ $option['count'] }})
                    </button>
                @endforeach
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Directorio de Clientes</h2>
                <span id="client-record-count" class="text-[11px] font-semibold text-slate-500">{{ $clients->total() }}
                    registros</span>
            </div>

            <div id="clients-feed" class="space-y-3">
                @forelse ($clients as $client)
                    @php
                        $hasDebt = $client->debt > 0;
                        $initials = collect(preg_split('/\s+/', trim($client->name)))
                            ->filter()
                            ->take(2)
                            ->map(function ($part) {
                                return mb_strtoupper(mb_substr($part, 0, 1));
                            })
                            ->implode('');
                        $initials = $initials ?: 'CG';
                        $phoneLink = $client->phone_number
                            ? preg_replace('/[^0-9+]/', '', $client->phone_number)
                            : null;
                    @endphp

                    <article
                        class="bg-white rounded-2xl p-4 border {{ $hasDebt ? 'border-rose-200' : 'border-slate-200/70' }} shadow-brand-card hover:border-slate-300 transition-all relative overflow-hidden">
                        @if ($hasDebt)
                            <div class="absolute top-0 left-0 right-0 h-1 bg-rose-500"></div>
                        @endif

                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-11 h-11 rounded-2xl flex items-center justify-center font-bold text-sm shrink-0 ring-1 {{ $hasDebt ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-blue-50 text-blue-700 ring-blue-200/70' }}">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-brand-navy truncate">{{ $client->name }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5 font-mono">CI:
                                        {{ $client->ci ?? 'No registrada' }}</p>
                                </div>
                            </div>

                            @if ($hasDebt)
                                <div class="text-right shrink-0">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
                                        Deuda: ${{ number_format($client->debt, 2) }}
                                    </span>
                                    <span class="block text-[9px] text-slate-400 mt-0.5 font-medium">Límite:
                                        ${{ number_format($client->credit_limit ?? 0, 2) }}</span>
                                </div>
                            @else
                                <div class="text-right shrink-0">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Solvente
                                    </span>
                                    <span class="block text-[9px] text-emerald-600 font-medium mt-0.5">
                                        Crédito: ${{ number_format($client->credit_limit ?? 0, 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                @if ($client->phone_number)
                                    <a href="tel:{{ $phoneLink }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg {{ $hasDebt ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }} text-[11px] font-medium border active:scale-95 transition shrink-0">
                                        <span class="material-symbols-outlined text-[14px]">call</span>
                                        <span>{{ $client->phone_number }}</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic shrink-0">Sin teléfono</span>
                                @endif

                                <span class="text-[11px] text-slate-500 truncate">
                                    {{ $client->address ?? 'Sin dirección' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('clients.edit', ['client' => $client->id]) }}" title="Editar cliente"
                                    class="p-1.5 text-blue-600 hover:text-brand-navy hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <button type="button"
                                    onclick="deleteClient('{{ route('clients.delete', ['client' => $client->id]) }}')"
                                    title="Eliminar cliente"
                                    class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('debts', ['client_id' => $client->id, 'open_debt' => 1]) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold hover:bg-rose-100 transition">
                                <span class="material-symbols-outlined text-[14px]">add_card</span>
                                Agregar deuda
                            </a>
                            @if ($client->debt > 0)
                                <a href="{{ route('debts', ['client_id' => $client->id, 'open_payment' => 1]) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold hover:bg-emerald-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">payments</span>
                                    Abonar
                                </a>
                            @endif
                            <button type="button" onclick="showClientHistory({{ $client->id }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold hover:bg-blue-100 transition">
                                <span class="material-symbols-outlined text-[14px]">history</span>
                                Historial
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-brand-card text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300">group_off</span>
                        <p class="mt-2 text-xs font-bold text-brand-navy">No se encontraron clientes</p>
                        <p class="text-[11px] text-slate-400 mt-1">Prueba con otro término o filtro.</p>
                    </div>
                @endforelse
            </div>

            <div id="clients-pagination-wrapper" class="pt-2 pb-4 space-y-2 text-center">
                @if ($clients->hasPages())
                    <p class="text-[11px] font-medium tracking-wide text-slate-400 uppercase">
                        Mostrando {{ $clients->firstItem() }} - {{ $clients->lastItem() }} de
                        {{ $clients->total() }} clientes
                    </p>
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ $clients->previousPageUrl() ?? '#' }}"
                            class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center {{ $clients->onFirstPage() ? 'text-slate-300 pointer-events-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        @foreach ($clients->getUrlRange(max(1, $clients->currentPage() - 1), min($clients->lastPage(), $clients->currentPage() + 1)) as $page => $url)
                            <a href="{{ $url }}"
                                class="{{ $page == $clients->currentPage() ? 'w-8 h-8 rounded-xl bg-brand-navy text-white font-bold text-xs flex items-center justify-center shadow-md shadow-brand-navy/20' : 'w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 font-semibold text-xs flex items-center justify-center hover:bg-slate-50' }}">{{ $page }}</a>
                        @endforeach
                        <a href="{{ $clients->nextPageUrl() ?? '#' }}"
                            class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center {{ !$clients->hasMorePages() ? 'text-slate-300 pointer-events-none' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>


    <div id="client-history-backdrop"
        class="fixed inset-0 z-[60] bg-brand-navy/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"
        onclick="closeClientHistory()"></div>

    <section id="client-history-sheet"
        class="fixed inset-x-0 bottom-0 z-[70] mx-auto max-w-md bg-white rounded-t-[34px] shadow-brand-sheet border-t border-white/60 transform translate-y-full flex flex-col max-h-[92vh] overflow-hidden"
        role="dialog" aria-modal="true" aria-labelledby="client-history-title">
        <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeClientHistory()">
            <div class="w-12 h-1.5 rounded-full bg-gray-300"></div>
        </div>

        <div class="px-5 pt-2 pb-3.5 border-b border-brand-border flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">history</span>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-brand-navy tracking-tight leading-tight"
                        id="client-history-title">Historial de Cuenta</h3>
                    <p class="text-[11px] font-semibold text-gray-500 tracking-wide mt-0.5" id="client-history-name">
                        Cliente</p>
                </div>
            </div>
            <button type="button" onclick="closeClientHistory()" aria-label="Cerrar ventana"
                class="w-9 h-9 rounded-full bg-[#f3f5fa] hover:bg-gray-200 text-gray-500 hover:text-brand-navy flex items-center justify-center active:scale-90 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-5 py-4 overflow-y-auto space-y-3.5 no-scrollbar bg-[#f8f9ff]">
            <div class="bg-white rounded-2xl p-4 border border-brand-border shadow-sm grid grid-cols-2 gap-3">
                <div class="bg-rose-50/60 p-2.5 rounded-xl border border-rose-100">
                    <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Deuda
                        Actual</span>
                    <span class="text-sm font-extrabold text-rose-600 mt-0.5 block" id="client-history-debt">$0.00</span>
                </div>
                <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                    <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wide">Límite
                        Crédito</span>
                    <span class="text-sm font-extrabold text-emerald-700 mt-0.5 block"
                        id="client-history-credit">$0.00</span>
                </div>
            </div>

            <div class="flex items-center justify-between px-1">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400">Movimientos</span>
                <span class="text-[10px] font-semibold text-slate-500">Más reciente primero</span>
            </div>

            <div id="client-history-list" class="space-y-2"></div>
        </div>

        <div class="p-4 bg-white border-t border-brand-border shadow-inner">
            <button type="button" onclick="closeClientHistory()"
                class="w-full py-3 rounded-2xl bg-brand-navy text-white text-xs font-bold active:scale-[0.98] transition">
                Entendido, cerrar
            </button>
        </div>
    </section>

    <form action="" id="actions-form-delete" class="hidden" method="POST">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <script>
        const clientsBaseUrl = @json(route('clients'));
        let currentFilter = @json($activeFilter);
        let clientSearchTimeout = null;

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('client-search');
            const clearButton = document.getElementById('client-clear-search');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(clientSearchTimeout);
                    clientSearchTimeout = setTimeout(function() {
                        const url = buildClientsUrl();
                        history.replaceState({}, '', url);
                        updateClearButton();
                        loadClients(url);
                    }, 300);
                });
            }

            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    const url = buildClientsUrl();
                    history.replaceState({}, '', url);
                    updateClearButton();
                    loadClients(url);
                });
            }

            document.querySelectorAll('.client-filter-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    currentFilter = this.dataset.filter;
                    updateFilterButtons();

                    const url = buildClientsUrl();
                    history.replaceState({}, '', url);
                    loadClients(url);
                });
            });
        });

        function buildClientsUrl() {
            const url = new URL(clientsBaseUrl);
            const searchInput = document.getElementById('client-search');
            const search = searchInput ? searchInput.value.trim() : '';

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (currentFilter !== 'all') {
                url.searchParams.set('filter', currentFilter);
            } else {
                url.searchParams.delete('filter');
            }

            url.searchParams.delete('page');

            return url;
        }

        function updateClearButton() {
            const searchInput = document.getElementById('client-search');
            const clearButton = document.getElementById('client-clear-search');

            if (!searchInput || !clearButton) return;

            clearButton.classList.toggle('hidden', searchInput.value.trim().length === 0);
        }

        function updateFilterButtons() {
            const activeClasses = ['bg-brand-navy', 'text-white', 'shadow-sm'];
            const inactiveClasses = ['bg-white', 'border', 'border-slate-200', 'text-slate-600', 'hover:bg-slate-50'];

            document.querySelectorAll('.client-filter-btn').forEach(function(button) {
                button.classList.remove(...activeClasses);
                button.classList.remove(...inactiveClasses);

                if (button.dataset.filter === currentFilter) {
                    button.classList.add(...activeClasses);
                } else {
                    button.classList.add(...inactiveClasses);
                }
            });
        }

        function loadClients(url) {
            const feed = document.getElementById('clients-feed');
            const paginationWrapper = document.getElementById('clients-pagination-wrapper');
            const countLabel = document.getElementById('client-record-count');

            if (!feed) return;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newFeed = doc.getElementById('clients-feed');
                    const newPagination = doc.getElementById('clients-pagination-wrapper');
                    const newCount = doc.getElementById('client-record-count');

                    if (newFeed) {
                        feed.innerHTML = newFeed.innerHTML;
                    }

                    if (paginationWrapper) {
                        paginationWrapper.innerHTML = newPagination ? newPagination.innerHTML : '';
                    }

                    if (countLabel && newCount) {
                        countLabel.textContent = newCount.textContent;
                    }
                })
                .catch(error => console.error(error));
        }

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

        function showClientHistory(clientId) {
            fetch(`${window.tenantBase}/home/clientes/${clientId}/historial`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    buildClientHistory(data);
                    openClientHistory();
                })
                .catch(error => console.error(error));
        }

        function buildClientHistory(data) {
            const client = data.client || {};
            const history = data.history || [];

            document.getElementById('client-history-name').textContent =
                `${client.name || 'Cliente'}${client.ci ? ' · ' + client.ci : ''}`;
            document.getElementById('client-history-debt').textContent = '$' + (Number(data.debt) || 0).toFixed(2);
            document.getElementById('client-history-credit').textContent = '$' + (Number(data.credit_limit) || 0)
                .toFixed(2);

            const container = document.getElementById('client-history-list');

            if (history.length === 0) {
                container.innerHTML = `<div class="bg-white rounded-2xl p-8 border border-brand-border shadow-sm text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                    <p class="mt-2 text-xs font-bold text-brand-navy">Sin movimientos registrados</p>
                </div>`;
                return;
            }

            container.innerHTML = history.map(item => {
                const isPayment = item.type === 'payment';
                const amount = Number(item.amount) || 0;
                const date = formatHistoryDate(item.date);
                const description = item.description ? `<p class="text-[11px] text-slate-500 mt-1">${escapeHtml(item.description)}</p>` : '';
                const sale = (!isPayment && item.sale_id) ?
                    `<a href="${window.tenantBase}/home/invoice/${item.sale_id}" target="_blank" class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-blue-600 hover:underline"><span class="material-symbols-outlined text-[12px]">receipt_long</span>Venta #${item.sale_id}</a>` :
                    '';

                return `<div class="bg-white rounded-2xl p-3.5 border border-brand-border shadow-sm flex items-start justify-between gap-2">
                    <div class="flex items-start gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 ${isPayment ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}">
                            <span class="material-symbols-outlined text-[16px]">${isPayment ? 'payments' : 'add_card'}</span>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-brand-navy">${isPayment ? 'Abono registrado' : 'Deuda registrada'}</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">${date}${!isPayment && item.remaining !== undefined ? ' · Saldo $' + Number(item.remaining).toFixed(2) : ''}</p>
                            ${description}
                            ${sale}
                        </div>
                    </div>
                    <span class="text-sm font-extrabold shrink-0 ${isPayment ? 'text-emerald-600' : 'text-rose-600'}">${isPayment ? '−' : '+'}$${amount.toFixed(2)}</span>
                </div>`;
            }).join('');
        }

        function formatHistoryDate(dateString) {
            const date = new Date(dateString);
            const months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            const time = date.toLocaleTimeString('es-VE', { hour: '2-digit', minute: '2-digit' });

            return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()} · ${time}`;
        }

        function openClientHistory() {
            const sheet = document.getElementById('client-history-sheet');
            const backdrop = document.getElementById('client-history-backdrop');

            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');
            document.body.style.overflow = 'hidden';
        }

        function closeClientHistory() {
            const sheet = document.getElementById('client-history-sheet');
            const backdrop = document.getElementById('client-history-backdrop');

            sheet.classList.remove('translate-y-0');
            sheet.classList.add('translate-y-full');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeClientHistory();
            }
        });

        function deleteClient(url) {
            if (confirm('¿Está seguro de eliminar este cliente? Esta acción no se puede deshacer.')) {
                const form = document.getElementById('actions-form-delete');
                form.action = url;
                form.submit();
            }
        }
    </script>
@endsection
