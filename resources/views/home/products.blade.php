@extends('layout.home')

@section('content')
<div class="card">
    <div class=" d-flex justify-content-between align-items-center ">
        <h5 class="card-header">Productos</h5>
        <a type="button" class="btn btn-primary text-white" href="{{ route('patients.create') }}" style="height: 70%; margin-right:30px;">
          <i class='bx bx-plus'></i>
          Nuevo producto
        </a>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Fecha de registro</th>
            <th>Nombre</th>
            <th>Precio de venta</th>

          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @php
            Carbon\Carbon::setLocale('es');
          @endphp
          @foreach ($products as $product )
            <tr>
              <td>
                <button type="button" onclick="deleteCasePatient({{ $product->id }})" class="btn btn-outline-danger p-1">
                  <i class='bx bx-trash' ></i>
                </button>
                
              </td>
              <td>
                {{ ucfirst($product->created_at->translatedFormat('F j, Y, g:i A')) }}
              </td>
              <td><span class="text-primary">{{ $product->name}}</span></td>
              <td>{{ $product->sell_price }}$</td>
            </tr>  
          @endforeach
        </tbody>
      </table>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-end" style="margin-right:30px;">
        {{-- <p class="m-0 p-0  align-self-center"> Total: {{ $casePatients->total() }}</p> --}}
          {{-- Enlace a la primera página --}}
          {{-- <li class="page-item {{ $casePatients->onFirstPage() ? 'disabled' : '' }}"> --}}
              {{-- <a class="page-link" href="{{ $casePatients->url(1) }}"> --}}
                  <i class="tf-icon bx bx-chevrons-left bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la página anterior --}}
          {{-- <li class="page-item {{ $casePatients->onFirstPage() ? 'disabled' : '' }}"> --}}
              {{-- <a class="page-link" href="{{ $casePatients->previousPageUrl() }}"> --}}
                  <i class="tf-icon bx bx-chevron-left bx-sm"></i>
              </a>
          </li>
  
          {{-- @for ($i = 1; $i <= $casePatients->lastPage(); $i++) --}}
            {{-- <li class="page-item {{ $i == $casePatients->currentPage() ? 'active' : '' }}"> --}}
                {{-- <a class="page-link" href="{{ $casePatients->url($i) }}">{{ $i }}</a> --}}
            </li>
           {{-- @endfor --}}
  
          {{-- Enlace a la página siguiente --}}
          {{-- <li class="page-item {{ $casePatients->hasMorePages() ? '' : 'disabled' }}"> --}}
              {{-- <a class="page-link" href="{{ $casePatients->nextPageUrl() }}"> --}}
                  <i class="tf-icon bx bx-chevron-right bx-sm"></i>
              </a>
          </li>
          {{-- Enlace a la última página --}}
          {{-- <li class="page-item {{ $casePatients->hasMorePages() ? '' : 'disabled' }}"> --}}
              {{-- <a class="page-link" href="{{ $casePatients->url($casePatients->lastPage()) }}"> --}}
                  <i class="tf-icon bx bx-chevrons-right bx-sm"></i>
              </a>
          </li>
      </ul>
  </nav
</div>

@endsection

@section('scripts')

@endsection