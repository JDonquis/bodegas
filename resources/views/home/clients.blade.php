@extends('layout.app')

@section('content')
<!-- Clients Header Section -->
<section class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2 font-headline">Cartera de Clientes</h1>
        <p class="text-on-surface-variant font-body">Gestión de contactos y control de cuentas por cobrar.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
        <div class="relative flex-1 sm:w-64 group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
            <input class="w-full bg-surface-container-low border-none rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/60 font-body transition-all" 
                   placeholder="Buscar cliente..." 
                   type="text" 
                   name="search"
                   id="client-search"
                   value="{{ request('search') }}">
        </div>
        <a href="{{ route('clients.create') }}" class="bg-primary hover:bg-primary-container text-white px-6 py-3 rounded-full flex items-center justify-center gap-2 transition-all font-headline font-bold shadow-lg shadow-primary/20 transform active:scale-95">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Nuevo Cliente
        </a>
    </div>
</section>

<!-- Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-surface-container-low p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Total Clientes</p>
            <p class="text-3xl font-black font-headline text-primary">{{ $clients->total() }}</p>
        </div>
        <div class="bg-primary/5 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-primary text-3xl">groups</span>
        </div>
    </div>
    <div class="bg-error-container/20 p-6 rounded-3xl flex items-center justify-between border border-error/10">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-error mb-1">Deuda Total</p>
            <p class="text-3xl font-black font-headline text-error">{{ number_format($clients->sum('debt'), 2) }}$</p>
        </div>
        <div class="bg-error/5 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-error text-3xl">money_off</span>
        </div>
    </div>
    <div class="bg-secondary-container/20 p-6 rounded-3xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-on-secondary-container mb-1">Clientes Solventes</p>
            <p class="text-3xl font-black font-headline text-secondary">{{ $clients->where('debt', 0)->count() }}</p>
        </div>
        <div class="bg-secondary/10 p-4 rounded-2xl">
            <span class="material-symbols-outlined text-secondary text-3xl">check_circle</span>
        </div>
    </div>
</div>

<!-- Clients Table Canvas -->
<div class="bg-surface-container-lowest rounded-[2.5rem] overflow-hidden shadow-sm border border-outline-variant/30">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-[10px] uppercase tracking-[0.2em] font-black">
                    <th class="px-8 py-5">Cliente</th>
                    <th class="px-4 py-5">Contacto</th>
                    <th class="px-4 py-5">Dirección</th>
                    <th class="px-4 py-5 text-right">Estado de Cuenta</th>
                    <th class="px-8 py-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="clients-table-body" class="text-sm font-body divide-y divide-surface-container">
                @foreach ($clients as $client)
                <tr class="hover:bg-surface-container/30 transition-all group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold">
                                {{ substr($client->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-black text-primary text-base leading-none">{{ $client->name }}</span>
                                <span class="text-[10px] text-outline font-mono mt-1 uppercase tracking-tighter">CI: {{ $client->ci ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-5">
                        <div class="flex items-center gap-2 text-on-surface-variant">
                            <span class="material-symbols-outlined text-sm">phone</span>
                            <span class="font-medium">{{ $client->phone_number ?? '---' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-5 max-w-[200px] truncate">
                        <span class="text-outline text-xs italic">{{ $client->address ?? 'No registrada' }}</span>
                    </td>
                    <td class="px-4 py-5 text-right">
                        @if($client->debt > 0)
                            <div class="flex flex-col items-end">
                                <span class="text-error font-black text-base">{{ number_format($client->debt, 2) }}$</span>
                                <span class="text-[9px] font-black text-error/60 uppercase tracking-widest">En deuda</span>
                            </div>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-secondary-container text-secondary uppercase tracking-widest">
                                Solvente
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="p-2 hover:bg-primary-container/10 text-primary rounded-xl transition-all" title="Editar">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </a>
                            <button onclick="deleteClient('{{ route('clients.delete', ['client' => $client->id]) }}')" class="p-2 hover:bg-error-container/40 text-error rounded-xl transition-all" title="Eliminar">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-8 py-6 bg-surface-container-low flex flex-col sm:flex-row justify-between items-center gap-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant border-t border-outline-variant/20">
        <p>Mostrando {{ $clients->firstItem() ?? 0 }} - {{ $clients->lastItem() ?? 0 }} de {{ $clients->total() }} clientes</p>
        
        <div class="flex items-center gap-1">
            <a href="{{ $clients->url(1) }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-surface-container transition-colors {{ $clients->onFirstPage() ? 'opacity-30 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_left</span>
            </a>
            @foreach ($clients->getUrlRange(max(1, $clients->currentPage() - 2), min($clients->lastPage(), $clients->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all {{ $page == $clients->currentPage() ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-110' : 'hover:bg-surface-container' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $clients->url($clients->lastPage()) }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-surface-container transition-colors {{ !$clients->hasMorePages() ? 'opacity-30 pointer-events-none' : '' }}">
                <span class="material-symbols-outlined text-lg">keyboard_double_arrow_right</span>
            </a>
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
function deleteClient(url) {
    if (confirm('¿Está seguro de eliminar este cliente? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('actions-form-delete');
        form.action = url;
        form.submit();
    }
}

// Búsqueda en tiempo real
document.getElementById('client-search')?.addEventListener('input', function() {
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(() => {
        const url = new URL(window.location.href);
        url.searchParams.set('search', this.value);
        window.location.href = url.toString();
    }, 800);
});
</script>
@endsection
