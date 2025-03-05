@extends('layout.home')

@section('content')

<div class="row">
    <div class="col-12">
      <div class="card mb-6" style="max-height: 800px; overflow-y: scroll;">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Buscar productos</h5>
          <small class="text-muted float-end">Entradas</small>
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

                  <button type="button" id="btnCreateProduct" disabled onclick="createProduct()" class="btn btn-icon btn-primary">
                    <i class='bx bx-plus' ></i>
                  </button>

                </div>
            </div>
          
            <div class="table-responsive text-nowrap">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Productos</th>
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

    <div class="col-12">
      <div class="card mb-6" style="max-height: 800px; overflow-y: scroll;">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Productos agregados</h5>
          <small class="text-muted float-end">Entradas</small>
        </div>
        <div class="card-body">
          <form action="{{ route('entries.store') }}" method="POST">
            @csrf
            <div class="table-responsive text-nowrap">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Producto</th>
                      <th>Cantidad</th>
                      <th>Prec. de Compra $</th>
                      <th>Nro Lote</th>
                      <th>Fecha de vencimiento</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0" id="added-products" >
                  
                  </tbody>
                </table>
              </div>
             
            <div class="w-full d-flex justify-content-end">  
              <button disabled type="submit" id="btn-create-entry" class="btn btn-primary mt-3">Crear entrada</button>
            </div>
          </form>
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
      fetch(`/home/productos/search/${encodeURIComponent(searchInput)}`)
        .then(response => response.json())
        .then(data => {

            if(data.products.length == 0)
            {
              document.getElementById('btnCreateProduct').disabled = false;
            }

            let results = data.products.map(product => {
                const productJson = JSON.stringify(product).replace(/"/g, '&quot;');
                
                let status = '';
                if (productsAdded.some(p => p.id === product.id)){
                  status = 'disabled'
                }

                
                return `<tr>
                    <td>
                        <a href="#" onclick="addProduct(${productJson})" class="text-decoration-none text-reset" productID=${product.id} >${product.name}</a>
                    </td>
                    <td>
                        <button type="button" ${status} onclick="addProduct(${productJson})" class="btn btn-icon btn-success" productID=${product.id}>
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

function addProduct($product) {

  if (!productsAdded.some(p => p.id === $product.id)) {
        $product.quantity = 1;
        $product.date = null;
        $product.cost = 0;
        $product.lote_number = "";


        productsAdded.unshift($product); 
        console.log('Producto añadido:', $product);
  }

        
  const button = document.querySelector(`button[productID="${$product.id}"]`);
  if (button) 
    button.disabled = true;
  
  const link = document.querySelector(`a[productID="${$product.id}"]`);
  if (link) 
    link.removeAttribute('onclick');  
  

  refreshProducts();
}

function refreshProducts()
{

  let createEntryBtn = document.getElementById('btn-create-entry');

  if(productsAdded.length > 0)
    createEntryBtn.disabled = false;
  else
    createEntryBtn.disabled = true;


  let results = productsAdded.map( (product, index ) => {
                const productJson = JSON.stringify(product).replace(/"/g, '&quot;'); // Escapar las comillas
                return `<tr>
                      <td>
                        <input type="hidden" name="products[${index}][productID]" value="${product.id}">
                        
                        <button type="button" onclick="cancelProduct(${product.id})" class="btn p-0" ><i class='bx bxs-x-circle' style="font-size: 24px;"></i></button>
                        ${product.name}
                      </td>
                      <td>
                        <input class="form-control" required type="number" oninput="refreshData(${product.id}, 'quantity' , this)" min="1" name="products[${index}][quantity]" value="${product.quantity}" pattern="[0-9]" title="Solo se permiten números" oninput="this.value = this.value.replace(/[a-zA-Z]/g, '');"  style="max-width: 80px;" >
                      </td>
                      <td>
                        <input class="form-control" required type="number" step="0.01" min="0" oninput="refreshData(${product.id}, 'cost' , this)" min="1" name="products[${index}][cost]" value="${product.cost}" pattern="[0-9]" title="Solo se permiten números" oninput="this.value = this.value.replace(/[a-zA-Z]/g, '');"  style="max-width: 100px;" >
                      </td>
                      <td>
                        <input class="form-control" required type="text" oninput="refreshData(${product.id}, 'lote_number' , this)" min="1" name="products[${index}][lote_number]" value="${product.lote_number}"  style="max-width: 120px;" >
                      </td>
                      <td>
                        <input class="form-control" required type="date" oninput="refreshData(${product.id}, 'date', this)" value="${product.date}" name="products[${index}][expiredDate]" >
                      </td>
                      
                    </tr>`;
            }).join('');
            document.getElementById('added-products').innerHTML = results;
}

function refreshData($productID, $type,  $element){

  const product = productsAdded.find(product => product.id == $productID);
  if($type == 'quantity')
    product.quantity = $element.value; 

  if($type == 'date')
    product.date = $element.value; 

  if($type == 'cost')
    product.cost = $element.value; 
    
  if($type == 'lote_number')
    product.lote_number = $element.value; 

  console.log(productsAdded);
    
}

function cancelProduct($productID){
  
  const index = productsAdded.findIndex(product => product.id === $productID);
  let productObject = productsAdded[index];

    console.log(productObject)
   
    if (index !== -1) 
       productObject = productsAdded.splice(index, 1)[0];
  

  let button = document.querySelector(`button[productID="${$productID}"]`);
  if (button) 
    button.disabled = false;
  
  let link = document.querySelector(`a[productID="${$productID}"]`);
  if (link) 
    link.setAttribute('onclick', `addProduct(${JSON.stringify(productObject)});`);
  
    
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