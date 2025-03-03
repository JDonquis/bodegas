@extends('layout.home')

@section('content')
<div class="card">
    <div class=" d-flex justify-content-between align-items-center ">
        <h5 class="card-header">Casos</h5>
        <a type="button" class="btn btn-primary text-white" href="{{ route('patients.create') }}" style="height: 70%; margin-right:30px;">
          <i class='bx bx-plus'></i>
          Atender nuevo caso
        </a>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Fecha de registro</th>
            <th>Paciente</th>
            <th>Atendido por:</th>
            <th>Tipo</th>
            <th>Enfermedad(es)</th>
            <th>Medicamentos asignados</th>
            <th>Observación</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @php
            Carbon\Carbon::setLocale('es');
          @endphp
          @foreach ($casePatients as $casePatient )
            <tr>
              <td>
                <button type="button" onclick="deleteCasePatient({{ $casePatient->id }})" class="btn btn-outline-danger p-1">
                  <i class='bx bx-trash' ></i>
                </button>
                
              </td>
              <td>
                
                {{ ucfirst($casePatient->created_at->translatedFormat('F j, Y, g:i A')) }}
              </td>
              <td>{{ $casePatient->patient->name . ' ' . $casePatient->patient->last_name . ' '}} <span class="text-primary">{{ $casePatient->patient->ci }}</span></td>
              <td>{{ $casePatient->user->charge . ': ' . $casePatient->user->name . ' ' . $casePatient->user->last_name }}</td>
              <td> <span class="{{ $casePatient->patient->type_patient_id == 1? 'text-primary': 'text-info' }}"  style="font-weight:bold">  {{ $casePatient->patient->typePatient->name }} </span> : {{ $casePatient->patient->career->name }}</td>
              <td>
                @if(count($casePatient->pathologies) == 0)
                  Sin diagnóstico.
                @endif

                @foreach ($casePatient->pathologies as $index => $pathology )
                  @if($index == count($casePatient->pathologies) - 1)
                    {{ $pathology->name }}.
                  @else
                    {{ $pathology->name }},
                  @endif
                
                  @endforeach
              </td>
              <td>
                @if($casePatient->output_general_id == 0)
                  Sin asignar.
                @else
                  @foreach ($casePatient->outputGeneral->outputs as $index => $output )
                    @if($index == count($casePatient->outputGeneral->outputs) - 1)
                      {{ $output->product->name }}: <span class="text-primary">{{ $output->quantity }}</span>.
                    @else
                      {{ $output->product->name }}: <span class="text-primary">{{ $output->quantity }}</span>,
                  @endif
                  @endforeach
                @endif
              </td>
              <td>{{ $casePatient->observation }}</td>

              
            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        <p class="m-0 p-0  align-self-center"> Total: {{ $casePatients->total() }}</p>
          {{-- Enlace a la primera página --}}
          <li class="page-item {{ $casePatients->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $casePatients->url(1) }}">
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          <li class="page-item {{ $casePatients->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $casePatients->previousPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          @for ($i = 1; $i <= $casePatients->lastPage(); $i++)
            <li class="page-item {{ $i == $casePatients->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $casePatients->url($i) }}">{{ $i }}</a>
            </li>
           @endfor
  
          {{-- Enlace a la página siguiente --}}
          <li class="page-item {{ $casePatients->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $casePatients->nextPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          <li class="page-item {{ $casePatients->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $casePatients->url($casePatients->lastPage()) }}">
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

function deleteCasePatient($caseID){

  if(confirm('Esta seguro de eliminar este caso?')){
      
      const form = document.getElementById('actions-form-delete'); 
      
      form.action = `/home/pacientes/casos/${$caseID}`; 
  
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