@extends('layout.home')

@section('content')
<div class="card">
  
    <div class=" d-flex justify-content-between align-items-center ">
      
      <h5 class="card-header">Productos</h5>
      <form action="{{ route('products') }}" method="GET" id="search-form">
        <div class="input-group input-group-merge" style="max-width: 300px;">
            <input class="form-control" type="search" name="search" placeholder="Buscar..." id="html5-search-input" value="{{ request('search') }}">
            <button class="btn btn-outline-primary" type="submit"><i class='bx bx-search-alt-2' ></i></button>
            <button type="button" class="btn btn-outline-danger" id="clear-search">X</button>
        </div>
    </form>
    
 

      <a type="button" class="btn btn-primary text-white" href="{{ route('products.create') }}" style="height: 70%; margin-right:30px;">
          <i class='bx bx-plus'></i>
          Nuevo producto
        </a>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Fecha de registro</th>
            <th>Nombre</th>
            <th>Precio de venta</th>

          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @php
            Carbon\Carbon::setLocale('es');
          @endphp
          @foreach ($products as $product )
            <tr>
              <td>
                <button onclick="deleteProduct('{{ route('products.delete', ['product' => $product->id]) }}')" class="btn btn-outline-danger p-1">
                  <i class='bx bx-trash' ></i>
                </button>
                <a  href="{{ route('products.edit',['product' => $product->id]) }}"  id="update-btn"  class="btn btn-outline-primary p-1">
                  <i class='bx bx-pencil' ></i>
                </a>
              </td>
              <td>
                {{ ucfirst($product->created_at->translatedFormat('F j, Y, g:i A')) }}
              </td>
              <td><span class="text-primary">{{ $product->name}}</span></td>
              <td>{{ $product->sell_price }}$ </td>
            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        <p class="m-0 p-0  align-self-center"> Total: {{ $products->total() }}</p>
          {{-- Enlace a la primera página --}}
          <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $products->url(1) }}"> 
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $products->previousPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          @for ($i = 1; $i <= $products->lastPage(); $i++)
            <li class="page-item {{ $i == $products->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a>
            </li>
           @endfor
  
          {{-- Enlace a la página siguiente --}}
          <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $products->nextPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $products->url($products->lastPage()) }}">
                  <i class="tf-icon bx bx-chevrons-right bx-sm"></i>
              </a>
          </li>
      </ul>
    </nav>
</div>

<form action="" id="actions-form-delete" class="d-none" method="POST">
  @csrf
  @method('DELETE')
</form>

@endsection

@section('scripts')
<script>

function deleteProduct(url){

  if(confirm('Esta seguro de eliminar este producto?')){
  
  const form = document.getElementById('actions-form-delete'); 
  
  form.action = `${url}`; 
  
  form.submit();
  
}

return 0;

}

document.getElementById('clear-search').addEventListener('click', function() {
    document.getElementById('html5-search-input').value = ''; // Limpia el campo de búsqueda
    document.getElementById('search-form').submit(); // Envía el formulario para restablecer la búsqueda
});


</script>
@endsection