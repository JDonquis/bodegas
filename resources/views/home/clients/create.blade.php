@extends('layout.app')

@section('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
@endsection

@section('content')
    @php
        $oldCI = old('clientCI', '');
        preg_match('/^\s*([VEJG])[-\s]?/i', $oldCI, $matches);
        $ciPrefix = strtoupper($matches[1] ?? 'V');
        $ciNumber = $matches ? preg_replace('/^\s*[VEJG][-\s]?/i', '', $oldCI) : $oldCI;
        $clientType = in_array($ciPrefix, ['J', 'G'], true) ? 'business' : 'natural';
    @endphp

    <div class="mx-auto w-full max-w-md px-1 font-jakarta">
        <div class="flex items-start gap-3.5 mb-5 px-1">
            <a href="{{ route('clients') }}" aria-label="Regresar a clientes"
                class="mt-0.5 w-9 h-9 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-sm active:scale-90 transition-transform">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Nuevo Cliente</h1>
                <p class="text-xs text-slate-600 font-medium leading-relaxed mt-0.5">Registra un nuevo contacto y
                    gestiona su límite de crédito en el sistema.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-blue-50/80 shadow-brand-card">
            <form method="POST" action="{{ route('clients.store') }}" id="client-form" class="space-y-4">
                @csrf

                <div class="p-1 bg-[#f0f4ff] rounded-2xl flex items-center gap-1 border border-blue-100/60">
                    <button type="button" id="tab-natural" onclick="setClientType('natural')"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">person</span>
                        Persona Natural
                    </button>
                    <button type="button" id="tab-business" onclick="setClientType('business')"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">storefront</span>
                        Empresa (RIF)
                    </button>
                </div>

                <div>
                    <label id="client-name-label" for="clientName"
                        class="block text-[11px] font-bold text-slate-700 tracking-wider uppercase mb-1.5">Nombre Completo
                        <span class="text-rose-500">*</span></label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 pointer-events-none text-slate-600">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </div>
                        <input type="text" name="clientName" id="clientName" value="{{ old('clientName') }}"
                            placeholder="Ej: Juan Pérez" required autofocus
                            class="w-full pl-11 pr-4 py-3 bg-[#f0f4ff]/80 focus:bg-white text-sm font-semibold text-brand-navy placeholder:text-slate-500 rounded-2xl border border-transparent focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                    </div>
                    @error('clientName')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label id="client-id-label" for="ci-number"
                        class="block text-[11px] font-bold text-slate-700 tracking-wider uppercase mb-1.5">Cédula / ID
                        Documento</label>
                    <div class="flex items-center gap-2">
                        <div class="relative w-24">
                            <select id="ci-prefix"
                                class="w-full py-3 pl-3 pr-7 bg-[#f0f4ff]/80 text-sm font-bold text-brand-navy rounded-2xl border border-transparent focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none appearance-none">
                                <option value="V" @selected($ciPrefix === 'V')>V -</option>
                                <option value="E" @selected($ciPrefix === 'E')>E -</option>
                                <option value="J" @selected($ciPrefix === 'J')>J -</option>
                                <option value="G" @selected($ciPrefix === 'G')>G -</option>
                            </select>
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-600">
                                <span class="material-symbols-outlined text-[14px]">expand_more</span>
                            </div>
                        </div>
                        <div class="relative flex-1 flex items-center">
                            <div class="absolute left-3.5 pointer-events-none text-slate-600">
                                <span class="material-symbols-outlined text-[18px]">badge</span>
                            </div>
                            <input type="text" id="ci-number" value="{{ $ciNumber }}" placeholder="12345678"
                                class="w-full pl-10 pr-4 py-3 bg-[#f0f4ff]/80 focus:bg-white text-sm font-semibold text-brand-navy placeholder:text-slate-500 rounded-2xl border border-transparent focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                    </div>
                    <input type="hidden" name="clientCI" id="clientCI" value="{{ old('clientCI') }}">
                    @error('clientCI')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="clientPhoneNumber"
                        class="block text-[11px] font-bold text-slate-700 tracking-wider uppercase mb-1.5">Teléfono
                        Móvil</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 pointer-events-none text-slate-600">
                            <span class="material-symbols-outlined text-[18px]">call</span>
                        </div>
                        <input type="tel" name="clientPhoneNumber" id="clientPhoneNumber"
                            value="{{ old('clientPhoneNumber') }}" placeholder="0412-0000000"
                            class="w-full pl-10 pr-12 py-3 bg-[#f0f4ff]/80 focus:bg-white text-sm font-semibold text-brand-navy placeholder:text-slate-500 rounded-2xl border border-transparent focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        <div
                            class="absolute right-3 w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200 pointer-events-none">
                            <span class="material-symbols-outlined text-[15px]">chat</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="p-3 bg-rose-50/50 rounded-2xl border border-rose-100">
                        <label for="clientDebt"
                            class="block text-[10px] font-bold text-rose-800 tracking-wider uppercase mb-1">Deuda
                            Inicial ($)</label>
                        <div class="relative flex items-center">
                            <span class="text-rose-500 font-bold text-sm mr-1.5">$</span>
                            <input type="number" step="0.01" min="0" name="clientDebt" id="clientDebt"
                                value="{{ old('clientDebt', 0) }}"
                                class="w-full p-0 bg-transparent text-rose-700 font-extrabold text-base border-none focus:ring-0 outline-none">
                        </div>
                        <p class="text-[9px] text-rose-600/80 italic mt-1 leading-snug">Saldo pendiente por cobrar</p>
                    </div>

                    <div class="p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                        <label for="clientCreditLimit"
                            class="block text-[10px] font-bold text-emerald-800 tracking-wider uppercase mb-1">Límite
                            Crédito ($)</label>
                        <div class="relative flex items-center">
                            <span class="text-emerald-600 font-bold text-sm mr-1.5">$</span>
                            <input type="number" step="0.01" min="0" name="clientCreditLimit" id="clientCreditLimit"
                                value="{{ old('clientCreditLimit', 0) }}" placeholder="50.00"
                                class="w-full p-0 bg-transparent text-emerald-700 font-extrabold text-base border-none focus:ring-0 outline-none placeholder:text-emerald-400">
                        </div>
                        <p class="text-[9px] text-emerald-700/80 italic mt-1 leading-snug">Monto máx. para fiado</p>
                    </div>
                </div>

                <div>
                    <label for="clientAddress"
                        class="block text-[11px] font-bold text-slate-700 tracking-wider uppercase mb-1.5">Dirección de
                        Habitación / Entrega</label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-3.5 pointer-events-none text-slate-600">
                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                        </div>
                        <textarea name="clientAddress" id="clientAddress" rows="2"
                            placeholder="Calle, Sector, Edificio o Punto de Referencia..."
                            class="w-full pl-10 pr-4 py-2.5 bg-[#f0f4ff]/80 focus:bg-white text-xs font-medium text-brand-navy placeholder:text-slate-500 rounded-2xl border border-transparent focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none resize-none transition-all">{{ old('clientAddress') }}</textarea>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-between gap-3">
                    <a href="{{ route('clients') }}"
                        class="w-1/3 py-3.5 px-3 rounded-2xl text-slate-500 hover:text-slate-800 text-xs font-bold tracking-wide uppercase active:scale-95 transition-all text-center">Descartar</a>
                    <button type="submit"
                        class="w-2/3 py-3.5 px-4 rounded-2xl bg-brand-navy text-white text-xs font-extrabold tracking-wide uppercase shadow-brand-btn active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-brand-accent">person_add</span>
                        Registrar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const initialClientType = @json($clientType);

        document.addEventListener('DOMContentLoaded', function() {
            setClientType(initialClientType);
            updateHiddenCI();

            document.getElementById('ci-prefix').addEventListener('change', updateHiddenCI);
            document.getElementById('ci-number').addEventListener('input', updateHiddenCI);
            document.getElementById('client-form').addEventListener('submit', updateHiddenCI);
        });

        function setClientType(type) {
            const naturalTab = document.getElementById('tab-natural');
            const businessTab = document.getElementById('tab-business');
            const prefix = document.getElementById('ci-prefix');
            const nameLabel = document.getElementById('client-name-label');
            const idLabel = document.getElementById('client-id-label');

            const activeClasses = ['bg-brand-navy', 'text-white', 'shadow-sm'];
            const inactiveClasses = ['text-slate-600', 'hover:text-slate-900'];

            [naturalTab, businessTab].forEach(function(tab) {
                tab.classList.remove(...activeClasses);
                tab.classList.add(...inactiveClasses);
            });

            if (type === 'business') {
                businessTab.classList.remove(...inactiveClasses);
                businessTab.classList.add(...activeClasses);

                if (!['J', 'G'].includes(prefix.value)) {
                    prefix.value = 'J';
                }

                nameLabel.innerHTML = 'Razón Social <span class="text-rose-500">*</span>';
                idLabel.textContent = 'RIF';
            } else {
                naturalTab.classList.remove(...inactiveClasses);
                naturalTab.classList.add(...activeClasses);

                if (!['V', 'E'].includes(prefix.value)) {
                    prefix.value = 'V';
                }

                nameLabel.innerHTML = 'Nombre Completo <span class="text-rose-500">*</span>';
                idLabel.textContent = 'Cédula / ID Documento';
            }

            updateHiddenCI();
        }

        function updateHiddenCI() {
            const prefix = document.getElementById('ci-prefix').value;
            const number = document.getElementById('ci-number').value.trim();
            const hidden = document.getElementById('clientCI');

            hidden.value = number ? `${prefix}-${number}` : '';
        }
    </script>
@endsection
