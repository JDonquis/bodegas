@extends('layout.home')

@section('content')
<div class="card">
    <div class=" d-flex justify-content-between align-items-center ">
        <h5 class="card-header">Salidas</h5>
        <a type="button" class="btn btn-primary text-white" href="{{ route('outputs.create') }}" style="height: 70%; margin-right:30px;">
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
            <th>Venta a</th>
            <th>Cuenta Total</th>
            <th>Ganancia Total</th>


          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @php
            Carbon\Carbon::setLocale('es');
          @endphp
          @foreach ($outputs as $output )
            <tr>
              
              <td>
                <a  href="{{ route('invoice',['outputID' => $output->id]) }}" class="btn btn-outline-primary p-1">
                  <i class='bx bxs-report'></i>
                </a>
                <button type="button" onclick="showDetail(this)" class="btn" output="{{ $output->id }}" data-bs-toggle="modal" data-bs-target="#modalScrollable">
                  <i class='bx bx-detail' ></i>
                </button>
                
                {{ ucfirst($output->created_at->translatedFormat('F j, Y')) }}
              </td>
              <td>{{ $output->quantity_products }}</td>
              <td>{{ $output->client->name }}</td>
              <td>{{ $output->total_sold }}$</td>
              <td><span class="text-primary">{{ $output->total_profit}}$</span></td>

            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        <p class="m-0 p-0  align-self-center"> Total: {{ $outputs->total() }}</p>
          {{-- Enlace a la primera página --}}
          <li class="page-item {{ $outputs->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $outputs->url(1) }}">
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          <li class="page-item {{ $outputs->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $outputs->previousPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          @for ($i = 1; $i <= $outputs->lastPage(); $i++)
            <li class="page-item {{ $i == $outputs->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $outputs->url($i) }}">{{ $i }}</a>
            </li>
           @endfor
  
          {{-- Enlace a la página siguiente --}}
          <li class="page-item {{ $outputs->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $outputs->nextPageUrl() }}">
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          <li class="page-item {{ $outputs->hasMorePages() ? '' : 'disabled' }}">
              <a class="page-link" href="{{ $outputs->url($outputs->lastPage()) }}">
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
          <h5 class="modal-title" id="modalScrollableTitle">Salida detallada</h5>
          <p class="m-0" id="date-output"></p>
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
                  <th>Prec. compra</th>
                  <th>Vendido en</th>
                  <th>Ganancia</th>
                  <th>Nro Lote</th>
                  <th>Vencimiento</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0" id="outputs-details">
                
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" >
          <div class="position-absolute w-full" method="" action="" style="left: 30px;">
            <button type="button"  id="delete-btn" onclick="deleteOutput(this)" data-outputID="" class="btn btn-outline-danger"><i class='bx bx-trash' ></i></button>
            <button type="button"  id="update-btn" onclick="updateOutput(this)" data-outputID="" class="btn btn-outline-primary "><i class='bx bx-pencil' ></i></button>
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

function showDetail($btn){

  let outputID = $btn.getAttribute('output');

  fetch(`/home/salidas/${outputID}`,{
      method: 'GET',
      headers:{
        "Content-Type" : "application/json",
        "X-Requested-With" : "XMLHttpRequest"
      },
    })
        .then(response => response.json())
        .then(data => {

          console.log(data)
          buildModal(data.outputs)
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

function deleteOutput($element){

  const outputID = $element.getAttribute('data-outputID');


  if(confirm('Esta seguro de eliminar esta salida?')){
      
      const form = document.getElementById('actions-form-delete'); 
      
      form.action = `/home/salidas/${outputID}`; 
  
      form.submit();
      
    }

}

function updateOutput($element){

  const outputID = $element.getAttribute('data-outputID');
  window.location.href=`/home/salidas/editar/${outputID}`

}

function buildModal($outputs){

  let dateOutput = document.getElementById('date-output')
  let tableOutputsDetail = document.getElementById('outputs-details')
  let deleteBtn = document.getElementById('delete-btn')
  let updateBtn = document.getElementById('update-btn')

  dateOutput.innerHTML = $outputs[0].created_at;
  deleteBtn.setAttribute('data-outputID',$outputs[0].output_general_id);
  updateBtn.setAttribute('data-outputID',$outputs[0].output_general_id);
  
  
  let results = $outputs.map(output => {

    const formattedExpiredDate = formatDate(output.expired_date);
    let sellPrice = calculateSellPrice(output);
        return `<tr>
                    <td>
                      ${output.product.name}
                    </td>
                    <td>${output.quantity}</td>
                    <td>${output.inventory.cost_per_unit}$</td>
                    <td>${sellPrice}$</td>
                    <td>${output.profit}$</td>
                    <td>${output.inventory.lote_number}</td>
                    <td>
                      ${formattedExpiredDate}
                    </td>
                  </tr>  `;
            }).join('');

            tableOutputsDetail.innerHTML = results;

}

function calculateSellPrice(output){

let costPrice = output.inventory.cost_per_unit * -1;
costPrice *= output.quantity; 
let sellPrice = costPrice - output.profit;
sellPrice = sellPrice / output.quantity;

sellPrice = Math.round(sellPrice * 1000) / 1000;

  return sellPrice * -1;
}



</script>


@endsection