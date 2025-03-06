@extends('layout.home')

@section('content')

<div class="row">
    <div class="col-xl" id="card-1-patient">
      <div class="card mb-6" style="max-width: 680px;" >
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Cliente</h5>
          <small class="text-body float-end">Actualizar cliente</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('clients.update', ['client' => $client->id]) }}">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label class="form-label" for="basic-default-company">Nombre *</label>
                <input type="text" required class="form-control" name="clientName" value="{{ $client->name }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
              </div>
              <div class="mb-6">
                  <label class="form-label" for="basic-default-company">Cédula</label>
                  <input type="text" class="form-control" name="clientCI" value="{{ $client->ci }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
              </div>
              <div class="mb-6">
                  <label class="form-label" for="basic-default-company">Nro Teléfono</label>
                  <input type="text" class="form-control" name="clientPhoneNumber" value="{{ $client->phone_number }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
              </div>
              <div class="mb-6">
                  <label class="form-label" for="basic-default-company">Dirección</label>
                  <input type="text" class="form-control" name="clientAddress" value="{{ $client->address }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
              </div>
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary justify-self-end">Actualizar</button>
               </div>    
        </form>
        </div>
      </div>
    </div>


@endsection

@section('scripts')
<script>
</script>
@endsection