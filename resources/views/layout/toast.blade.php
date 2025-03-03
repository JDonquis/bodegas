@if(session('success') || $errors->any())
<div class="bs-toast toast toast-placement-ex m-2 fade bg-primary {{ session('success')? 'show' : '' }} {{ $errors->any()? 'show' : '' }}  top-0 end-0 hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
    <div class="toast-header">
      <i class="bx bx-bell me-2"></i>
      <div class="me-auto fw-medium">{{ config('app.name') }}</div>
      <small>Ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    @if(session('success'))
        <div class="toast-body">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="toast-body">{{ $errors->first() }}</div>
    @endif
    

    </div>


  <div class="row gx-3 gy-2 align-items-center d-none" >
    <div class="col-md-3">
      <label class="form-label" for="selectTypeOpt">Type</label>
      <select id="selectTypeOpt" class="form-select color-dropdown">
        <option value="bg-primary" {{ session('success')? 'selected' : '' }}>Primary</option>
        <option value="bg-secondary">Secondary</option>
        <option value="bg-success">Success</option>
        <option value="bg-danger" {{ $errors->any()? 'selected' : '' }} >Danger</option>
        <option value="bg-warning">Warning</option>
        <option value="bg-info">Info</option>
        <option value="bg-dark">Dark</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label" for="selectPlacement">Placement</label>
      <select class="form-select placement-dropdown" id="selectPlacement">
        <option value="top-0 start-0">Top left</option>
        <option value="top-0 start-50 translate-middle-x">Top center</option>
        <option value="top-0 end-0" selected>Top right</option>
        <option value="top-50 start-0 translate-middle-y">Middle left</option>
        <option value="top-50 start-50 translate-middle">Middle center</option>
        <option value="top-50 end-0 translate-middle-y">Middle right</option>
        <option value="bottom-0 start-0">Bottom left</option>
        <option value="bottom-0 start-50 translate-middle-x">Bottom center</option>
        <option value="bottom-0 end-0">Bottom right</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label" for="showToastPlacement">&nbsp;</label>
      <button id="showToastPlacement" class="btn btn-primary d-block">Show Toast</button>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastElement = document.querySelector('.bs-toast');

        // Si hay una sesión de éxito, ocultar el toast después de 3 segundos
        if (toastElement.classList.contains('show')) {
            setTimeout(() => {
                toastElement.classList.remove('show');
                toastElement.classList.add('hide');

            }, 5000); // 3000 ms = 3 segundos
        }
    });
</script>


@endif

