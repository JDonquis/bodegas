@extends('layout.app')

@section('content')
    <!-- Products Header Section -->
    <section class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2">Productos</h1>
            <p class="text-on-surface-variant font-body">Catálogo maestro de artículos disponibles en bodega.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <form action="{{ route('products') }}" method="GET" id="search-form" class="relative flex-1 sm:w-64">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input
                    class="w-full bg-surface-container-low border-none rounded-xl py-2.5 pl-10 pr-10 text-sm focus:ring-2 focus:ring-secondary/20 placeholder:text-on-surface-variant/60 font-body"
                    placeholder="Buscar productos..." type="search" name="search" id="html5-search-input"
                    value="{{ request('search') }}">
                @if (request('search'))
                    <button type="button" id="clear-search"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-error transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                @endif
            </form>
            <a href="{{ route('products.create') }}"
                class="bg-primary hover:bg-primary-container text-white px-6 py-3 rounded-full flex items-center justify-center gap-2 transition-all font-headline font-bold shadow-lg shadow-primary/20 transform active:scale-95">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Nuevo Producto
            </a>
        </div>
    </section>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div
            class="bg-surface-container-low p-6 rounded-xl flex items-center justify-between border border-outline-variant/30">
            <div>
                <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-1">Total Productos</p>
                <p class="text-3xl font-headline font-extrabold text-primary">{{ $products->total() }}</p>
            </div>
            <div class="bg-primary/5 p-3 rounded-lg">
                <span class="material-symbols-outlined text-primary text-3xl">inventory</span>
            </div>
        </div>
    </div>

    <!-- Product Table Canvas -->
    <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant/30">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">Acciones</th>
                        <th class="px-6 py-4">Cód. Barras</th>
                        <th class="px-6 py-4">Nombre del Producto</th>
                        <th class="px-6 py-4">Precio USD</th>
                        <th class="px-6 py-4">Precio Bs</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-body divide-y divide-surface-container">
                    @php
                        Carbon\Carbon::setLocale('es');
                    @endphp
                    @foreach ($products as $product)
                        <tr class="hover:bg-surface-container/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('products.edit', ['product' => $product->id]) }}"
                                        class="p-2 hover:bg-primary-container/10 text-primary rounded-lg transition-colors"
                                        title="Editar">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </a>
                                    <button
                                        onclick="deleteProduct('{{ route('products.delete', ['product' => $product->id]) }}')"
                                        class="p-2 hover:bg-error-container/40 text-error rounded-lg transition-colors"
                                        title="Eliminar">
                                        <span class="material-symbols-outlined text-xl">delete</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">
                                {{ $product->barcode ?? '---' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-primary">
                                {{ $product->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-primary-fixed text-primary rounded-full text-xs font-bold">
                                    {{ number_format($product->sell_price, 2) }}$
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full text-xs font-bold">
                                    {{ number_format($product->sell_price_bs, 2) }} Bs
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div
            class="px-6 py-4 bg-surface-container-low flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-medium text-on-surface-variant">
            <p>Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} productos
            </p>

            <div class="flex items-center gap-1">
                {{-- Enlace a la primera página --}}
                <a href="{{ $products->url(1) }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ $products->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                    <span class="material-symbols-outlined text-lg">keyboard_double_arrow_left</span>
                </a>

                {{-- Enlace a la página anterior --}}
                <a href="{{ $products->previousPageUrl() }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ $products->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </a>

                @foreach ($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $page == $products->currentPage() ? 'bg-primary text-white font-bold' : 'hover:bg-surface-container' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Enlace a la página siguiente --}}
                <a href="{{ $products->nextPageUrl() }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ !$products->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </a>

                {{-- Enlace a la última página --}}
                <a href="{{ $products->url($products->lastPage()) }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors {{ !$products->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('html5-search-input');
            const searchForm = document.getElementById('search-form');
            let timeout = null;

            if (searchInput) {
                // Colocar el cursor al final del texto al cargar
                const length = searchInput.value.length;
                searchInput.focus();
                searchInput.setSelectionRange(length, length);

                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        searchForm.submit();
                    }, 500); // 500ms de retraso para no saturar
                });
            }
        });

        function deleteProduct(url) {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                const form = document.getElementById('actions-form-delete');
                form.action = url;
                form.submit();
            }
        }

        if (document.getElementById('clear-search')) {
            document.getElementById('clear-search').addEventListener('click', function() {
                document.getElementById('html5-search-input').value = '';
                document.getElementById('search-form').submit();
            });
        }
    </script>
@endsection
