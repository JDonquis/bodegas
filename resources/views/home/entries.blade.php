@extends('layout.home')

@section('content')
<div class="card">
    <div class=" d-flex justify-content-between align-items-center ">
        <h5 class="card-header">Entradas</h5>
        <a type="button" class="btn btn-primary text-white" href="{{ route('entries.create') }}" style="height: 70%; margin-right:30px;">
          <i class='bx bx-plus'></i>
          Crear
        </a>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th>Fecha de registro</th>
            <th>Nro Productos</th>
            <th>Costo total</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @php
            Carbon\Carbon::setLocale('es');
          @endphp
          @foreach ($entries as $entry )
            <tr>
              
              <td>
                <button type="button" onclick="showDetailEntry(this)" class="btn" entry="{{ $entry->id }}" data-bs-toggle="modal" data-bs-target="#modalScrollable">
                  <i class='bx bx-detail' ></i>
                </button>
                {{ ucfirst($entry->created_at->translatedFormat('F j, Y')) }}
              </td>
              <td>{{ $entry->quantity_products }}</td>
              <td>{{ $entry->total_expense }}$</td>

            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        <p class="m-0 p-0  align-self-center"> Total: {{ $entries->total() }}</p>
          {{-- Enlace a la primera página --}}
          <li class="page-item {{ $entries->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $entries->url(1) }}">
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          <li class="page-item {{ $entries->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $entries->previousPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          @for ($i = 1; $i <= $entries->lastPage(); $i++)
            <li class="page-item {{ $i == $entries->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $entries->url($i) }}">{{ $i }}</a>
            </li>
           @endfor
  
          {{-- Enlace a la página siguiente --}}
          <li class="page-item {{ $entries->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $entries->nextPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          <li class="page-item {{ $entries->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $entries->url($entries->lastPage()) }}">
                  <i class="tf-icon bx bx-chevrons-right bx-sm"></i>
              </a>
          </li>
      </ul>
  </nav
</div>



  {{-- Modal --}}
  <div class="modal fade" id="modalScrollable" tabindex="-1" style="display: none;"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-4">
          <h5 class="modal-title" id="modalScrollableTitle">Entrada detallada</h5>
          <p class="m-0" id="date-entry"></p>
        </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive text-nowrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Prec. Compra</th>
                  <th>Nro Lote</th>
                  <th>Vencimiento</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0" id="entries-details">
                
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" >
          <div class="position-absolute w-full" method="" action="" style="left: 30px;">
            <button type="button"  id="delete-btn" onclick="deleteEntry(this)" data-entryID="" class="btn btn-outline-danger"><i class='bx bx-trash' ></i></button>
            <button type="button"  id="update-btn" onclick="updateEntry(this)" data-entryID="" class="btn btn-outline-primary "><i class='bx bx-pencil' ></i></button>
          </div>
          <div>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <form action="" id="actions-form-delete" class="d-none" method="POST">
    @csrf
    @method('DELETE')
  </form>
  <form action="" id="actions-form-update" class="d-none" method="POST">
    @csrf
    @method('PUT')
  </form>
  
@endsection

@section('scripts')

<script>

function showDetailEntry($btn){

  let entryID = $btn.getAttribute('entry');

  fetch(`/home/entradas/${entryID}`,{
      method: 'GET',
      headers:{
        "Content-Type" : "application/json",
        "X-Requested-With" : "XMLHttpRequest"
      },
    })
        .then(response => response.json())
        .then(data => {

          console.log(data)
          buildModal(data.entries)
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

function deleteEntry($element){

  const entryID = $element.getAttribute('data-entryID');

  if(confirm('Esta seguro de eliminar esta entrada?')){
      
    const form = document.getElementById('actions-form-delete'); 
    
    form.action = `/home/entradas/${entryID}`; 

    form.submit();
  }

  return 0;

}

function updateEntry($element){

  const entryID = $element.getAttribute('data-entryID');

  window.location.href=`/home/entradas/editar/${entryID}`

}

function buildModal($entries){

  let dateEntry = document.getElementById('date-entry')
  let tableEntryDetailsBody = document.getElementById('entries-details')
  let deleteBtn = document.getElementById('delete-btn')
  let updateBtn = document.getElementById('update-btn')

  
  dateEntry.innerHTML = $entries[0].created_at;
  deleteBtn.setAttribute('data-entryID',$entries[0].entry_general_id);
  updateBtn.setAttribute('data-entryID',$entries[0].entry_general_id);

  
  let results = $entries.map(entry => {

    const formattedExpiredDate = formatDate(entry.expired_date);
        return `<tr>
                    <td>
                      ${entry.product.name}
                    </td>
                    <td>${entry.quantity}</td>
                    <td>${entry.cost}$</td>
                    <td>${entry.lote_number}</td>
                    <td>
                      ${formattedExpiredDate}
                    </td>
                  </tr>  `;
            }).join('');

            tableEntryDetailsBody.innerHTML = results;

}

</script>


@endsection