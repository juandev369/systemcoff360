<?php
/**
 * ╔══════════════════════════════════════════════╗
 * ║  SystemCOFF 360 — Layout: Sidebar           ║
 * ║  Ruta: views/layouts/sidebar.php            ║
 * ╚══════════════════════════════════════════════╝
 */

// Página activa — se define antes del include del header
// Ejemplo: $paginaActiva = 'usuarios';
$paginaActiva = $paginaActiva ?? '';
$rol          = $_SESSION['usuario']['rol'] ?? 'trabajador';

// Menú de navegación por secciones
$menu = [
    'PRINCIPAL' => [
        ['id' => 'dashboard',      'ico' => 'fa-home',           'lbl' => 'Dashboard',        'url' => 'admin.php'],
        ['id' => 'notificaciones', 'ico' => 'fa-bell',           'lbl' => 'Alertas',          'url' => 'notificaciones.php', 'badge' => '5', 'badge_color' => 'bg-red-500'],
    ],
    'PRODUCCIÓN' => [
        ['id' => 'lotes',          'ico' => 'fa-seedling',       'lbl' => 'Lotes',            'url' => 'lotes.php'],
        ['id' => 'cosechas',       'ico' => 'fa-leaf',           'lbl' => 'Cosechas',         'url' => 'cosechas.php'],
        ['id' => 'ventas',         'ico' => 'fa-dollar-sign',    'lbl' => 'Ventas',           'url' => 'ventas.php'],
    ],
    'OPERACIONES' => [
        ['id' => 'tareas',         'ico' => 'fa-clipboard-list', 'lbl' => 'Tareas',           'url' => 'tareas.php',   'badge' => '7', 'badge_color' => 'bg-green-600'],
        ['id' => 'nomina',         'ico' => 'fa-credit-card',    'lbl' => 'Nómina',           'url' => 'nomina.php'],
    ],
    'RECURSOS' => [
        ['id' => 'inventario',     'ico' => 'fa-boxes',          'lbl' => 'Inventario',       'url' => 'inventario.php'],
        ['id' => 'insumos',        'ico' => 'fa-flask',          'lbl' => 'Insumos',          'url' => 'insumos.php',  'badge' => '1', 'badge_color' => 'bg-red-500'],
        ['id' => 'epp',            'ico' => 'fa-hard-hat',       'lbl' => 'EPP',              'url' => 'epp.php'],
        ['id' => 'herramientas',   'ico' => 'fa-tools',          'lbl' => 'Herramientas',     'url' => 'herramientas.php'],
        ['id' => 'proveedores',    'ico' => 'fa-industry',       'lbl' => 'Proveedores',      'url' => 'proveedores.php'],
        ['id' => 'compras',        'ico' => 'fa-shopping-cart',  'lbl' => 'Compras',          'url' => 'compras.php'],
    ],
    'ANÁLISIS' => [
        ['id' => 'reportes',       'ico' => 'fa-chart-bar',      'lbl' => 'Reportes',         'url' => 'reportes.php'],
    ],
];

// Solo el admin ve la gestión de usuarios
if ($rol === 'administrador') {
    $menu['ANÁLISIS'][] = ['id' => 'usuarios', 'ico' => 'fa-users', 'lbl' => 'Usuarios', 'url' => 'admin.php'];
}

$menu['ANÁLISIS'][] = ['id' => 'perfil', 'ico' => 'fa-user-circle', 'lbl' => 'Mi Perfil', 'url' => 'perfil.php'];
?>

<!-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
<aside id="sidebar"
       class="fixed top-[52px] left-0 h-[calc(100vh-52px)] overflow-y-auto flex flex-col z-40
              transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

    <!-- LOGO -->
    <div class="px-4 py-4 border-b border-green-900/25 flex-shrink-0">
        <div style="font-size:14px;font-weight:700;color:#86efac;font-family:serif">SystemCOFF 360</div>
        <div style="font-size:8px;color:#3a6b4a;letter-spacing:2px;text-transform:uppercase;margin-top:1px">Gestión de Fincas</div>
        <span class="inline-block mt-2 px-2 py-0.5 text-[8.5px] font-bold uppercase tracking-wide rounded-full"
              style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.15);color:#22c55e">
            JD Solutions
        </span>
    </div>

    <!-- NAVEGACIÓN -->
    <nav class="flex-1 py-3 overflow-y-auto">
        <?php foreach ($menu as $seccion => $items): ?>
        <div class="mb-1">
            <div class="px-4 pt-3 pb-1 text-[8px] font-black uppercase tracking-[1.8px]"
                 style="color:rgba(58,107,74,.55)"><?= $seccion ?></div>

            <?php foreach ($items as $item): ?>
                <?php $activo = $paginaActiva === $item['id'] ? 'active' : ''; ?>
                <a href="<?= $item['url'] ?>"
                   class="nav-item <?= $activo ?> flex items-center gap-2.5 px-4 py-2 cursor-pointer no-underline">
                    <i class="fas <?= $item['ico'] ?> text-[12px] w-4 text-center flex-shrink-0"></i>
                    <span><?= $item['lbl'] ?></span>
                    <?php if (isset($item['badge'])): ?>
                    <span class="ml-auto <?= $item['badge_color'] ?> text-white text-[8.5px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
                        <?= $item['badge'] ?>
                    </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </nav>

    <!-- USUARIO ACTIVO (bottom) -->
    <div class="px-4 py-3 border-t border-green-900/25 flex-shrink-0 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
             style="background:linear-gradient(135deg,#15803d,#22c55e)">
            <?= strtoupper(substr($_SESSION['usuario']['nombres'], 0, 1)) ?>
        </div>
        <div class="overflow-hidden">
            <div class="text-[11.5px] font-semibold text-green-200 truncate">
                <?= htmlspecialchars($_SESSION['usuario']['nombres'] . ' ' . $_SESSION['usuario']['apellidos']) ?>
            </div>
            <div class="text-[9px] text-green-700 capitalize"><?= $_SESSION['usuario']['rol'] ?></div>
        </div>
    </div>
</aside>

<!-- Overlay mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden"></div>

<!-- Spacer para empujar el main en desktop -->
<div class="hidden lg:block flex-shrink-0" style="width:222px"></div>

<script>
(function(){
    const btn      = document.getElementById('sidebar-toggle');
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');

    function openSidebar(){
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
    function closeSidebar(){
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    btn?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
})();
</script>
