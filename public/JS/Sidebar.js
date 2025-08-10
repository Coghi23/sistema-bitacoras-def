// Código para los menús desplegables
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando menús...');
    
    // Menú Personal
    const personalBtn = document.getElementById('personal-btn');
    const personalSubmenu = document.getElementById('submenu');
    let personalVisible = false;

    // Menú Recintos
    const recintosBtn = document.getElementById('recintos-btn');
    const recintosSubmenu = document.getElementById('submenu-recintos');
    let recintosVisible = false;

    console.log('Personal button:', personalBtn);
    console.log('Personal submenu:', personalSubmenu);
    console.log('Recintos button:', recintosBtn);
    console.log('Recintos submenu:', recintosSubmenu);

    // Función para cerrar todos los menús
    function closeAllMenus() {
        if (personalSubmenu) {
            personalSubmenu.style.display = 'none';
            personalVisible = false;
        }
        if (recintosSubmenu) {
            recintosSubmenu.style.display = 'none';
            recintosVisible = false;
        }
    }

    // Event listener para Personal
    if (personalBtn) {
        personalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Click en Personal');
            
            // Cerrar menú de recintos si está abierto
            if (recintosSubmenu && recintosVisible) {
                recintosSubmenu.style.display = 'none';
                recintosVisible = false;
            }
            
            personalVisible = !personalVisible;
            if (personalSubmenu) {
                personalSubmenu.style.display = personalVisible ? 'block' : 'none';
                if (personalVisible) {
                    personalSubmenu.style.top = personalBtn.offsetTop + 'px';
                    personalSubmenu.style.left = '95px';
                }
            }
        });
    }

    // Event listener para Recintos
    if (recintosBtn) {
        recintosBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Click en Recintos');
            
            // Cerrar menú personal si está abierto
            if (personalSubmenu && personalVisible) {
                personalSubmenu.style.display = 'none';
                personalVisible = false;
            }
            
            recintosVisible = !recintosVisible;
            if (recintosSubmenu) {
                recintosSubmenu.style.display = recintosVisible ? 'block' : 'none';
                if (recintosVisible) {
                    recintosSubmenu.style.position = 'absolute';
                    recintosSubmenu.style.top = recintosBtn.offsetTop + 'px';
                    recintosSubmenu.style.left = '95px';
                    recintosSubmenu.style.zIndex = '1000';
                    recintosSubmenu.style.backgroundColor = '#7491C1';
                    recintosSubmenu.style.borderRadius = '10px';
                    recintosSubmenu.style.padding = '15px 10px';
                    recintosSubmenu.style.minWidth = '220px';
                    recintosSubmenu.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.3)';
                }
                console.log('Menú recintos:', recintosVisible ? 'abierto' : 'cerrado');
                console.log('Top:', recintosBtn.offsetTop, 'Display:', recintosSubmenu.style.display);
            }
        });
    } else {
        console.error('No se encontró el botón de Recintos');
    }

    // Cerrar menús al hacer clic fuera
    document.addEventListener('click', function(e) {
        const clickedPersonal = personalBtn && (personalBtn.contains(e.target) || (personalSubmenu && personalSubmenu.contains(e.target)));
        const clickedRecintos = recintosBtn && (recintosBtn.contains(e.target) || (recintosSubmenu && recintosSubmenu.contains(e.target)));
        
        if (!clickedPersonal && !clickedRecintos) {
            closeAllMenus();
        }
    });
});


// JS para toggle de sidebar
document.addEventListener('DOMContentLoaded', function() {
  // Verificar si existe el elemento antes de agregar el listener
  const menuToggle = document.getElementById('menu-toggle');
  if (menuToggle) {
    menuToggle.addEventListener('click', function () {
      const sidebar = document.getElementById('sidebar');
      if (sidebar) {
        sidebar.classList.toggle('d-none');
      }
    });
  }
});

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    sidebar.classList.toggle('show');
  }
}

// Cierra el sidebar al hacer clic fuera
document.addEventListener('DOMContentLoaded', function() {
  document.addEventListener('click', function (event) {
    const sidebar = document.getElementById('sidebar');
    const hamburger = document.querySelector('.hamburger');
    if (
      sidebar && hamburger &&
      window.innerWidth <= 767 &&
      sidebar.classList.contains('show') &&
      !sidebar.contains(event.target) &&
      !hamburger.contains(event.target)
    ) {
      sidebar.classList.remove('show');
    }
  });
});