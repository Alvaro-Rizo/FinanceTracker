<header class="app-header navbar navbar-expand">
    <div class="container-fluid">
        <button class="btn sidebar-toggle me-2 d-lg-none">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-cash-coin me-2"></i>
            <span class="d-none d-sm-inline">FinanceTracker</span>
        </a>

        <div class="d-flex align-items-center ms-auto">
 <button class="btn dark-mode-toggle me-2 p-0" type="button" role="switch" aria-checked="false" aria-label="Cambiar tema">
    <span class="switch-wrapper">
        <input class="switch-check" id="darkModeSwitch" type="checkbox">
        <label class="switch-label" for="darkModeSwitch">
            <span class="switch-handle"></span>
        </label>
    </span>
</button>

            <div class="dropdown">
                <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('perfil') }}">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>