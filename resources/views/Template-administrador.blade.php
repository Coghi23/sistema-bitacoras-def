<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <title>Bitácoras HHC - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('Css/Sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/tabla.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Modals.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Footer.css') }}">
    <link rel="icon" href="https://covao.ed.cr/wp-content/uploads/2025/02/cropped-favicon-32x32.png" sizes="32x32">

    @stack('css')
</head>

<body>

    

    {{-- Sidebar y contenido --}}
    <div>
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

            <a href="{{ url('index.html') }}" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-house-door-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Inicio">Inicio</div>
            </a>

            <div class="sidebar-item" id="personal-btn" data-bs-toggle="tooltip" title="Manejo de Personal">
                <div class="icon-circle"><i class="bi bi-people-fill"></i></div>
                <div class="label">Personal</div>
            </div>

            <div class="submenu-popover" id="submenu">
                <div class="submenu-arrow"></div>

                <a href="#" class="submenu-item" style="text-decoration: none;"><i class="bi bi-person"></i> Docentes</a>
                <a href="#" class="submenu-item" style="text-decoration: none;"><i class="bi bi-tools"></i> Soporte</a>
                <a href="{{ asset('seccion') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-diagram-3"></i> Sección</a>
                <a href="{{ asset('institucion') }}" class="submenu-item"style="text-decoration: none;"><i class="bi bi-bank"></i>Institución</a>
                <a href="{{ asset('sub-area') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-diagram-2"></i> SubÁrea</a>
                <a href="{{ asset('especialidad') }}" class="submenu-item" style="text-decoration: none;"><i class="bi bi-journal-bookmark"></i> Especialidad</a>
            </div>

            <a href="{{ url('horario.html') }}" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-calendar-week-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Horarios">Horarios</div>
            </a>

            <a href="{{ url('recintos.html') }}" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-building-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Recintos">Recintos</div>
            </a>

            <a href="{{ url('reportes.html') }}" class="sidebar-item">
                <div class="icon-circle"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Reportes">Reportes</div>
            </a>

            <a href="{{ url('login.html') }}" class="sidebar-item mt-auto mb-3">
                <div class="icon-circle"><i class="bi bi-box-arrow-right"></i></div>
                <div class="label" data-bs-toggle="tooltip" title="Salir">Salir</div>
            </a>
        </div>
    </div>
    </div>

    <div class="wrapper">
        <div class="main-content p-4">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!-- Inicializador de DataTables -->
    <script>
        $(function () {
            let tabla = $('#datatablesSimple');
            if (tabla.length) {
                tabla.DataTable({
                    responsive: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });
                console.log("DataTables inicializado correctamente");
            }
        });
    </script>
    <!-- JS personalizados -->
    <script src="{{ asset('JS/Sidebar.js') }}"></script>

    @stack('js')
</body>

</html>