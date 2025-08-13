const btn = document.getElementById('personal-btn');
const submenu = document.getElementById('submenu');
let visible = false;

const recintoBtn = document.getElementById('recinto-btn');
const recintoSubmenu = document.getElementById('recinto-submenu');
let recintoVisible = false;

btn.addEventListener('click', (e) => {
  e.stopPropagation();
  // Cerrar el submenú de recinto si está abierto
  if (recintoVisible) {
    recintoSubmenu.style.display = 'none';
    recintoVisible = false;
  }
  visible = !visible;
  submenu.style.display = visible ? 'block' : 'none';
  if (visible) {
    submenu.style.top = `${btn.offsetTop}px`;
  }
});

recintoBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  // Cerrar el submenú de personal si está abierto
  if (visible) {
    submenu.style.display = 'none';
    visible = false;
  }
  recintoVisible = !recintoVisible;
  recintoSubmenu.style.display = recintoVisible ? 'block' : 'none';
  if (recintoVisible) {
    recintoSubmenu.style.top = `${recintoBtn.offsetTop}px`;
  }
});

document.addEventListener('click', function (event) {
  // Cerrar submenú de personal
  if (!btn.contains(event.target) && !submenu.contains(event.target)) {
    submenu.style.display = 'none';
    visible = false;
  }
  // Cerrar submenú de recinto
  if (!recintoBtn.contains(event.target) && !recintoSubmenu.contains(event.target)) {
    recintoSubmenu.style.display = 'none';
    recintoVisible = false;
  }
});


// JS para toggle de sidebar
document.getElementById('menu-toggle').addEventListener('click', function () {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('d-none');
});


function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('show');
}

// Cierra el sidebar al hacer clic fuera
document.addEventListener('click', function (event) {
  const sidebar = document.getElementById('sidebar');
  const hamburger = document.querySelector('.hamburger');
  if (
    window.innerWidth <= 767 &&
    sidebar.classList.contains('show') &&
    !sidebar.contains(event.target) &&
    !hamburger.contains(event.target)
  ) {
    sidebar.classList.remove('show');
  }
});