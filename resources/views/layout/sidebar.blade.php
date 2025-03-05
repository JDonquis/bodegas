<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex justify-content-center" style="height:auto !important;">
      <a href="{{ route('home') }}" class="app-brand-link">
          <img src="{{ asset('bodega.png') }}" class="w-full " style=" height:90px; object-fit: cover; " alt="">
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
    

      <!-- Apps & Pages -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Menú</span>
      </li>
      @role('admin')
      <li class="menu-item {{ request()->routeIs('patients') ? 'active' : ''}}">
        <a
        href="{{ route('products') }}"
        class="menu-link">
        <i class="menu-icon tf-icons bx bx-cheese"></i>
        <div class="text-truncate" data-i18n="Chat">Productos</div>
      </a>
    </li>

      <li class="menu-item {{ request()->routeIs('entries') ? 'open active' : ''}} {{ request()->routeIs('entries.*') ? 'open active' : ''}} {{ request()->routeIs('outputs.*') ? 'open active' : ''}} {{ request()->routeIs('outputs') ? 'open active' : ''}} {{ request()->routeIs('inventory') ? 'open active' : ''}}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-store-alt"></i>
          <div class="text-truncate" data-i18n="Account Settings">Inventario</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('entries.*') ? 'active' : ''}} {{ request()->routeIs('entries') ? 'active' : ''}}">
            <a href="{{ route('entries') }}" class="menu-link">
              <div class="text-truncate" data-i18n="Account">Entradas</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('outputs.*') ? 'active' : ''}} {{ request()->routeIs('outputs') ? 'active' : ''}}">
            <a href="{{ route('outputs') }}" class="menu-link">
              <div class="text-truncate" data-i18n="Account">Salidas</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('inventory') ? 'active' : ''}}">
            <a href="{{ route('inventory') }}" class="menu-link">
              <div class="text-truncate" data-i18n="Account">Ver inventario</div>
            </a>
          </li>
          
        </ul>
      </li>
      <li class="menu-item {{ request()->routeIs('patients') ? 'active' : ''}}">
        <a
        href="{{ route('patients') }}"
        class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div class="text-truncate" data-i18n="User">Clientes</div>
      </a>
    </li>
    @endrole
    
    </ul>
  </aside>