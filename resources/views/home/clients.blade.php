@extends('layout.home')

@section('content')
<div class="card">
    <div class=" d-flex justify-content-between align-items-center ">
        <h5 class="card-header">Clientes</h5>
        <a type="button" class="btn btn-primary text-white" href="{{ route('clients.create') }}" style="height: 70%; margin-right:30px;">
          <i class='bx bx-plus'></i>
          Crear cliente
        </a>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Fecha de registro</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Nro Teléfono</th>
            <th>Dirección</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @php
            Carbon\Carbon::setLocale('es');
          @endphp
          @foreach ($clients as $client )
            <tr>
              <td>
                <button type="button" @if($client->id == 1) {{ 'disabled' }} @endif onclick="deleteClient({{ $client->id }})" class="btn btn-outline-danger p-1">
                  <i class='bx bx-trash' ></i>
                </button>
                <a href="{{ route('clients.edit', ['client' => $client->id]) }}" 
                    @if($client->id == 1) 
                        onclick="event.preventDefault();"
                        style="pointer-events: none; color: gray; text-decoration: none;"
                    @endif 
                    id="update-btn" class="btn btn-outline-primary p-1">
                     <i class='bx bx-pencil'></i>
                 </a>
                
              </td>
              <td>
                
                {{ ucfirst($client->created_at->translatedFormat('F j, Y')) }}
              </td>
              <td><span class="text-primary">{{ $client->name }}</span></td>
    
              <td>{{ $client->ci ?? 'Sin registrar' }}</td>
              <td>{{ $client->phone_number ?? 'Sin registrar' }}</td>
              <td>{{ $client->address ?? 'Sin registrar' }}</td>
              
            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        <p class="m-0 p-0  align-self-center"> Total: {{ $clients->total() }}</p>
          {{-- Enlace a la primera página --}}
          <li class="page-item {{ $clients->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $clients->url(1) }}">
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          <li class="page-item {{ $clients->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $clients->previousPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          @for ($i = 1; $i <= $clients->lastPage(); $i++)
            <li class="page-item {{ $i == $clients->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $clients->url($i) }}">{{ $i }}</a>
            </li>
           @endfor
  
          {{-- Enlace a la página siguiente --}}
          <li class="page-item {{ $clients->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $clients->nextPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          <li class="page-item {{ $clients->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $clients->url($clients->lastPage()) }}">
                  <i class="tf-icon bx bx-chevrons-right bx-sm"></i>
              </a>
          </li>
      </ul>
  </nav
</div>



  {{-- Modal --}}
 

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

function deleteClient($clientID){

  if(confirm('Esta seguro de eliminar este cliente?')){
      
      const form = document.getElementById('actions-form-delete'); 
      
      form.action = `/home/clientes/${$clientID}`; 
  
      form.submit();
      
      
      }
      else{
        return 0;
      }
  

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

function buildModal($entries){

  let dateEntry = document.getElementById('date-entry')
  let tableEntryDetailsBody = document.getElementById('entries-details')
  let deleteBtn = document.getElementById('delete-btn')
  let updateBtn = document.getElementById('update-btn')


  

  
  dateEntry.innerHTML = $entries[0].created_at;
  deleteBtn.setAttribute('data-entryID',$entries[0].entry_general_id);
  const entryID = deleteBtn.getAttribute('data-entryID');

  deleteBtn.addEventListener('click',function (){

    if(confirm('Esta seguro de eliminar esta entrada?')){
      
    const form = document.getElementById('actions-form-delete'); 
    
    form.action = `/home/entradas/${entryID}`; 

    form.submit();
    
    
    }
    else{
      return 0;
    }

  })

  updateBtn.addEventListener('click',function (){

    window.location.href=`/home/entradas/editar/${entryID}`
})
  
  
  let results = $entries.map(entry => {

    const formattedExpiredDate = formatDate(entry.expired_date);
        return `<tr>
                    <td>
                      ${entry.product.name}
                    </td>
                    <td>${entry.quantity}</td>
                    <td>
                      ${formattedExpiredDate}
                    </td>
                  </tr>  `;
            }).join('');

            tableEntryDetailsBody.innerHTML = results;

}

</script>


@endsection