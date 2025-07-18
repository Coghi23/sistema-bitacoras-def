const btn = document.getElementById('personal-btn');
const submenu = document.getElementById('submenu');
let visible = false;

btn.addEventListener('click', (e) => {
  e.stopPropagation();
  visible = !visible;
  submenu.style.display = visible ? 'block' : 'none';
  if (visible) {
    submenu.style.top = `${btn.offsetTop}px`;
  }
});

document.addEventListener('click', function (event) {
  if (!btn.contains(event.target) && !submenu.contains(event.target)) {
    submenu.style.display = 'none';
    visible = false;
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