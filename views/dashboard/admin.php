<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../Config/database.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();

$usuarioSesion = $_SESSION['usuario'];

/**
 * Función segura para consultas de un solo valor.
 * Si una tabla o columna no existe, no rompe el dashboard.
 */
function valorSeguro(PDO $db, string $sql, $default = 0) {
    try {
        $stmt = $db->query($sql);
        return $stmt->fetchColumn() ?? $default;
    } catch (Throwable $e) {
        return $default;
    }
}

/**
 * Función segura para consultas de varias filas.
 */
function filasSeguras(PDO $db, string $sql): array {
    try {
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

$totalUsuarios      = valorSeguro($db, "SELECT COUNT(*) FROM usuario");
$totalAdmins        = valorSeguro($db, "SELECT COUNT(*) FROM usuario WHERE id_rol = 1");
$totalTrabajadores  = valorSeguro($db, "SELECT COUNT(*) FROM usuario WHERE id_rol = 2");
$totalActivos       = valorSeguro($db, "SELECT COUNT(*) FROM usuario WHERE estado = 'activo'");
$totalInactivos     = valorSeguro($db, "SELECT COUNT(*) FROM usuario WHERE estado <> 'activo'");

$totalLotes         = valorSeguro($db, "SELECT COUNT(*) FROM lote");
$totalTareas        = valorSeguro($db, "SELECT COUNT(*) FROM tarea");
$totalInsumos       = valorSeguro($db, "SELECT COUNT(*) FROM insumo");
$totalHerramientas  = valorSeguro($db, "SELECT COUNT(*) FROM herramienta");
$totalActivosFinca  = valorSeguro($db, "SELECT COUNT(*) FROM activo");
$totalProveedores   = valorSeguro($db, "SELECT COUNT(*) FROM proveedor");

$totalCompras       = valorSeguro($db, "SELECT COUNT(*) FROM compra");
$totalVentas        = valorSeguro($db, "SELECT COUNT(*) FROM venta");
$totalCosechas      = valorSeguro($db, "SELECT COUNT(*) FROM cosecha");
$totalMantenimientos = valorSeguro($db, "SELECT COUNT(*) FROM mantenimiento");

$usuariosRecientes = filasSeguras($db, "
    SELECT u.id_usuario, u.nombre, u.correo, u.estado, u.fecha_registro, r.nombre AS rol
    FROM usuario u
    LEFT JOIN rol r ON u.id_rol = r.id_rol
    ORDER BY u.fecha_registro DESC
    LIMIT 6
");

$tareasRecientes = filasSeguras($db, "
    SELECT *
    FROM tarea
    ORDER BY 1 DESC
    LIMIT 5
");

$cosechasRecientes = filasSeguras($db, "
    SELECT *
    FROM cosecha
    ORDER BY 1 DESC
    LIMIT 5
");

$notificaciones = filasSeguras($db, "
    SELECT *
    FROM notificacion
    ORDER BY 1 DESC
    LIMIT 5
");

function porcentaje($parte, $total) {
    if ($total <= 0) return 0;
    return round(($parte / $total) * 100);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador — SystemCOFF 360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f0fdf4;
        }

        .glass {
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(14px);
        }

        .sidebar {
            background: linear-gradient(180deg, #052e16 0%, #064e3b 60%, #022c22 100%);
        }

        .card-hover {
            transition: all .2s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(22, 101, 52, .15);
        }

        .progress {
            height: 8px;
            border-radius: 999px;
            background: #dcfce7;
            overflow: hidden;
        }

        .progress span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #16a34a, #22c55e);
        }
    </style>
</head>

<body class="min-h-screen">

<?php if (isset($_SESSION['alert'])): ?>
    <?php $alert = $_SESSION['alert']; unset($_SESSION['alert']); ?>
    <script>
        Swal.fire({
            icon: '<?= htmlspecialchars($alert['icon'] ?? 'success') ?>',
            title: '<?= htmlspecialchars($alert['title'] ?? '') ?>',
            text: '<?= htmlspecialchars($alert['text'] ?? '') ?>',
            confirmButtonColor: '#16a34a'
        });
    </script>
<?php endif; ?>

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="sidebar w-72 hidden lg:flex flex-col text-white p-6">
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-seedling text-xl"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-lg">SystemCOFF 360</h1>
                <p class="text-green-200 text-xs">Panel Administrador</p>
            </div>
        </div>

        <nav class="space-y-2 flex-1">
            <a href="admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/20 text-green-100">
                <i class="fas fa-chart-line w-5"></i>
                Dashboard
            </a>

            <a href="usuario_crear.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
              <i class="fas fa-user-plus w-5"></i>
              Crear usuario
            </a>

            <a href="usuarios.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
              <i class="fas fa-users w-5"></i>
              Usuarios
            </a>
            <a href="lotes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
             <i class="fas fa-map-marked-alt w-5"></i>
              Lotes
            </a>

            <a href="admin_tareas.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
              <i class="fas fa-clipboard-check w-5"></i>
              Tareas
            </a>

           <a href="inventario.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
              <i class="fas fa-warehouse w-5"></i>
              Inventario
           </a>

           
        </nav>

        <form action="../../controllers/AuthController.php" method="POST">
            <input type="hidden" name="logout" value="1">
            <button class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-100 transition">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar sesión
            </button>
        </form>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-4 md:p-8">

        <!-- HEADER -->
        <header class="glass rounded-3xl p-6 shadow-sm border border-green-100 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                        Administración general
                    </p>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-green-950">
                        Bienvenido, <?= htmlspecialchars($usuarioSesion['nombre'] ?? $usuarioSesion['nombres'] ?? 'Administrador') ?>
                    </h2>
                    <p class="text-gray-500 mt-2">
                        Control general de usuarios, cosechas, tareas, inventario y operaciones de la finca.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-700"><?= date('d/m/Y') ?></p>
                        <p class="text-xs text-gray-500">Panel activo</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-shield text-xl"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- ESTADÍSTICAS PRINCIPALES -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <p class="text-sm text-gray-500">Usuarios registrados</p>
                        <h3 class="text-4xl font-extrabold text-green-950"><?= $totalUsuarios ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="progress"><span style="width: <?= porcentaje($totalActivos, max($totalUsuarios,1)) ?>%"></span></div>
                <p class="text-xs text-gray-500 mt-3"><?= $totalActivos ?> activos · <?= $totalInactivos ?> inactivos</p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <p class="text-sm text-gray-500">Lotes registrados</p>
                        <h3 class="text-4xl font-extrabold text-green-950"><?= $totalLotes ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fas fa-map text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Control de zonas productivas de la finca</p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <p class="text-sm text-gray-500">Tareas registradas</p>
                        <h3 class="text-4xl font-extrabold text-green-950"><?= $totalTareas ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-lime-100 text-lime-700 flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Seguimiento de actividades del personal</p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <p class="text-sm text-gray-500">Cosechas registradas</p>
                        <h3 class="text-4xl font-extrabold text-green-950"><?= $totalCosechas ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                        <i class="fas fa-mug-hot text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Historial productivo del café</p>
            </div>

        </section>

        <!-- BLOQUES SECUNDARIOS -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

            <!-- Operación de finca -->
            <div class="xl:col-span-2 bg-white rounded-3xl p-6 border border-green-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-green-950">Resumen operativo</h3>
                        <p class="text-sm text-gray-500">Vista rápida del estado general del sistema</p>
                    </div>
                    <i class="fas fa-leaf text-green-500 text-2xl"></i>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="rounded-2xl bg-green-50 p-4">
                        <p class="text-xs text-gray-500">Insumos</p>
                        <p class="text-2xl font-extrabold text-green-900"><?= $totalInsumos ?></p>
                    </div>

                    <div class="rounded-2xl bg-green-50 p-4">
                        <p class="text-xs text-gray-500">Herramientas</p>
                        <p class="text-2xl font-extrabold text-green-900"><?= $totalHerramientas ?></p>
                    </div>

                    <div class="rounded-2xl bg-green-50 p-4">
                        <p class="text-xs text-gray-500">Activos</p>
                        <p class="text-2xl font-extrabold text-green-900"><?= $totalActivosFinca ?></p>
                    </div>

                    <div class="rounded-2xl bg-green-50 p-4">
                        <p class="text-xs text-gray-500">Proveedores</p>
                        <p class="text-2xl font-extrabold text-green-900"><?= $totalProveedores ?></p>
                    </div>

                    <div class="rounded-2xl bg-yellow-50 p-4">
                        <p class="text-xs text-gray-500">Compras</p>
                        <p class="text-2xl font-extrabold text-yellow-700"><?= $totalCompras ?></p>
                    </div>

                    <div class="rounded-2xl bg-yellow-50 p-4">
                        <p class="text-xs text-gray-500">Ventas</p>
                        <p class="text-2xl font-extrabold text-yellow-700"><?= $totalVentas ?></p>
                    </div>

                    <div class="rounded-2xl bg-blue-50 p-4">
                        <p class="text-xs text-gray-500">Mantenimientos</p>
                        <p class="text-2xl font-extrabold text-blue-700"><?= $totalMantenimientos ?></p>
                    </div>

                    <div class="rounded-2xl bg-blue-50 p-4">
                        <p class="text-xs text-gray-500">Trabajadores</p>
                        <p class="text-2xl font-extrabold text-blue-700"><?= $totalTrabajadores ?></p>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm">
                <h3 class="text-xl font-extrabold text-green-950 mb-2">Acciones rápidas</h3>
                <p class="text-sm text-gray-500 mb-5">Atajos administrativos</p>

                <div class="space-y-3">
                    <a href="usuario_crear.php" class="flex items-center gap-3 p-4 rounded-2xl bg-green-50 hover:bg-green-100 transition">
                        <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <p class="font-bold text-green-950">Crear usuario</p>
                            <p class="text-xs text-gray-500">Administrador o trabajador</p>
                        </div>
                    </a>

                    <a href="inventario.php" class="flex items-center gap-3 p-4 rounded-2xl bg-yellow-50 hover:bg-yellow-100 transition">
                        <div class="w-10 h-10 rounded-xl bg-yellow-500 text-white flex items-center justify-center">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <p class="font-bold text-green-950">Revisar inventario</p>
                            <p class="text-xs text-gray-500">Insumos y herramientas</p>
                        </div>
                    </a>

                    <a href="admin_tareas.php" class="flex items-center gap-3 p-4 rounded-2xl bg-blue-50 hover:bg-blue-100 transition">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <p class="font-bold text-green-950">Asignar tareas</p>
                            <p class="text-xs text-gray-500">Actividades de campo</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- TABLAS -->
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Usuarios recientes -->
            <div class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-xl font-extrabold text-green-950">Usuarios recientes</h3>
                        <p class="text-sm text-gray-500">Últimos usuarios creados</p>
                    </div>
                    <i class="fas fa-user-clock text-green-500 text-xl"></i>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-3">Nombre</th>
                            <th class="py-3">Rol</th>
                            <th class="py-3">Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($usuariosRecientes) > 0): ?>
                            <?php foreach ($usuariosRecientes as $u): ?>
                                <tr class="border-b last:border-0">
                                    <td class="py-3">
                                        <p class="font-bold text-green-950"><?= htmlspecialchars($u['nombre'] ?? '') ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($u['correo'] ?? '') ?></p>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                            <?= htmlspecialchars($u['rol'] ?? 'Sin rol') ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-3 py-1 rounded-full <?= ($u['estado'] ?? '') === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?> text-xs font-bold">
                                            <?= htmlspecialchars($u['estado'] ?? '') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-500">
                                    No hay usuarios recientes.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-xl font-extrabold text-green-950">Actividad del sistema</h3>
                        <p class="text-sm text-gray-500">Resumen de registros recientes</p>
                    </div>
                    <i class="fas fa-bell text-green-500 text-xl"></i>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-green-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <p class="font-bold text-green-950">Tareas recientes</p>
                                <p class="text-xs text-gray-500">Registros encontrados</p>
                            </div>
                        </div>
                        <span class="text-2xl font-extrabold text-green-900"><?= count($tareasRecientes) ?></span>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-2xl bg-yellow-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-yellow-500 text-white flex items-center justify-center">
                                <i class="fas fa-mug-hot"></i>
                            </div>
                            <div>
                                <p class="font-bold text-green-950">Cosechas recientes</p>
                                <p class="text-xs text-gray-500">Producción registrada</p>
                            </div>
                        </div>
                        <span class="text-2xl font-extrabold text-yellow-700"><?= count($cosechasRecientes) ?></span>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-2xl bg-blue-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <p class="font-bold text-green-950">Notificaciones</p>
                                <p class="text-xs text-gray-500">Alertas del sistema</p>
                            </div>
                        </div>
                        <span class="text-2xl font-extrabold text-blue-700"><?= count($notificaciones) ?></span>
                    </div>
                </div>
            </div>

        </section>

    </main>
</div>

</body>
</html>