@extends('layout.home')

@section('content')
<div class="row justify-content-start">
    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <div class="card " style="height: 180px;">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <i class='bx bxs-down-arrow' style="font-size: 42px; color:#71dd37"></i>
            </div>
          </div>
          <p class="mb-1">Entradas</p>
          <h4 class="card-title mb-3 text-success">{{ $entries }}</h4>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <div class="card " style="height: 180px;">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <i class='bx bxs-up-arrow' style="font-size: 42px; color:#ffab00"></i>
            </div>
          </div>
          <p class="mb-1">Salidas</p>
          <h4 class="card-title mb-3 text-warning">{{ $outputs }}</h4>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <div class="card " style="height: 180px;">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <i class='bx bx-list-ul' style="font-size: 42px; color:#696cff" ></i>
            </div>
          </div>
          <p class="mb-1">En inventario</p>
          <h4 class="card-title mb-3 text-primary">{{ $inventories }}</h4>
        </div>
      </div>
    </div>

    {{-- <div class="col-6 col-md-4 col-lg-2 mb-4">
      <div class="card " style="height: 180px;">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
              <i class='bx bx-male' style="font-size: 42px; color:#ff3e1d;"></i>
            </div>
          </div>
          <p class="mb-1">Pacientes atendidos</p>
          <h4 class="card-title mb-3 text-danger">{{ $casePatients }}</h4>
        </div>
      </div>
    </div> --}}

  </div>

@endsection

