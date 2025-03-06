<li class="nav-item navbar-dropdown dropdown-user dropdown">
    <a
      class="nav-link dropdown-toggle hide-arrow p-0"
      href="javascript:void(0);"
      data-bs-toggle="dropdown">
      <div class="avatar avatar-online">
        <div  style="position: relative; top:8px; left:5px;" >
          <i class='bx bx-user-circle' style="font-size: 32px;" ></i>
        </div>
        {{-- <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" /> --}}
      </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li>
        <a class="dropdown-item" href="#">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="avatar avatar-online">
                <div  style="position: relative; top:8px; left:5px;" >
                  <i class='bx bx-user-circle' style="font-size: 32px;" ></i>
                </div>
              </div>
            </div>
            <div class="flex-grow-1">
              <h6 class="mb-0">{{ auth()->user()->name . ' ' . auth()->user()->last_name  }}</h6>
              <small class="text-muted">{{ auth()->user()->charge }}(a)</small>
            </div>
          </div>
        </a>
      </li>
      <li>
        <div class="dropdown-divider my-1"></div>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('profile') }}">
          <i class="bx bx-user bx-md me-3"></i><span>Perfil</span>
        </a>
      </li>
      <li>
        <div class="dropdown-divider my-1"></div>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('logout') }}">
          <i class="bx bx-power-off bx-md me-3"></i><span>Salir</span>
        </a>
      </li>
    </ul>
  </li>