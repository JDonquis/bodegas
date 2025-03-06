@extends('layout.home')

@section('content')
<div class="card">
    <div class=" d-flex justify-content-between align-items-center ">
        <h5 class="card-header">Inventario</h5>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
              <th>Producto</th>
            <th>Stock</th>
            <th>Entradas</th>
            <th>Salidas</th>
            <th>Gastado</th>
            <th>Vendido</th>
            <th>Ganancias</th>


          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @foreach ($inventories as $inventory )
            <tr>
              <td>
                    <button type="button" onclick="showDetail(this)" class="btn" inventory="{{ $inventory->id }}" data-bs-toggle="modal" data-bs-target="#modalScrollable">
                        <i class='bx bx-detail' ></i>
                    </button>
                    {{ $inventory->product->name }}
              </td>
              <td>
                {{ $inventory->stock }}
              </td>
              <td>
                {{ $inventory->entries }}
              </td>
              <td>
                {{ $inventory->outputs }}
              </td>
              <td>
                {{ $inventory->expense }}$
              </td>
              <td>
                {{ $inventory->sold }}$
              </td>
              <td>
                <span class="text-primary" style="font-weight: bold;">{{ $inventory->profits }}$</span>
              </td>
              {{-- <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0);"
                      ><i class="bx bx-edit-alt me-1"></i> Edit</a
                    >
                    <a class="dropdown-item" href="javascript:void(0);"
                      ><i class="bx bx-trash me-1"></i> Delete</a
                    >
                  </div>
                </div>
              </td> --}}
            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        <p class="m-0 p-0  align-self-center"> Total: {{ $inventories->total() }}</p>
          {{-- Enlace a la primera página --}}
          <li class="page-item {{ $inventories->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $inventories->url(1) }}">
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          <li class="page-item {{ $inventories->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $inventories->previousPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          @for ($i = 1; $i <= $inventories->lastPage(); $i++)
            <li class="page-item {{ $i == $inventories->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $inventories->url($i) }}">{{ $i }}</a>
            </li>
           @endfor
  
          {{-- Enlace a la página siguiente --}}
          <li class="page-item {{ $inventories->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $inventories->nextPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          <li class="page-item {{ $inventories->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $inventories->url($inventories->lastPage()) }}">
                  <i class="tf-icon bx bx-chevrons-right bx-sm"></i>
              </a>
          </li>
      </ul>
  </nav
</div>



  {{-- Modal --}}
  <div class="modal fade" id="modalScrollable" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-4">
          <h5 class="modal-title" id="modalScrollableTitle">Inventario detallado</h5>
        </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive text-nowrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Stock</th>
                  <th>Vencimiento</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0" id="inventory-details">
                
              </tbody>
            </table>
          </div>
        </div>
           <div class="modal-footer" >
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  
@endsection

@section('scripts')

<script>

function showDetail($btn){

  let inventoryID = $btn.getAttribute('inventory');

  fetch(`/home/inventario/${inventoryID}`,{
      method: 'GET',
      headers:{
        "Content-Type" : "application/json",
        "X-Requested-With" : "XMLHttpRequest"
      },
    })
        .then(response => response.json())
        .then(data => {

          console.log(data)
          buildModal(data.details)
        })
        .catch(error => {
            console.error(error);
        });

}

function formatDate(dateString) {
    const date = new Date(dateString);
    const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];
    const month = monthNames[date.getMonth()]; // Nombre completo del mes
    const day = date.getDate(); // Día del mes
    const year = date.getFullYear(); // Año
    return `${month} ${day} ${year}`; // Formato "f m Y"
}

function buildModal($details){

  let tableInventoryDetail = document.getElementById('inventory-details')
  
  
  let results = $details.map(detail => {

    const formattedExpiredDate = formatDate(detail.expired_date);
        return `<tr>
                    <td>
                      ${detail.product.name}
                    </td>
                    <td>${detail.stock}</td>
                    <td>
                      ${formattedExpiredDate}
                    </td>
                  </tr>  `;
            }).join('');

            tableInventoryDetail.innerHTML = results;

}

</script>


@endsection