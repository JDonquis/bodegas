@extends('layout.app')

@section('content')
<!-- Header Section -->
<section class="mb-10 flex items-end gap-4">
    <a href="{{ route('products') }}" class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-primary">
        <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2">Editar Producto</h1>
        <p class="text-on-surface-variant font-body">Actualiza la información técnica del artículo.</p>
    </div>
</section>

<div class="max-w-2xl">
    <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm border border-outline-variant/30">
        <form method="POST" action="{{ route('products.update', ['product' => $product->id]) }}" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Tasa BCV Info -->
                <div class="bg-secondary-container/10 p-4 rounded-2xl flex items-center justify-between border border-secondary/20">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary">trending_up</span>
                        <span class="text-sm font-bold text-primary font-headline">Tasa BCV Actual:</span>
                    </div>
                    <span class="text-lg font-extrabold text-secondary tracking-tight">{{ number_format($usdRate ?? 0, 2) }} Bs/USD</span>
                    <input type="hidden" id="usd-rate" value="{{ $usdRate ?? 0 }}">
                </div>

                <!-- Código de Barras -->
                <div class="space-y-2">
                    <label for="barcode" class="text-sm font-bold text-primary ml-1">Código de Barras</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">barcode_scanner</span>
                        <input type="text" name="barcode" id="barcode"
                            value="{{ $product->barcode }}"
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                            placeholder="Código de barras">
                    </div>
                </div>

                <!-- Nombre del Producto -->
                <div class="space-y-2">
                    <label for="productName" class="text-sm font-bold text-primary ml-1">Nombre del Producto</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">package_2</span>
                        <input type="text" name="productName" id="productName" required autofocus
                            value="{{ $product->name }}"
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                            placeholder="Nombre del producto">
                    </div>
                </div>

                <!-- Tipo de Venta -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-primary ml-1">Tipo de Venta *</label>
                    <div class="bg-surface-container-low p-1.5 rounded-2xl flex gap-1">
                        <button type="button" onclick="setSaleType('unit')" id="btn-unit" 
                                class="flex-1 py-3 text-sm font-black rounded-xl transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">inventory_2</span>
                            Por Unidad
                        </button>
                        <button type="button" onclick="setSaleType('weight')" id="btn-weight" 
                                class="flex-1 py-3 text-sm font-black rounded-xl transition-all text-outline hover:text-primary flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">scale</span>
                            Por Peso (gramos)
                        </button>
                    </div>
                    <input type="hidden" name="saleType" id="saleType" value="{{ $product->sale_type }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Precio de Venta USD -->
                    <div class="space-y-2">
                        <label for="sellPrice" class="text-sm font-bold text-primary ml-1" id="label-usd">Precio USD</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">attach_money</span>
                            <input type="number" step="0.01" min="0" name="sellPrice" id="sellPrice" required
                                value="{{ $product->sale_type === 'weight' ? $product->price_per_kg : $product->sell_price }}"
                                class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                                placeholder="0.00">
                        </div>
                    </div>

                    <!-- Precio de Venta BS -->
                    <div class="space-y-2">
                        <label for="sellPriceBs" class="text-sm font-bold text-primary ml-1" id="label-bs">Precio Bs</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">account_balance_wallet</span>
                            <input type="number" step="0.01" min="0" name="sellPriceBs" id="sellPriceBs"
                                value="{{ $product->sell_price_bs }}"
                                class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/40 transition-all"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-outline-variant/30 flex items-center justify-end gap-4">
                <a href="{{ route('products') }}" class="px-6 py-3 rounded-full text-sm font-bold text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-secondary hover:bg-on-secondary-container text-white px-8 py-4 rounded-full flex items-center gap-2 transition-all font-headline font-bold shadow-lg shadow-secondary/20 transform active:scale-95">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    Actualizar Producto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const usdInput = document.getElementById('sellPrice');
    const bsInput = document.getElementById('sellPriceBs');
    const rate = parseFloat(document.getElementById('usd-rate').value) || 0;
    const currentType = document.getElementById('saleType').value;
    updateSaleTypeButtons(currentType);

    if (rate > 0) {
        usdInput.addEventListener('input', function() {
            const usd = parseFloat(this.value) || 0;
            bsInput.value = (usd * rate).toFixed(2);
        });

        bsInput.addEventListener('input', function() {
            const bs = parseFloat(this.value) || 0;
            usdInput.value = (bs / rate).toFixed(2);
        });
    }
});

function setSaleType(type) {
    document.getElementById('saleType').value = type;
    updateSaleTypeButtons(type);
    updatePriceLabels(type);
}

function updateSaleTypeButtons(type) {
    const btnUnit = document.getElementById('btn-unit');
    const btnWeight = document.getElementById('btn-weight');
    
    if (type === 'weight') {
        btnWeight.className = 'flex-1 py-3 text-sm font-black rounded-xl transition-all bg-primary text-white shadow-sm flex items-center justify-center gap-2';
        btnUnit.className = 'flex-1 py-3 text-sm font-black rounded-xl transition-all text-outline hover:text-primary flex items-center justify-center gap-2';
    } else {
        btnUnit.className = 'flex-1 py-3 text-sm font-black rounded-xl transition-all bg-primary text-white shadow-sm flex items-center justify-center gap-2';
        btnWeight.className = 'flex-1 py-3 text-sm font-black rounded-xl transition-all text-outline hover:text-primary flex items-center justify-center gap-2';
    }
}

function updatePriceLabels(type) {
    const labelUsd = document.getElementById('label-usd');
    const labelBs = document.getElementById('label-bs');
    
    if (type === 'weight') {
        labelUsd.textContent = 'Precio por Kg (USD)';
        labelBs.textContent = 'Precio por Kg (Bs)';
    } else {
        labelUsd.textContent = 'Precio USD';
        labelBs.textContent = 'Precio Bs';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const currentType = document.getElementById('saleType').value;
    updateSaleTypeButtons(currentType);
    updatePriceLabels(currentType);
});
</script>
@endsection
