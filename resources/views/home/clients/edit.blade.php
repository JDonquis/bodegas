@extends('layout.app')

@section('content')
<!-- Header Section -->
<section class="mb-10 flex items-end gap-4">
    <a href="{{ route('clients') }}" class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-primary">
        <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2 font-headline">Editar Cliente</h1>
        <p class="text-on-surface-variant font-body">Actualiza el perfil y estado de cuenta del contacto.</p>
    </div>
</section>

<div class="max-w-4xl">
    <div class="bg-surface-container-lowest p-10 rounded-[3rem] shadow-sm border border-outline-variant/30">
        <form method="POST" action="{{ route('clients.update', ['client' => $client->id]) }}" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Nombre del Cliente -->
                <div class="space-y-2 md:col-span-2">
                    <label for="clientName" class="text-sm font-black text-primary ml-1 uppercase tracking-widest">Nombre Completo</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">person</span>
                        <input type="text" name="clientName" id="clientName" required autofocus
                            value="{{ $client->name }}"
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-on-surface-variant/40 transition-all font-bold"
                            placeholder="Nombre del cliente">
                    </div>
                </div>

                <!-- Cédula -->
                <div class="space-y-2">
                    <label for="clientCI" class="text-sm font-black text-primary ml-1 uppercase tracking-widest">Cédula / ID</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">badge</span>
                        <input type="text" name="clientCI" id="clientCI"
                            value="{{ $client->ci }}"
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-on-surface-variant/40 transition-all font-bold"
                            placeholder="Cédula">
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="space-y-2">
                    <label for="clientPhoneNumber" class="text-sm font-black text-primary ml-1 uppercase tracking-widest">Teléfono</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">phone</span>
                        <input type="text" name="clientPhoneNumber" id="clientPhoneNumber"
                            value="{{ $client->phone_number }}"
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-on-surface-variant/40 transition-all font-bold"
                            placeholder="Teléfono">
                    </div>
                </div>

                <!-- Deuda Actual -->
                <div class="space-y-2">
                    <label for="clientDebt" class="text-sm font-black text-primary ml-1 uppercase tracking-widest">Saldo Deudor ($)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-error">account_balance_wallet</span>
                        <input type="number" step="0.01" min="0" name="clientDebt" id="clientDebt"
                            value="{{ $client->debt }}"
                            class="w-full bg-error-container/10 border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-error/20 placeholder:text-on-surface-variant/40 transition-all font-black text-error"
                            placeholder="0.00">
                    </div>
                    <p class="text-[10px] text-error/60 ml-1 italic font-medium">Modifica este valor solo para ajustes manuales de cuenta.</p>
                </div>

                <!-- Dirección -->
                <div class="space-y-2 md:col-span-2">
                    <label for="clientAddress" class="text-sm font-black text-primary ml-1 uppercase tracking-widest">Dirección de Habitación</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-4 text-on-surface-variant">location_on</span>
                        <textarea name="clientAddress" id="clientAddress" rows="3"
                            class="w-full bg-surface-container-low border-none rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-on-surface-variant/40 transition-all font-medium"
                            placeholder="Dirección completa...">{{ $client->address }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-8 border-t border-outline-variant/30 flex items-center justify-end gap-4">
                <a href="{{ route('clients') }}" class="px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-secondary hover:bg-on-secondary-container text-white px-10 py-4 rounded-2xl flex items-center gap-3 transition-all font-headline font-black uppercase tracking-widest shadow-xl shadow-secondary/20 transform active:scale-95">
                    <span class="material-symbols-outlined">save</span>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
