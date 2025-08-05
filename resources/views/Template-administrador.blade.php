<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Bitácoras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Css/Sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/tabla.css') }}" />
    <link rel="stylesheet" href="{{ asset('Css/Modals.css') }}">
    
    <link rel="icon" href="https://covao.ed.cr/wp-content/uploads/2025/02/cropped-favicon-32x32.png" sizes="32x32">
</head>

<body>




    <div id="sidebar-navbar">
        <div class="topbar">
            <button class="hamburger d-md-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="left-section d-none d-md-flex">
                <img src="https://academiashhc.com/wp-content/uploads/2022/09/AcademiasB.png" alt="Logo Academias" class="right-logo d-none d-md-block" />

                <div class="separator"></div>
                <img src="https://covao.ed.cr/wp-content/uploads/2024/12/image-removebg-preview-3.png" alt="Logo COVAO" class="logo" />
            </div>
            <div class="title">Bitácoras HHC</div>
            <img src="https://nocturno.covao.ed.cr/wp-content/uploads/2024/11/logo-covao-nocturno.webp" alt="Logo COVAO Nocturno" class="logo" />
        </div>

        <div class="yellow-line"></div>
        <div class="sidebar-separator d-none d-md-block"></div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="https://academiashhc.com/wp-content/uploads/2022/09/AcademiasB.png" alt="Logo Academias" class="right-logo d-none d-md-block" />
            </div>

            <a href="{{ route('dashboard') }}" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-house-door-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Inicio">Inicio</div>
            </a>

            <div class="sidebar-item" id="personal-btn" data-bs-toggle="tooltip" title="Manejo de Personal">
                <div class="icon-circle"><i class="bi bi-people-fill"></i></div>
                <div class="label">Personal</div>
            </div>

            <div class="submenu-popover" id="submenu">
                <div class="submenu-arrow"></div>

                <a href="{{ asset('usuario') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-person"></i> Docentes</a>
                <a href="{{ asset('usuario') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-tools"></i> Soporte</a>
                <a href="{{ asset('seccion') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-diagram-3"></i> Sección</a>
                <a href="{{ asset('institucion') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-bank"></i> Institución</a>
                <a href="{{ asset('especialidad') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-journal-bookmark"></i> Especialidad</a>
                <a href="{{ asset('subarea') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-diagram-2"></i> SubÁrea</a>
            </div>

            <a href="{{ asset('horario') }}" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-calendar-week-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Horarios">Horarios</div>
            </a>

            <a href="{{ asset('recinto') }}" class="sidebar-item"><div class="icon-circle"><i class="bi bi-building-fill"></i></div><div class="label" data-bs-toggle="tooltip" title="Recintos">Recintos</div></a>

            <a href="{{ url('reportes.html') }}" class="sidebar-item">
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

    <div class="wrapper">
        <div class="main-content p-4">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('JS/Sidebar.js') }}"></script>
    <script src="{{ asset('JS/modals-create-especialidad.js') }}"></script>
    <script src="{{ asset('JS/modals-edit-especialidad.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>
