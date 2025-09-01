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
    <link rel="stylesheet" href="{{ asset('Css/delete-alerts.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Inicio.css') }}">

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
                <img src="https://covao.ed.cr/wp-content/uploads/2025/01/Especialidades-logos-05-e1736905518920.webp" alt="Logo COVAO Nocturno" class="logo" />
               
            </div>
            <div class="title">SIREBI</div>
            <img src="https://covao.ed.cr/wp-content/uploads/2024/12/image-removebg-preview-3.png" alt="Logo COVAO" class="logo" />
           
        </div>

        <div class="yellow-line"></div>
        <div class="sidebar-separator d-none d-md-block"></div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-content">
                <div class="sidebar-logo">
                    <img src="https://academiashhc.com/wp-content/uploads/2022/09/AcademiasB.png" alt="Logo Academias" class="right-logo d-none d-md-block" />
                </div>

                <div class="sidebar-section">
                    <a href="{{ route('dashboard') }}" class="sidebar-item">
                        <div class="icon-circle"><i class="bi bi-house-door-fill"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="Inicio">Inicio</div>
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-item has-subitems" onclick="toggleSidebarSubmenu('roles-subitems')" style="cursor:pointer;">
                        <div class="icon-circle"><i class="bi bi-shield-lock"></i></div>
                        <div class="label">Roles y permisos</div>
                    </div>
                    <div class="sidebar-subitems" id="roles-subitems" style="display:none;">
                        <a href="{{ asset('role') }}" class="sidebar-item subitem"><i class="bi bi-bank"></i> Roles</a>
                        <a href="{{ asset('permisos') }}" class="sidebar-item subitem"><i class="bi bi-journal-bookmark"></i> Permisos</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <a href="{{ asset('usuario') }}" class="sidebar-item">
                        <div class="icon-circle"><i class="bi bi-person"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="Horarios">Usuarios</div>
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-item has-subitems" onclick="toggleSidebarSubmenu('otras-subitems')" style="cursor:pointer;">
                        <div class="icon-circle"><i class="bi bi-clipboard-fill"></i></div>
                        <div class="label">Otras opciones</div>
                    </div>
                    <div class="sidebar-subitems" id="otras-subitems" style="display:none;">
                        <a href="{{ asset('institucion') }}" class="sidebar-item subitem"><i class="bi bi-bank"></i> Institución</a>
                        <a href="{{ asset('especialidad') }}" class="sidebar-item subitem"><i class="bi bi-journal-bookmark"></i> Especialidad</a>
                        <a href="{{ asset('seccion') }}" class="sidebar-item subitem"><i class="bi bi-diagram-3"></i> Sección</a>
                        <a href="{{ asset('subarea') }}" class="sidebar-item subitem"><i class="bi bi-diagram-2"></i> SubÁrea</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <a href="{{ asset('llave') }}" class="sidebar-item">
                        <div class="icon-circle"><i class="bi bi-key-fill"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="Llaves">Llaves</div>
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-item has-subitems" onclick="toggleSidebarSubmenu('recinto-subitems')" style="cursor:pointer;">
                        <div class="icon-circle"><i class="bi bi-building-fill"></i></div>
                        <div class="label">Recintos</div>
                    </div>
                    <div class="sidebar-subitems" id="recinto-subitems" style="display:none;">
                        <a href="{{ asset('tipoRecinto') }}" class="sidebar-item subitem"><i class="bi bi-building-fill-gear"></i> Tipo de Recinto</a>
                        <a href="{{ asset('estadoRecinto') }}" class="sidebar-item subitem"><i class="bi bi-building-fill-exclamation"></i> Estado de Recinto</a>
                        <a href="{{ asset('recinto') }}" class="sidebar-item subitem"><i class="bi bi-building-fill-add"></i>Crear Recintos</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <a href="{{ asset('horario') }}" class="sidebar-item">
                        <div class="icon-circle"><i class="bi bi-calendar-week-fill"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="Horarios">Horarios</div>
                    </a>
                </div>

                <div class="sidebar-section">
                    <a href="{{ route('admin.qr.index') }}" class="sidebar-item">
                        <div class="icon-circle"><i class="bi bi-qr-code-scan"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="QR Temporales">QR Temporales</div>
                    </a>
                </div>

                <div class="sidebar-section">
                    <a href="{{ route('evento.index') }}" class="sidebar-item">
                        <div class="icon-circle"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                        <div class="label" data-bs-toggle="tooltip" title="Reportes">Reportes</div>
                    </a>
                </div>
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
        </div>
    </div>

















    <div class="wrapper">
        <div class="main-content p-4">
            @yield('content')
        </div>
    </div>

    <script>
    function toggleSidebarSubmenu(id) {
        var el = document.getElementById(id);
        var parent = el.previousElementSibling;
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'block';
            parent.classList.add('active');
        } else {
            el.style.display = 'none';
            parent.classList.remove('active');
        }
    }
    </script>
    <script src="{{ asset('JS/Sidebar.js') }}"></script>
    <script src="{{ asset('JS/modals-create-especialidad.js') }}"></script>
    <script src="{{ asset('JS/modals-edit-especialidad.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
    <!-- Scripts específicos de páginas -->
    @stack('scripts')
</body>

</html>
