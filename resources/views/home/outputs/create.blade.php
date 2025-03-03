@extends('layout.home')

@section('content')

<div class="row">
    <div class="col-xl col-md-6">
      <div class="card mb-6" style="max-height: 800px; overflow-y: scroll;">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Buscar productos</h5>
          <small class="text-muted float-end">Salidas</small>
        </div>
        <div class="card-body">
          <form>
            <div class="mb-6 d-flex gap-4">
              <div class="d-flex gap-2">  
                <div class="input-group input-group-merge">
                    <span id="basic-icon-default-fullname2" class="input-group-text">
                      <i class='bx bx-package'></i>
                    </span>
                    <input class="form-control" type="search"  value="" oninput="searchProduct()" placeholder="Buscar..." id="html5-search-input">
                  </div>

                </div>
            </div>
          
            <div class="table-responsive text-nowrap">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Productos</th>
                      <th>Stock</th>
                      <th></th>


                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0" id="search-results">
                    
                    
                  </tbody>
                </table>
              </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-xl col-md-6">
      <div class="card mb-6" style="max-height: 800px; overflow-y: scroll;">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Productos agregados</h5>
          <small class="text-muted float-end">Salidas</small>
        </div>
        <div class="card-body">
          <form action="{{ route('outputs.store') }}" method="POST">
            @csrf
            <div>
              <label for="defaultFormControlInput" class="form-label">Destino</label>
              <input type="text" required name="destiny" value="" class="form-control" id="defaultFormControlInput" placeholder="Organización..." aria-describedby="defaultFormControlHelp">
              
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Producto</th>
                      <th>Cantidad</th>
                      <th>Fecha de vencimiento</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0" id="added-products" >
                  
                  </tbody>
                </table>
              </div>
             
            <div class="w-full d-flex justify-content-end">  
              <button disabled type="submit" id="btn-create-output" class="btn btn-primary mt-3">Crear salida</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalScrollable" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-4">
          <h5 class="modal-title" id="modalScrollableTitle">Seleccionar inventario</h5>
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

  const productsAdded = [];

  function searchProduct() {
      let searchInput = document.getElementById('html5-search-input').value;

      // Evitar búsquedas vacías
      if (searchInput.length < 1) {
          document.getElementById('search-results').innerHTML = '';
          return;
      }

      // Realizar la llamada AJAX
      fetch(`/home/inventario/search/${encodeURIComponent(searchInput)}`)
        .then(response => response.json())
        .then(data => {

            

            let results = data.inventories.map(inventory => {
                
                
                return `<tr>
                    <td>
                        <a href="#" class="text-decoration-none text-reset" inventoryID=${inventory.id} >${inventory.product.name}</a>
                    </td>
                    <td>
                         ${inventory.stock}
                    </td>
                    <td>
                        <button type="button" onclick="showDetail(this)" class="btn btn-icon btn-success" data-bs-toggle="modal" data-bs-target="#modalScrollable"  inventoryID=${inventory.id}>
                            <i class='bx bx-plus'></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');
            document.getElementById('search-results').innerHTML = results;
        })
        .catch(error => {
            console.error(error);
        });
}

function showDetail($btn){

let inventoryID = $btn.getAttribute('inventoryID');

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

function buildModal($details){

let tableInventoryDetail = document.getElementById('inventory-details')


let results = $details.map(detail => {

  const productJson = JSON.stringify(detail).replace(/"/g, '&quot;');
  
  let status = '';

  if (productsAdded.some(p => p.id === detail.id)){
    status = 'disabled'
  }

  const formattedExpiredDate = formatDate(detail.expired_date);
      return `<tr>
                  <td>
                  <button class="btn" ${status} style="padding-left:0px !important;" inventoryDetailID="${detail.id}" onclick="addProduct(${productJson})">
                    ${detail.product.name}
                  </button>
                  </td>
                  <td>${detail.stock}</td>
                  <td>
                    ${formattedExpiredDate}
                  </td>
                </tr>  `;
          }).join('');

          tableInventoryDetail.innerHTML = results;

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

function addProduct($inventory) {

  if (!productsAdded.some(p => p.id === $inventory.id)) {
        $inventory.request_quantity = 1;

        productsAdded.unshift($inventory); 
        console.log('Producto añadido:', $inventory);
  }

        
  const button = document.querySelector(`button[inventoryDetailID="${$inventory.id}"]`);
  if (button) 
    button.disabled = true;
  


  refreshProducts();
}

function refreshProducts()
{

  let createOutputBtn = document.getElementById('btn-create-output');

  if(productsAdded.length > 0)
    createOutputBtn.disabled = false;
  else
    createOutputBtn.disabled = true;


  let results = productsAdded.map( (inventory, index ) => {

                const productJson = JSON.stringify(inventory).replace(/"/g, '&quot;'); // Escapar las comillas
                return `<tr>
                      <td>
                        <input type="hidden" name="products[${index}][productID]" value="${inventory.product.id}">
                        <input type="hidden" name="products[${index}][inventoryID]" value="${inventory.id}">
                        
                        <button type="button" onclick="cancelProduct(${inventory.id})" class="btn p-0" ><i class='bx bxs-x-circle' style="font-size: 24px;"></i></button>
                        ${inventory.product.name}
                      </td>
                      <td>
                        <div class="d-flex align-items-center"> 
                          <input class="form-control" required type="number" oninput="refreshData(${inventory.id}, 'quantity' , this)" min="1" max="${inventory.stock}" name="products[${index}][quantity]" value="${inventory.request_quantity}" pattern="[0-9]" title="Solo se permiten números" oninput="this.value = this.value.replace(/[a-zA-Z]/g, '');"  style="max-width: 80px;" >
                        /${inventory.stock}
                        </div>
                        </td>
                      <td>
                        ${inventory.expired_date}
                      </td>
                      
                    </tr>`;
            }).join('');
            document.getElementById('added-products').innerHTML = results;
}

function refreshData($inventoryID, $type,  $element){

  const product = productsAdded.find(product => product.id == $inventoryID);
  if($type == 'quantity')
    product.request_quantity = $element.value; 

  console.log(productsAdded);
    
}

function cancelProduct($inventoryID){
  
  

  const index = productsAdded.findIndex(product => product.id === $inventoryID);
  let productObject = productsAdded[index];

    console.log(productObject)
   
    if (index !== -1) 
       productObject = productsAdded.splice(index, 1)[0];
  

  let button = document.querySelector(`button[inventoryDetailID="${$inventoryID}"]`);
  if (button) 
    button.disabled = false;
  
    
    refreshProducts();
}

function createProduct(){
  
  if(confirm('Esta seguro de crear este producto?')){
    let searchInput = document.getElementById('html5-search-input').value;

    fetch(`/home/productos`,{
      method: 'POST',
      headers:{
        "Content-Type" : "application/json",
        "X-Requested-With" : "XMLHttpRequest"
      },
      body: JSON.stringify({
        _token: "{{ csrf_token() }}",
        productName: searchInput,
      })
    })
        .then(response => response.json())
        .then(data => {

          console.log(data)
          addProduct(data.product)
        })
        .catch(error => {
            console.error(error);
        });

  }
  else{
    console.log('ayy')
  }
}

</script>
@endsection