<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Sistema de Bitácoras')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Css/Sidebar.css') }}">
    @stack('styles')
</head>

<body>
    <div id="sidebar-navbar">
        <div class="topbar">
            <button class="hamburger d-md-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="left-section d-none d-md-flex">
                <img src="https://covao.ed.cr/wp-content/uploads/2024/12/image-removebg-preview-3.png" alt="Logo Covao" class="logo" />
                <div class="separator"></div>
                <img src="https://nocturno.covao.ed.cr/wp-content/uploads/2024/11/logo-covao-nocturno.webp" alt="Logo Covao Nocturno" class="logo" />
            </div>
            <div class="title">Bitácoras HHC</div>
            <img src="https://academiashhc.com/wp-content/uploads/2022/09/AcademiasB.png" alt="Logo Academias" class="right-logo d-none d-md-block" />
        </div>
        <div class="yellow-line"></div>
        <div class="sidebar-separator d-none d-md-block"></div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="https://covao.ed.cr/wp-content/uploads/2024/12/image-removebg-preview-3.png" />
            </div>
            <a href="#" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-house-door-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Inicio">Inicio</div>
            </a>
            <a href="#" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-calendar-week-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Horarios">Bitácora</div>
            </a>
            <a href="#" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Reportes">Reportes</div>
            </a>
            <div class="mt-auto mb-3" style="width:100%;">
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <a href="#" onclick="this.closest('form').submit();return false;" class="sidebar-item" style="width:100%;">
                        <div class="icon-circle"><i class="bi bi-box-arrow-right"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="Salir">Salir</div>
                    </a>
                </form>
            </div>
        </div>
    </div>

    @yield('content')

    <script src="{{ asset('JS/Sidebar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @stack('scripts')
</body>

</html>
