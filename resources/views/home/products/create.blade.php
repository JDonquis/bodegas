@extends('layout.app')

@section('styles')
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-4 pb-24 lg:pb-0">

        <!-- Cabecera de página -->
        <section class="flex items-center gap-3">
            <a href="{{ route('products') }}" aria-label="Volver al catálogo"
                class="w-9 h-9 shrink-0 rounded-xl bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                    <span class="text-[10px] font-label font-semibold uppercase tracking-wider text-secondary">En línea</span>
                </div>
                <h1 class="text-2xl font-extrabold font-headline tracking-tight text-on-surface leading-tight">Nuevo
                    Producto</h1>
                <p class="text-xs text-on-surface-variant">Registra un nuevo artículo en el catálogo</p>
            </div>
        </section>

        <!-- Tasa BCV -->
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-secondary-container/40 via-secondary-fixed/20 to-surface-container-lowest border border-secondary-container/60 p-3.5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-secondary-container/50 border border-secondary-container flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[22px]">trending_up</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-label font-bold tracking-wider text-on-surface-variant uppercase">Tasa
                                oficial BCV</span>
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-on-secondary text-[9px] font-extrabold uppercase tracking-tight">Hoy</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant font-medium">Actualización automática</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-lg font-black font-headline text-secondary tracking-tight leading-none">
                        {{ number_format($usdRate ?? 0, 2) }} <span class="text-xs font-bold">Bs/$</span>
                    </div>
                    <span class="text-[10px] text-secondary/80 font-semibold">Tasa del día activa</span>
                </div>
            </div>
            <input type="hidden" id="usd-rate" value="{{ $usdRate ?? 0 }}">
        </section>

        <!-- Formulario -->
        <form method="POST" action="{{ route('products.store') }}" id="product-form" class="space-y-4">
            @csrf

            <!-- Código de barras -->
            <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline-variant/20 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <label for="barcode"
                        class="text-[11px] font-bold text-on-surface uppercase tracking-wider flex items-center gap-1.5">
                        <span>Código de Barras</span>
                        <span class="text-on-surface-variant font-normal text-[10px] normal-case">(Opcional)</span>
                    </label>
                    <button type="button" id="btn-generate-code"
                        class="text-[11px] font-bold text-primary hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">autorenew</span>
                        Generar
                    </button>
                </div>
                <div
                    class="relative flex items-center rounded-xl bg-surface-container-low border border-outline-variant/30 focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/20 transition-all">
                    <div class="pl-3.5 pr-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[20px]">barcode_scanner</span>
                    </div>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                        placeholder="7591031000123..."
                        class="w-full min-w-0 bg-transparent border-0 py-3 text-sm font-mono font-medium text-on-surface placeholder:text-on-surface-variant/40 focus:ring-0">
                    <button type="button" id="scan-barcode"
                        class="m-1.5 shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-surface-container hover:bg-surface-container-high text-on-surface rounded-lg text-[11px] font-bold active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                        <span>Escanear</span>
                    </button>
                </div>
                @error('barcode')
                    <p class="text-[11px] text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información básica -->
            <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline-variant/20 shadow-sm space-y-3.5">
                <div class="space-y-1.5">
                    <label for="productName" class="block text-[11px] font-bold text-on-surface uppercase tracking-wider">
                        Nombre del Producto <span class="text-error">*</span>
                    </label>
                    <div
                        class="relative flex items-center rounded-xl bg-surface-container-low border border-outline-variant/30 focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/20 transition-all">
                        <div class="pl-3.5 pr-2 text-on-surface-variant">
                            <span class="material-symbols-outlined text-[20px]">package_2</span>
                        </div>
                        <input type="text" name="productName" id="productName" value="{{ old('productName') }}"
                            placeholder="Ej. Harina PAN 1kg, Amoxicilina 500mg" required
                            class="w-full bg-transparent border-0 py-3 text-sm font-semibold text-on-surface placeholder:text-on-surface-variant/40 placeholder:font-normal focus:ring-0">
                    </div>
                    @error('productName')
                        <p class="text-[11px] text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tipo de venta -->
            <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline-variant/20 shadow-sm space-y-2">
                <label class="block text-[11px] font-bold text-on-surface uppercase tracking-wider">
                    Tipo de Venta <span class="text-error">*</span>
                </label>
                <div class="grid grid-cols-2 p-1 bg-surface-container rounded-xl gap-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="saleType" value="unit" class="peer sr-only"
                            @checked(old('saleType', 'unit') === 'unit')>
                        <div
                            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface peer-checked:bg-primary peer-checked:text-on-primary peer-checked:shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                            <span>Por Unidad</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="saleType" value="weight" class="peer sr-only"
                            @checked(old('saleType') === 'weight')>
                        <div
                            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface peer-checked:bg-primary peer-checked:text-on-primary peer-checked:shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">scale</span>
                            <span>Por Peso (Kg/g)</span>
                        </div>
                    </label>
                </div>
                @error('saleType')
                    <p class="text-[11px] text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precios -->
            <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline-variant/20 shadow-sm space-y-3.5">
                <div class="flex items-center justify-between border-b border-outline-variant/20 pb-2">
                    <h2 class="text-[11px] font-black text-on-surface uppercase tracking-wider">Estructura de Precios
                    </h2>
                    <span class="text-[10px] font-semibold text-secondary flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                        Auto-conversión
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="sellPrice" id="label-usd" class="block text-[11px] font-bold text-on-surface">
                            Precio USD ($) <span class="text-error">*</span>
                        </label>
                        <div
                            class="relative flex items-center rounded-xl bg-surface-container-low border border-outline-variant/30 focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/20">
                            <div class="pl-3.5 pr-1 text-on-surface-variant font-extrabold text-base">$</div>
                            <input type="number" step="0.01" min="0" name="sellPrice" id="sellPrice"
                                value="{{ old('sellPrice') }}" placeholder="0.00" required
                                class="w-full bg-transparent border-0 py-3 pr-3 text-base font-extrabold text-on-surface placeholder:text-on-surface-variant/30 focus:ring-0">
                        </div>
                        @error('sellPrice')
                            <p class="text-[11px] text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="sellPriceBs" id="label-bs" class="block text-[11px] font-bold text-on-surface">
                                Precio en Bolívares (Bs)
                            </label>
                            <span
                                class="text-[9px] bg-surface-container text-on-surface-variant px-1.5 py-0.5 rounded font-medium">Tasa
                                BCV</span>
                        </div>
                        <div
                            class="relative flex items-center rounded-xl bg-secondary-container/20 border border-secondary-container/60 focus-within:border-secondary focus-within:ring-2 focus-within:ring-secondary/20">
                            <div class="pl-3.5 pr-1 text-secondary font-extrabold text-xs">Bs</div>
                            <input type="number" step="0.01" min="0" name="sellPriceBs" id="sellPriceBs"
                                value="{{ old('sellPriceBs') }}" placeholder="0.00"
                                class="w-full bg-transparent border-0 py-3 pr-3 text-base font-black text-secondary placeholder:text-secondary/40 focus:ring-0">
                        </div>
                        @error('sellPriceBs')
                            <p class="text-[11px] text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center justify-between px-1 pt-1 text-[10px] text-on-surface-variant">
                    <span>Conversión automática · Tasa {{ number_format($usdRate ?? 0, 2) }} Bs</span>
                    <span class="font-medium">IVA no incluido</span>
                </div>
            </div>

            <!-- Control de existencias -->
            <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline-variant/20 shadow-sm space-y-3">
                <h2 class="text-[11px] font-black text-on-surface uppercase tracking-wider">Control de Existencias</h2>
                <div class="space-y-1.5">
                    <label for="lowStockThreshold"
                        class="block text-[11px] font-bold text-on-surface uppercase tracking-wider">
                        Alerta mínima <span class="text-error">*</span>
                    </label>
                    <div
                        class="relative flex items-center rounded-xl bg-amber-50 border border-amber-200 focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-500/20 transition-all">
                        <div class="pl-3.5 pr-2 text-amber-700">
                            <span class="material-symbols-outlined text-[20px]">warning</span>
                        </div>
                        <input type="number" step="1" min="0" name="lowStockThreshold" id="lowStockThreshold"
                            value="{{ old('lowStockThreshold', \App\Models\Product::DEFAULT_LOW_STOCK_THRESHOLD) }}"
                            placeholder="10" required
                            class="w-full bg-transparent border-0 py-3 pr-3 text-sm font-bold text-amber-800 placeholder:text-amber-700/40 focus:ring-0">
                        <span class="pr-3.5 text-[11px] font-semibold text-amber-700/80">uds</span>
                    </div>
                    <p class="text-[11px] text-amber-700/80 px-1">
                        Umbral usado para marcar productos con bajo stock en el catálogo.
                    </p>
                    @error('lowStockThreshold')
                        <p class="text-[11px] text-error px-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </form>

        <!-- Barra de acciones -->
        <div class="fixed inset-x-0 z-30 px-4 py-2.5 bg-surface-container-lowest/90 backdrop-blur-md border-t border-outline-variant/20 shadow-[0_-4px_20px_rgba(11,30,54,0.08)] lg:static lg:z-auto lg:mt-1 lg:px-0 lg:py-0 lg:bg-transparent lg:border-0 lg:shadow-none"
            style="bottom: calc(4rem + env(safe-area-inset-bottom));">
            <div class="flex items-center gap-3 max-w-2xl mx-auto">
                <a href="{{ route('products') }}"
                    class="w-2/5 py-3 px-3 rounded-xl border border-outline-variant/40 text-on-surface-variant font-bold text-xs hover:bg-surface-container active:scale-95 transition-all text-center">
                    Descartar
                </a>
                <button type="submit" form="product-form"
                    class="w-3/5 py-3 px-4 rounded-xl bg-primary text-on-primary font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-primary/20 active:scale-[0.98] hover:bg-primary-container transition-all">
                    <span class="material-symbols-outlined text-[18px] text-secondary-fixed">save</span>
                    <span class="tracking-wide">Guardar Producto</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rate = parseFloat(document.getElementById('usd-rate').value) || 0;
            const usdInput = document.getElementById('sellPrice');
            const bsInput = document.getElementById('sellPriceBs');
            const labelUsd = document.getElementById('label-usd');
            const labelBs = document.getElementById('label-bs');
            const barcodeInput = document.getElementById('barcode');
            const generateButton = document.getElementById('btn-generate-code');
            const scanButton = document.getElementById('scan-barcode');

            function updatePriceLabels() {
                const selected = document.querySelector('input[name="saleType"]:checked');
                const type = selected ? selected.value : 'unit';

                if (type === 'weight') {
                    labelUsd.innerHTML = 'Precio por Kg (USD) <span class="text-error">*</span>';
                    labelBs.textContent = 'Precio por Kg (Bs)';
                } else {
                    labelUsd.innerHTML = 'Precio USD ($) <span class="text-error">*</span>';
                    labelBs.textContent = 'Precio en Bolívares (Bs)';
                }
            }

            function syncBsPrice() {
                const usd = parseFloat(usdInput.value);

                if (!isNaN(usd) && usd >= 0 && rate > 0) {
                    bsInput.value = (usd * rate).toFixed(2);
                } else {
                    bsInput.value = '';
                }
            }

            function syncUsdPrice() {
                const bs = parseFloat(bsInput.value);

                if (!isNaN(bs) && bs >= 0 && rate > 0) {
                    usdInput.value = (bs / rate).toFixed(2);
                } else {
                    usdInput.value = '';
                }
            }

            document.querySelectorAll('input[name="saleType"]').forEach(function(radio) {
                radio.addEventListener('change', updatePriceLabels);
            });

            if (rate > 0) {
                usdInput.addEventListener('input', syncBsPrice);
                bsInput.addEventListener('input', syncUsdPrice);
                if (usdInput.value) {
                    syncBsPrice();
                }
            }

            if (generateButton && barcodeInput) {
                generateButton.addEventListener('click', function() {
                    barcodeInput.value = '759' + Math.floor(100000000 + Math.random() * 900000000);
                    barcodeInput.focus();
                });
            }

            if (scanButton && barcodeInput) {
                scanButton.addEventListener('click', function() {
                    barcodeInput.focus();
                    const length = barcodeInput.value.length;
                    barcodeInput.setSelectionRange(length, length);
                });
            }

            updatePriceLabels();
        });
    </script>
@endsection
