@extends('layout.home')

@section('content')
<div class="row">
    <div class="col-md-12 d-flex flex-column align-items-center">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6">
          <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);"
              ><i class="bx bx-sm bx-user me-1_5"></i> Mi Cuenta</a
            >
          </li>
          
        </ul>
      </div>
      <div class="card mb-6" style="max-width: 600px;">
        <!-- Account -->
        
        <div class="card-body pt-4">
          <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}" >
            @csrf
            @method('PUT')
            <div class="row g-6">
              <div class="col-12">
                <label for="name" class="form-label">Nombre</label>
                <input
                  class="form-control"
                  type="text"
                  id="name"
                  name="name"
                  value="{{ auth()->user()->name }}"
                  autofocus />
              </div>
              <div class="col-12">
                <label for="lastName" class="form-label">Apellido</label>
                <input class="form-control" type="text" name="lastName" id="lastName" value="{{ auth()->user()->last_name}}" />
              </div>
              <div class="col-12">
                <label for="ci" class="form-label">Cédula</label>
                <input
                  class="form-control"
                  type="text"
                  id="ci"
                  name="ci"
                  value="{{ auth()->user()->ci }}"
                  placeholder="Cédula" />
              </div>

              </div>
              
            </div>
            <div class="mt-6 mb-3 d-flex justify-content-center">
              <button type="submit" class="btn btn-primary me-3">Guardar cambios</button>
              <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
          </form>
        </div>
        <!-- /Account -->
      </div>

    </div>
  </div>

@endsection

