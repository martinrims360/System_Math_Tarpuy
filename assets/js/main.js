// assets/js/main.js

// Sidebar toggle mobile
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// Cerrar sidebar al hacer click fuera (mobile)
document.addEventListener('click', function (e) {
  const sidebar = document.getElementById('sidebar');
  const toggle  = document.querySelector('.sidebar-toggle');
  if (sidebar && toggle &&
      !sidebar.contains(e.target) &&
      !toggle.contains(e.target)) {
    sidebar.classList.remove('open');
  }
});

// Confirmar eliminación
function confirmDelete(url, nombre) {
  if (confirm('¿Eliminar "' + nombre + '"? Esta acción no se puede deshacer.')) {
    window.location.href = url;
  }
}

// Auto-cerrar flash messages
document.addEventListener('DOMContentLoaded', function () {
  const flash = document.querySelectorAll('.alert-flash');
  flash.forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 3500);
  });
});