<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $page_title ?? 'ChinaBox - Sistema de Facturación' ?></title>

  <!-- Aplicar tema ANTES de renderizar para evitar flash -->
  <script>
    if (localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    }
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: "#3B82F6",
            secondary: "#8B5CF6",
            dark: "#1F2937",
            light: "#F9FAFB",
            success: "#10B981",
            danger: "#EF4444",
            warning: "#F59E0B",
            info: "#06B6D4",
            inventory: "#0EA5E9",
          },
        },
      },
    };
  </script>

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

    body { font-family: "Inter", sans-serif; background: #f8fafc; }

    .compact-menu { transition: all 0.3s ease; }
    .compact-menu.active { background-color: #0ea5e9; color: white; }
    .compact-menu.active .menu-icon { color: white; }

    .submenu { transition: all 0.3s ease; max-height: 0; overflow: hidden; }
    .submenu.open { max-height: none; }

    .metric-card { transition: all 0.3s ease; border-left: 3px solid #0ea5e9; }
    .metric-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14,165,233,0.15); }

    .action-btn { transition: all 0.2s ease; }
    .action-btn:hover { transform: scale(1.1); }

    /* ── DARK MODE GLOBAL OVERRIDES ── */
    .dark body                        { background: #0f172a; color: #e2e8f0; }

    /* Fondos */
    .dark .bg-white                   { background-color: #1e293b !important; }
    .dark .bg-gray-50                 { background-color: #0f172a !important; }
    .dark .bg-gray-100                { background-color: #273044 !important; }
    .dark .bg-gray-800                { background-color: #0f172a !important; }

    /* Textos */
    .dark .text-gray-900              { color: #f1f5f9 !important; }
    .dark .text-gray-800              { color: #e2e8f0 !important; }
    .dark .text-gray-700              { color: #cbd5e1 !important; }
    .dark .text-gray-600              { color: #94a3b8 !important; }
    .dark .text-gray-500              { color: #64748b !important; }
    .dark .text-gray-400              { color: #475569 !important; }
    .dark .text-dark                  { color: #f1f5f9 !important; }

    /* Bordes y divisores */
    .dark .border-gray-200            { border-color: #334155 !important; }
    .dark .border-gray-300            { border-color: #475569 !important; }
    .dark .border-gray-100            { border-color: #1e293b !important; }
    .dark .divide-gray-200 > *        { border-color: #334155 !important; }
    .dark .divide-gray-100 > *        { border-color: #1e293b !important; }
    .dark .border-b                   { border-color: #334155 !important; }

    /* Header y sidebar */
    .dark header.bg-white             { background-color: #1e293b !important; box-shadow: 0 1px 3px rgba(0,0,0,0.4) !important; }
    .dark #sidebar-icons              { background-color: #0f172a !important; }
    .dark #sidebar-menu               { background-color: #1e293b !important; border-color: #334155 !important; }
    .dark #sidebar-menu a             { color: #cbd5e1 !important; }
    .dark #sidebar-menu a:hover       { background-color: #273044 !important; color: #0ea5e9 !important; }

    /* Tablas */
    .dark thead.bg-gray-50            { background-color: #1e293b !important; }
    .dark tbody tr:hover              { background-color: #273044 !important; }
    .dark table                       { color: #e2e8f0; }

    /* Inputs, selects, textarea */
    .dark input:not([type=checkbox]):not([type=radio]),
    .dark select,
    .dark textarea                    { background-color: #273044 !important; border-color: #475569 !important; color: #e2e8f0 !important; }
    .dark input::placeholder          { color: #64748b !important; }

    /* Cards / paneles */
    .dark .rounded-xl,
    .dark .rounded-lg                 { }
    .dark .shadow-sm                  { box-shadow: 0 1px 3px rgba(0,0,0,0.5) !important; }

    /* Badges estado */
    .dark .bg-yellow-100              { background-color: #422006 !important; }
    .dark .text-yellow-800            { color: #fbbf24 !important; }
    .dark .bg-green-100               { background-color: #052e16 !important; }
    .dark .text-green-800             { color: #4ade80 !important; }
    .dark .bg-red-100                 { background-color: #450a0a !important; }
    .dark .text-red-800               { color: #f87171 !important; }
    .dark .bg-blue-50                 { background-color: #172554 !important; }
    .dark .bg-indigo-50               { background-color: #1e1b4b !important; }
    .dark .bg-green-50                { background-color: #052e16 !important; }
    .dark .text-green-600             { color: #4ade80 !important; }
    .dark .text-red-500               { color: #f87171 !important; }
    .dark .text-red-600               { color: #f87171 !important; }

    /* Menu compacto en dark */
    .dark .compact-menu:not(.active)  { background-color: transparent !important; }
    .dark .compact-menu:not(.active) .menu-icon { color: #94a3b8 !important; }
    .dark .compact-menu:not(.active):hover { background-color: #1e293b !important; }

    /* Flash messages */
    .dark .bg-green-100.border-green-200  { background-color: #052e16 !important; border-color: #166534 !important; color: #4ade80 !important; }
    .dark .bg-red-100.border-red-200      { background-color: #450a0a !important; border-color: #991b1b !important; color: #f87171 !important; }

    /* Botón toggle */
    #theme-toggle { transition: all 0.2s ease; }
  </style>
  <?= $extra_css ?? '' ?>
</head>

<body class="min-h-screen bg-gray-50 flex">

  <?php \App\Core\View::render('includes/sidebar', ['baseUrl' => $baseUrl, 'user' => $user], false) ?>

  <!-- Contenido principal -->
  <div class="flex-1 overflow-auto">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="px-6 py-4">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-xl font-bold text-dark">
              <?= $page_title ?? 'Sistema de Facturación' ?>
            </h2>
            <p class="text-gray-600 text-sm mt-1">
              <?= $page_subtitle ?? 'ChinaBox - Gestión de facturas y clientes' ?>
            </p>
          </div>

          <div class="flex items-center space-x-3">
            <!-- Botones de acción rápida -->
            <div class="hidden md:flex space-x-2">
              <a href="<?= $baseUrl ?>facturas/crear" class="bg-inventory hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-lg flex items-center action-btn">
                <i class="fas fa-plus-circle mr-1 text-xs"></i> Factura
              </a>
              <a href="<?= $baseUrl ?>clientes/crear" class="bg-success hover:bg-green-600 text-white text-xs font-medium py-1.5 px-3 rounded-lg flex items-center action-btn">
                <i class="fas fa-user-plus mr-1 text-xs"></i> Cliente
              </a>
              <a href="<?= $baseUrl ?>servicios/crear" class="bg-warning hover:bg-yellow-600 text-white text-xs font-medium py-1.5 px-3 rounded-lg flex items-center action-btn">
                <i class="fas fa-cube mr-1 text-xs"></i> Servicio
              </a>
            </div>

            <!-- Toggle Dark/Light -->
            <button id="theme-toggle" onclick="toggleTheme()"
              class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300"
              title="Cambiar tema">
              <i id="theme-icon" class="fas fa-moon text-sm"></i>
            </button>

            <!-- Notificaciones -->
            <div class="relative">
              <i class="fas fa-bell text-gray-500"></i>
              <span class="absolute -top-1 -right-1 bg-danger w-3 h-3 flex items-center justify-center rounded-full text-xs text-white"></span>
            </div>

            <!-- Usuario -->
            <div class="flex items-center">
              <div class="w-8 h-8 rounded-full bg-inventory bg-opacity-20 flex items-center justify-center">
                <i class="fas fa-user text-inventory text-sm"></i>
              </div>
              <div class="ml-2">
                <p class="text-sm font-medium text-dark">
                  <?php if($user): ?>
                    <?= trim(($user['first_name']??'') . ' ' . ($user['last_name']??'')) ?: $user['username'] ?>
                  <?php else: ?>
                    Usuario
                  <?php endif; ?>
                </p>
                <a href="<?= $baseUrl ?>logout" class="text-xs text-red-500 hover:text-red-700">Cerrar Sesión</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Alertas Flash -->
    <?php if (!empty($flash_messages)): ?>
      <div class="px-6 pt-4">
        <?php foreach ($flash_messages as $msg): ?>
          <?php
            $bg   = $msg['type'] == 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
            $icon = $msg['type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
          ?>
          <div class="p-4 mb-4 text-sm <?= $bg ?> border rounded-lg flex items-center" role="alert">
            <i class="fas <?= $icon ?> mr-2"></i>
            <span class="font-medium"><?= $msg['message'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Contenido -->
    <main><?= $content ?></main>
  </div>

  <script>
    // ── Menú compacto ──
    const menuItems = document.querySelectorAll(".compact-menu");
    menuItems.forEach((item) => {
      item.addEventListener("click", function () {
        menuItems.forEach((i) => {
          i.classList.remove("active");
          i.querySelector(".menu-icon").classList.remove("text-white");
          i.querySelector(".menu-icon").classList.add("text-gray-500");
        });
        this.classList.add("active");
        this.querySelector(".menu-icon").classList.remove("text-gray-500");
        this.querySelector(".menu-icon").classList.add("text-white");
      });
    });

    // ── Dark / Light toggle ──
    function applyTheme(theme) {
      const icon = document.getElementById('theme-icon');
      if (theme === 'dark') {
        document.documentElement.classList.add('dark');
        icon.classList.replace('fa-moon', 'fa-sun');
      } else {
        document.documentElement.classList.remove('dark');
        icon.classList.replace('fa-sun', 'fa-moon');
      }
    }

    function toggleTheme() {
      const isDark = document.documentElement.classList.contains('dark');
      const next   = isDark ? 'light' : 'dark';
      localStorage.setItem('theme', next);
      applyTheme(next);
    }

    // Inicializar ícono al cargar
    applyTheme(document.documentElement.classList.contains('dark') ? 'dark' : 'light');
  </script>
  <?= $extra_js ?? '' ?>
</body>
</html>
