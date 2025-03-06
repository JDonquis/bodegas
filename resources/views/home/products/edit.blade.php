@extends('layout.home')

@section('content')

<div class="row">
    <div class="col-xl" id="card-1-patient">
      <div class="card mb-6" style="max-width: 680px;" >
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Producto</h5>
          <small class="text-body float-end">Actualizar producto</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('products.update', ['product' => $product->id]) }}">
            @csrf
            @method('PUT')
            <div class="mb-6">
              <label class="form-label" for="basic-default-company">Nombre</label>
              <input type="text" class="form-control" name="productName" value="{{ $product->name }}" id="input-name-patient" placeholder="Nombre">
            </div>
            <div class="mb-6">
              <label class="form-label" for="basic-default-email">Precio de venta</label>
              <div class="input-group input-group-merge">
                <input type="number" step="0.001" min="0.001" id="input-lastname-patient" value="{{ $product->sell_price }}" name="sellPrice" class="form-control" placeholder="Precio de venta" aria-label="john.doe" aria-describedby="basic-default-email2">
              </div>
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