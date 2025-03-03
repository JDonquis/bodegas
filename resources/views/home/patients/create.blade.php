@extends('layout.home')

@section('content')

<div class="row">
    <div class="col-xl" id="card-1-patient">
      <div class="card mb-6" style="max-width: 680px;" >
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Paciente</h5>
          <small class="text-body float-end">Crear caso</small>
        </div>
        <div class="card-body">
          <form>
            <div class="mb-6">
              <label class="form-label" for="basic-default-fullname">Buscar por cédula</label>
              <div class="d-flex">
                <input type="text" class="form-control" value="" id="searcher-ci" placeholder="Cédula del paciente">
                <button type="button" class="btn btn-outline-primary" onclick="searchPatient()"><i class='bx bx-search'></i></button>
              </div>
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-company">Nombre</label>
              <input type="text" class="form-control" id="input-name-patient" placeholder="Nombre">
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-email">Apellido</label>
              <div class="input-group input-group-merge">
                <input type="text" id="input-lastname-patient" class="form-control" placeholder="Apellido" aria-label="john.doe" aria-describedby="basic-default-email2">
              </div>
            </div>
            <div class="mb-6">
              <label for="exampleFormControlSelect1" class="form-label">Tipo de paciente</label>
              <select class="form-select" id="input-type-patient" aria-label="Default select example">
                @foreach ($typePatients as $typePatient )
                  <option value="{{ $typePatient->id }}">{{ $typePatient->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-6">
                <label for="exampleFormControlSelect1" class="form-label">Carrera</label>
                <select class="form-select" id="input-career-patient" aria-label="Default select example">
                  @foreach ($careers as $career )
                    <option value="{{ $career->id }}">{{ $career->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md mb-6 d-flex justify-content-start gap-5">
                    <div>
                        <small class="text-light fw-medium">Sexo</small>
                        <div class="form-check mt-3">
                        <input name="default-radio-1" class="form-check-input" type="radio" value="Masculino" ="" id="defaultRadio1">
                        <label class="form-check-label" for="defaultRadio1"> Masculino </label>
                        </div>
                        <div class="form-check">
                        <input name="default-radio-1" class="form-check-input" type="radio" value="Femenino" checked id="defaultRadio2" >
                        <label class="form-check-label" for="defaultRadio2"> Femenino </label>
                        </div>
                    </div>
                    <div>
                        <small class="text-light fw-medium" for="defaultRadio1"> Edad </small>
                        <input class="form-control mt-3" type="number" value="18" style="max-width: 80px;" id="input-age-patient">
                    </div>
              </div>
              <div class="d-flex justify-content-between">
                <button type="button" onclick="finishPatientCard()" class="btn btn-primary justify-self-end">Siguiente</button>

               </div>    
        </form>
        </div>
      </div>
    </div>
    <div class="col-xl d-none" id="card-2-diagnostico">
      <div class="card mb-6" style="max-width: 680px;" >
          
          <div class="card-header d-flex justify-content-between align-items-center">
           <button class="btn text-secondary" onclick="backToPatient()" style="max-width: 80px;"><i class='bx bx-left-arrow-alt' style="font-size:32px; margin:0px !important;"></i></button>
          <h5 class="mb-0">Diagnóstico</h5>
          <small class="text-muted float-end">Crear caso</small>
        </div>
        <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                  <img src="{{ asset('/assets/img/avatars/paciente.png') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar">
                  <div class="d-flex flex-column gap-3">
                    <small id="profile-fullname" class="text-muted float-end">Nombre Apellido</small>
                    <small id="profile-ci" class="text-muted float-end">Cedula</small>
                    <small id="profile-career" class="text-muted float-end">Carrera</small>
                    <small id="profile-sex-age" class="text-muted float-end">Sexo / Edad</small>
                  </div>
                </div>
          <form method="POST" action="{{ route('patients.case.store') }}" id="formCasePathology">
            @csrf
            <input type="hidden" name="patientID" id="input-patiendID" value="">
            <input type="hidden" name="outputGeneralID" id="input-outputGeneralID" value="0">
            <div class="mb-6 mt-6">
                <label class="form-label" for="basic-default-company">Buscar enfermedad</label>
                <div class="d-flex ">
                  <input type="text" oninput="searchPathology(this)" class="form-control" id="searcher-pathology" placeholder="Gripe...">
                  <button type="button" id="btnCreatePathology" disabled onclick="createPathology()" class="btn btn-icon btn-primary">
                    <i class='bx bx-plus' ></i>
                  </button>                
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Enfermedad</th>
                        <th></th>
  
  
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="search-patology-results">
                      
                      
                    </tbody>
                  </table>
              </div>
              <div class="list-group list-group-flush" id="added-pathologies">
                
              </div>
            </div>

            <div class="mb-6">
              <label class="form-label" for="basic-icon-default-message">Observación</label>
              <div class="input-group input-group-merge">
                <span id="basic-icon-default-message2" class="input-group-text"><i class="bx bx-comment"></i></span>
                <textarea id="basic-icon-default-message" name="observation" class="form-control" placeholder="El paciente posee..." aria-label="Hi, Do you have a moment to talk Joe?" aria-describedby="basic-icon-default-message2"></textarea>
              </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" onclick="finishPathologyCard()" class="btn btn-info">Asignar medicamentos</button>
                <button type="submit" class="btn btn-primary">Crear caso</button>

            </div>    
        </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row d-none" id="card-3-outputs">
    <div class="col-xl col-md-6">
      <div class="card mb-6" style="max-height: 800px; overflow-y: scroll;">
        <div class="card-header d-flex justify-content-between align-items-center">
           <button class="btn text-secondary" onclick="backToPathology()" style="max-width: 80px;"><i class='bx bx-left-arrow-alt' style="font-size:32px; margin:0px !important;"></i></button>
          <h5 class="mb-0">Buscar productos</h5>
          <small class="text-muted float-end">Crear caso</small>
        </div>
        <div class="card-body">
          <form>
              <label for="search">Buscar:</label>
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
          <small class="text-muted float-end">Crear caso</small>
        </div>
        <div class="card-body">
          <form id="formProductsOutput" method="POST">
            @csrf
            <div>
              <input type="hidden" name="destiny" id="destinyInput" value="">
              <label for="defaultFormControlInput" id="destinyLabel" class="form-label">Paciente: Nombre y apellido</label>
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
              <button disabled type="button" onclick="createOutput()" id="btn-create-output" class="btn btn-primary mt-3">Crear caso</button>
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
    const pathologiesAdded = [];
    const patientObj = {};
    let pathologyForm = {};

    let patientCiInput = document.getElementById('searcher-ci');
    let patientNameInput = document.getElementById('input-name-patient');
    let patientLastNameInput = document.getElementById('input-lastname-patient');
    let patientCareerInput = document.getElementById('input-career-patient');
    let patientTypeInput = document.getElementById('input-type-patient');
    let patientSexInput = {Masculino:document.getElementById('defaultRadio1') , Femenino: document.getElementById('defaultRadio2') };
    let patientAgeInput = document.getElementById('input-age-patient');
    
    
    function searchPatient(){
      let searchPatientValue = patientCiInput.value;

      fetch(`/home/pacientes/search/${encodeURIComponent(searchPatientValue)}`)
      .then(response => response.json())
      .then(data => {

          if(data.patient == null)
          {
            alert('No se ha conseguido registros de este paciente');
            clearInputs()
            return 0;
          }

          fillInputs(data.patient);

          
      })
    }

    function fillInputs($patient){
  
     patientNameInput.value = $patient.name 
     patientLastNameInput.value = $patient.last_name
     patientCareerInput.value = $patient.career_id 
     patientTypeInput.value = $patient.type_patient_id

     if($patient.sex == 'Masculino')
     {
      patientSexInput.Masculino.checked = true;
      patientSexInput.Femenino.checked = false;
     }
     else
     {
      patientSexInput.Masculino.checked = false;
      patientSexInput.Femenino.checked = true;
     }

     patientAgeInput.value = $patient.age
  
  }

  function clearInputs(){
  
    patientNameInput.value = null 
    patientLastNameInput.value = null
    patientCareerInput.value = 1 
    patientTypeInput.value = 1
    patientSexInput.Masculino.checked = true;
    patientSexInput.Femenino.checked = false;
    patientAgeInput.value = 18
    

}

  function finishPatientCard(){
    patientObj.career_name = patientCareerInput.options[patientCareerInput.selectedIndex].text
    patientObj.ci = patientCiInput.value;
    patientObj.name = patientNameInput.value;
    patientObj.last_name = patientLastNameInput.value;
    patientObj.career_id = patientCareerInput.value;
    patientObj.type_patient_id = patientTypeInput.value;

    
    if(patientSexInput.Masculino.checked)
      patientObj.sex = "Masculino";
    else
      patientObj.sex = "Femenino";

    patientObj.age = patientAgeInput.value;

    fetch(`/home/pacientes`,{
      method: 'POST',
      headers:{
        "Content-Type" : "application/json",
        "X-Requested-With" : "XMLHttpRequest"
      },
      body: JSON.stringify({
        _token: "{{ csrf_token() }}",
        patient: patientObj,
      })
    })
        .then(response => response.json())
        .then(data => {

          document.getElementById('card-1-patient').classList.add('d-none');
          document.getElementById('card-2-diagnostico').classList.remove('d-none');
          document.getElementById('input-patiendID').value = data.patient.id

          fillPatientProfile(patientObj);
          
        })
        .catch(error => {
            console.error(error);
        });
  }

  function fillPatientProfile($patient){

    document.getElementById('profile-fullname').innerHTML = `${$patient.name} ${$patient.last_name}`
    document.getElementById('profile-ci').innerHTML = `${$patient.ci}`
    document.getElementById('profile-career').innerHTML = `${$patient.career_name}`
    document.getElementById('profile-sex-age').innerHTML = `${$patient.sex} / ${$patient.age} años`

  }

  function searchPathology(searcher){
    
    if (searcher.value.length < 1) {
            document.getElementById('search-patology-results').innerHTML = '';
            return;
        }

        fetch(`/home/patologias/search/${encodeURIComponent(searcher.value)}`)
          .then(response => response.json())
          .then(data => {
  
            if(data.pathologies.length == 0)
            {
              document.getElementById('btnCreatePathology').disabled = false;
            }
  
              let results = data.pathologies.map(pathology => {
                  
                  
                const pathologyJson = JSON.stringify(pathology).replace(/"/g, '&quot;');
                
                let status = '';
                if (pathologiesAdded.some(p => p.id === pathology.id)){
                  status = 'disabled'
                }

                
                return `<tr>
                    <td>
                        <a href="#" onclick="addPathology(${pathologyJson})" class="text-decoration-none text-reset" pathologyID=${pathology.id} >${pathology.name}</a>
                    </td>
                    <td>
                        <button type="button" ${status} onclick="addPathology(${pathologyJson})" class="btn btn-icon btn-success" pathologyID=${pathology.id}>
                            <i class='bx bx-plus'></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');
            document.getElementById('search-patology-results').innerHTML = results;
          })
          .catch(error => {
              console.error(error);
          });
  }

  function addPathology($pathology){

    if (!pathologiesAdded.some(p => p.id === $pathology.id)) {
        pathologiesAdded.unshift($pathology); 
        console.log('Enfermedad añadida:', $pathology);
    }

        
    const button = document.querySelector(`button[pathologyID="${$pathology.id}"]`);
    if (button) 
      button.disabled = true;
    
    const link = document.querySelector(`a[pathologyID="${$pathology.id}"]`);
    if (link) 
      link.removeAttribute('onclick');  
    

    refreshPathologies();
  }

  function refreshPathologies(){

    let results = pathologiesAdded.map( (pathology, index ) => {
                  return `
                  <input type="hidden" name="pathologies[${index}]" value="${pathology.id}">
                  <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                     ${pathology.name}
                    <button type="button" onclick="cancelPathology(${pathology.id})" class="btn p-2" ><i class='bx bxs-x-circle' style="font-size: 24px;"></i></button> 
                  </a>
                  `;
              }).join('');
              document.getElementById('added-pathologies').innerHTML = results;
  }

  function cancelPathology($pathologyID){
  
    const index = pathologiesAdded.findIndex(pathology => pathology.id === $pathologyID);
    let pathologyObject = pathologiesAdded[index];

      console.log(pathologyObject)
    
      if (index !== -1) 
        pathologyObject = pathologiesAdded.splice(index, 1)[0];
    

    let button = document.querySelector(`button[pathologyID="${$pathologyID}"]`);
    if (button) 
      button.disabled = false;
    
    let link = document.querySelector(`a[pathologyID="${$pathologyID}"]`);
    if (link) 
      link.setAttribute('onclick', `addProduct(${JSON.stringify(pathologyObject)});`);
    
      
      refreshPathologies();
    
  }

  function createPathology(){

      if(confirm('Esta seguro de crear este registro?')){
        let searchInput = document.getElementById('searcher-pathology').value;

      fetch(`/home/patologias`,{
        method: 'POST',
        headers:{
          "Content-Type" : "application/json",
          "X-Requested-With" : "XMLHttpRequest"
        },
        body: JSON.stringify({
          _token: "{{ csrf_token() }}",
          pathologyName: searchInput,
        })
      })
          .then(response => response.json())
          .then(data => {

            addPathology(data.pathology)

          })
          .catch(error => {
              console.error(error);
          });

    }
    else{
      console.log('ayy')
    }
  }


  function backToPatient(){

    document.getElementById('card-1-patient').classList.remove('d-none');
    document.getElementById('card-2-diagnostico').classList.add('d-none');
  
  }

  function finishPathologyCard(){
    


    document.getElementById('card-2-diagnostico').classList.add('d-none');
    document.getElementById('card-3-outputs').classList.remove('d-none');

    document.getElementById('destinyInput').value = `${patientObj.name} ${patientObj.last_name}`
    document.getElementById('destinyLabel').innerHTML = `Paciente: ${patientObj.name} ${patientObj.last_name}`
    
  }


  function createOutput(){

    outputForm = new FormData(document.getElementById('formProductsOutput'));
    
    if(confirm('Esta seguro de asignar estos medicamentos?')){
  
      fetch(`/home/salidas/true`,{
        method: 'POST',
        // headers:{
        //   "Content-Type" : "application/json",
        //   "X-Requested-With" : "XMLHttpRequest"
        // },
        body: outputForm
      })
          .then(response => response.json())
          .then(data => {
          
            document.getElementById('input-outputGeneralID').value = data.outputGeneralID;
            document.getElementById('formCasePathology').submit();
            
            // createCase()
          })
          .catch(error => {
              console.error(error);
          });
  
    }
    else{
      console.log('ayy')
    }
  }

  // function createCase(){


  
  // }

  function backToPathology(){

    document.getElementById('card-2-diagnostico').classList.remove('d-none');
    document.getElementById('card-3-outputs').classList.add('d-none');

}

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