<!-- Menú lateral compacto -->
<div id="sidebar-icons" class="w-16 bg-white shadow-sm flex flex-col items-center py-4" style="transition: width 0.25s ease;">
  <!-- Logo + Toggle -->
  <div class="w-full px-1 mb-8 flex flex-col items-center gap-2">
    <img src="<?= $baseUrl ?>public/images/logo2.png" alt="Logo" class="w-12 h-auto">
    <button id="sidebar-toggle-btn" onclick="toggleSidebar()" title="Colapsar/Expandir menú"
      class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100 hover:bg-inventory hover:text-white text-gray-500 transition-colors">
      <i id="sidebar-toggle-icon" class="fas fa-chevron-left text-sm"></i>
    </button>
  </div>

  <!-- Menú principal compacto -->
  <?php $uri = \App\Core\Request::getUri(); ?>
  <div class="flex flex-col space-y-6">
    <a href="<?= $baseUrl ?>dashboard" title="Inicio" class="compact-menu <?= str_contains($uri, 'dashboard')?'active':'' ?> w-12 h-12 rounded-lg flex items-center justify-center cursor-pointer">
      <i class="fas fa-home menu-icon <?= str_contains($uri, 'dashboard')?'text-white':'text-gray-500' ?> text-xl"></i>
    </a>

    <a href="<?= $baseUrl ?>facturas" title="Facturación" class="compact-menu <?= str_contains($uri, 'factura')?'active':'' ?> w-12 h-12 rounded-lg flex items-center justify-center cursor-pointer">
      <i class="fas fa-file-invoice menu-icon <?= str_contains($uri, 'factura')?'text-white':'text-gray-500' ?> text-xl"></i>
    </a>

    <a href="<?= $baseUrl ?>clientes" title="Clientes" class="compact-menu <?= str_contains($uri, 'cliente')?'active':'' ?> w-12 h-12 rounded-lg flex items-center justify-center cursor-pointer">
      <i class="fas fa-users menu-icon <?= str_contains($uri, 'cliente')?'text-white':'text-gray-500' ?> text-xl"></i>
    </a>

    <a href="<?= $baseUrl ?>servicios" title="Servicios / Inventario" class="compact-menu <?= str_contains($uri, 'servicio')?'active':'' ?> w-12 h-12 rounded-lg flex items-center justify-center cursor-pointer">
      <i class="fas fa-cube menu-icon <?= str_contains($uri, 'servicio')?'text-white':'text-gray-500' ?> text-xl"></i>
    </a>

    <?php if(($user['is_superuser'] ?? false)): ?>
    <a href="<?= $baseUrl ?>configuracion" title="Configuración" class="compact-menu <?= str_contains($uri, 'configuracion')?'active':'' ?> w-12 h-12 rounded-lg flex items-center justify-center cursor-pointer">
      <i class="fas fa-cog menu-icon <?= str_contains($uri, 'configuracion')?'text-white':'text-gray-500' ?> text-xl"></i>
    </a>
    <?php endif; ?>
  </div>

</div>

<!-- Submenús -->
<div id="sidebar-menu" class="w-52 bg-gray-50 border-r border-gray-200 py-4 overflow-y-auto" style="transition: width 0.25s ease, opacity 0.25s ease;">
  <div class="submenu open">
    <h3 class="px-4 text-xs font-semibold text-blue-500 uppercase tracking-wider mb-3">Facturación</h3>
    <div class="space-y-1">
      <a href="<?= $baseUrl ?>facturas" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        Todas las Facturas
      </a>
      <a href="<?= $baseUrl ?>facturas/crear" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        Nueva Factura
      </a>
      <a href="<?= $baseUrl ?>servicios" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        Servicios
      </a>
    </div>

    <h3 class="px-4 mt-6 text-xs font-semibold text-blue-500 uppercase tracking-wider mb-3">Clientes</h3>
    <div class="space-y-1">
      <a href="<?= $baseUrl ?>clientes" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        Todos los Clientes
      </a>
      <a href="<?= $baseUrl ?>clientes/crear" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        Nuevo Cliente
      </a>
    </div>

    <h3 class="px-4 mt-6 text-xs font-semibold text-blue-500 uppercase tracking-wider mb-3">Acciones Rápidas</h3>
    <div class="space-y-1">
      <a href="<?= $baseUrl ?>facturas/crear" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        <i class="fas fa-file-invoice mr-2 text-info"></i> Nueva Factura
      </a>
      <a href="<?= $baseUrl ?>clientes/crear" class="block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-inventory">
        <i class="fas fa-user-plus mr-2 text-success"></i> Nuevo Cliente
      </a>
    </div>
  </div>
</div>

<script>
  // ── Sidebar collapse ──
  function toggleSidebar() {
    const menu   = document.getElementById('sidebar-menu');
    const icon   = document.getElementById('sidebar-toggle-icon');
    const isOpen = menu.style.display !== 'none';

    if (isOpen) {
      menu.style.opacity = '0';
      setTimeout(() => { menu.style.display = 'none'; }, 220);
      icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
      localStorage.setItem('sidebarCollapsed', '1');
    } else {
      menu.style.display = '';
      setTimeout(() => { menu.style.opacity = '1'; }, 10);
      icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
      localStorage.setItem('sidebarCollapsed', '0');
    }
  }

  // Restaurar estado al cargar
  (function() {
    if (localStorage.getItem('sidebarCollapsed') === '1') {
      const menu = document.getElementById('sidebar-menu');
      const icon = document.getElementById('sidebar-toggle-icon');
      if (menu) { menu.style.display = 'none'; menu.style.opacity = '0'; }
      if (icon) { icon.classList.replace('fa-chevron-left', 'fa-chevron-right'); }
    }
  })();
</script>
