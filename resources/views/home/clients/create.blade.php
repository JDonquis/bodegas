@extends('layout.home')

@section('content')

<div class="row">
    <div class="col-xl" id="card-1-patient">
      <div class="card mb-6" style="max-width: 380px;" >
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Cliente</h5>
          <small class="text-body float-end">Crear cliente</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('clients.store') }}">
            @csrf
            <div class="mb-6">
              <label class="form-label" for="basic-default-company">Nombre *</label>
              <input type="text" required class="form-control" name="clientName" value="{{ old('clientName') }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
            </div>
            <div class="mb-6">
                <label class="form-label" for="basic-default-company">Cédula</label>
                <input type="text" class="form-control" name="clientCI" value="{{ old('clientCI') }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
            </div>
            <div class="mb-6">
                <label class="form-label" for="basic-default-company">Nro Teléfono</label>
                <input type="text" class="form-control" name="clientPhoneNumber" value="{{ old('clientPhoneNumber') }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
            </div>
            <div class="mb-6">
                <label class="form-label" for="basic-default-company">Dirección</label>
                <input type="text" class="form-control" name="clientAddress" value="{{ old('clientAddress') }}" id="input-name-patient" placeholder="Sin asignar" style="max-width: 200px;">
            </div>

            
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary justify-self-end">Crear</button>
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