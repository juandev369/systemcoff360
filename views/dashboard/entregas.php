<?php
session_start();

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../models/Inventario.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();

$inventarioModel = new Inventario($db);

$herramientas = $inventarioModel->obtenerHerramientas();
$epps = $inventarioModel->obtenerEpp();
$trabajadores = $inventarioModel->obtenerTrabajadores();
$entregasHerramientas = $inventarioModel->obtenerEntregasHerramientas();
$entregasEpp = $inventarioModel->obtenerEntregasEpp();

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

$paginaActual = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entregas — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: #f0fdf4;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #052e16 0%, #064e3b 60%, #022c22 100%);
        }

        .field {
            width: 100%;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 11px 13px;
            outline: none;
            background: white;
        }

        .field:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .15);
        }
    </style>
</head>

<body class="min-h-screen">

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

            <a href="admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'admin.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-chart-line w-5"></i>
                Dashboard
            </a>

            <a href="usuario_crear.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'usuario_crear.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-user-plus w-5"></i>
                Crear usuario
            </a>

            <a href="usuarios.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'usuarios.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-users w-5"></i>
                Usuarios
            </a>

            <a href="lotes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'lotes.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-map-marked-alt w-5"></i>
                Lotes
            </a>

            <a href="admin_tareas.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'admin_tareas.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-clipboard-check w-5"></i>
                Tareas
            </a>

            <a href="inventario.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'inventario.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-warehouse w-5"></i>
                Inventario
            </a>

            <a href="entregas.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'entregas.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-truck-loading w-5"></i>
                Entregas
            </a>

            <a href="asistente.php" class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'asistente.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                <i class="fas fa-robot w-5"></i>
                Asistente AI
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
    <main class="flex-1 p-5 md:p-8">

        <!-- HEADER -->
        <header class="bg-white rounded-3xl p-6 shadow-sm border border-green-100 mb-8">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                        Gestión de finca
                    </p>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-green-950">
                        Entregas
                    </h1>

                    <p class="text-gray-500 mt-2">
                        Administra la entrega y devolución de herramientas y elementos de protección personal.
                    </p>
                </div>

                <div class="w-16 h-16 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg">
                    <i class="fas fa-truck-loading text-2xl"></i>
                </div>
            </div>

        </header>

        <!-- FORMULARIOS -->
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

            <!-- ENTREGAR HERRAMIENTA -->
            <div class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm">

                <h2 class="text-xl font-extrabold text-green-950 mb-4">
                    Entregar herramienta
                </h2>

                <form action="../../controllers/InventarioController.php" method="POST" class="space-y-4">
                    <input type="hidden" name="accion" value="entregarHerramienta">

                    <select class="field" name="id_herramienta" required>
                        <option value="">Herramienta disponible</option>

                        <?php foreach ($herramientas as $h): ?>
                            <?php if ($h['estado'] === 'disponible'): ?>
                                <option value="<?= $h['id_herramienta'] ?>">
                                    <?= htmlspecialchars($h['nombre']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <select class="field" name="id_usuario" required>
                        <option value="">Trabajador</option>

                        <?php foreach ($trabajadores as $t): ?>
                            <option value="<?= $t['id_usuario'] ?>">
                                <?= htmlspecialchars($t['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input class="field" type="date" name="fecha_entrega" value="<?= date('Y-m-d') ?>" required>

                    <select class="field" name="estado_herramienta">
                        <option value="bueno">Bueno</option>
                        <option value="regular">Regular</option>
                        <option value="malo">Malo</option>
                    </select>

                    <textarea class="field" name="observaciones" placeholder="Observaciones"></textarea>

                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-2xl font-bold">
                        <i class="fas fa-hand-holding mr-2"></i>
                        Entregar herramienta
                    </button>
                </form>

            </div>

            <!-- ENTREGAR EPP -->
            <div class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm">

                <h2 class="text-xl font-extrabold text-green-950 mb-4">
                    Entregar EPP
                </h2>

                <form action="../../controllers/InventarioController.php" method="POST" class="space-y-4">
                    <input type="hidden" name="accion" value="entregarEpp">

                    <select class="field" name="id_epp" required>
                        <option value="">Seleccione EPP</option>

                        <?php foreach ($epps as $e): ?>
                            <?php if ((int)$e['stock_disponible'] > 0): ?>
                                <option value="<?= $e['id_epp'] ?>">
                                    <?= htmlspecialchars($e['nombre']) ?> — Disponible: <?= $e['stock_disponible'] ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <select class="field" name="id_usuario" required>
                        <option value="">Trabajador</option>

                        <?php foreach ($trabajadores as $t): ?>
                            <option value="<?= $t['id_usuario'] ?>">
                                <?= htmlspecialchars($t['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input class="field" type="date" name="fecha_entrega" value="<?= date('Y-m-d') ?>" required>

                    <select class="field" name="estado_elemento">
                        <option value="bueno">Bueno</option>
                        <option value="regular">Regular</option>
                        <option value="malo">Malo</option>
                    </select>

                    <textarea class="field" name="observaciones" placeholder="Observaciones"></textarea>

                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-2xl font-bold">
                        <i class="fas fa-hand-holding-medical mr-2"></i>
                        Entregar EPP
                    </button>
                </form>

            </div>

        </section>

        <!-- LISTADO ENTREGAS -->
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- ENTREGAS HERRAMIENTAS -->
            <div class="bg-white rounded-3xl border border-green-100 overflow-hidden shadow-sm">

                <div class="p-6 border-b border-green-100">
                    <h2 class="text-xl font-extrabold text-green-950">
                        Entregas de herramientas
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-green-50 text-green-900">
                            <tr>
                                <th class="px-5 py-4 text-left">Herramienta</th>
                                <th class="px-5 py-4 text-left">Trabajador</th>
                                <th class="px-5 py-4 text-left">Fecha</th>
                                <th class="px-5 py-4 text-right">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($entregasHerramientas) > 0): ?>
                                <?php foreach ($entregasHerramientas as $eh): ?>
                                    <tr class="border-b last:border-0 hover:bg-green-50/50">

                                        <td class="px-5 py-4 font-bold">
                                            <?= htmlspecialchars($eh['herramienta']) ?>
                                        </td>

                                        <td class="px-5 py-4">
                                            <?= htmlspecialchars($eh['trabajador']) ?>
                                        </td>

                                        <td class="px-5 py-4">
                                            <?= htmlspecialchars($eh['fecha_entrega']) ?>

                                            <?php if (!empty($eh['fecha_devolucion'])): ?>
                                                <p class="text-xs text-green-600">
                                                    Devuelta: <?= htmlspecialchars($eh['fecha_devolucion']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            <?php if (empty($eh['fecha_devolucion'])): ?>

                                                <form action="../../controllers/InventarioController.php" method="POST" onsubmit="return confirm('¿Confirmas la devolución de esta herramienta?')">
                                                    <input type="hidden" name="accion" value="devolverHerramienta">
                                                    <input type="hidden" name="id_entrega" value="<?= $eh['id_entrega'] ?>">

                                                    <button class="px-3 py-2 rounded-xl bg-green-50 text-green-700 hover:bg-green-100">
                                                        Devolver
                                                    </button>
                                                </form>

                                            <?php else: ?>

                                                <span class="text-xs text-gray-400">
                                                    Finalizada
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                                        No hay entregas de herramientas.
                                    </td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- ENTREGAS EPP -->
            <div class="bg-white rounded-3xl border border-green-100 overflow-hidden shadow-sm">

                <div class="p-6 border-b border-green-100">
                    <h2 class="text-xl font-extrabold text-green-950">
                        Entregas de EPP
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-green-50 text-green-900">
                            <tr>
                                <th class="px-5 py-4 text-left">EPP</th>
                                <th class="px-5 py-4 text-left">Trabajador</th>
                                <th class="px-5 py-4 text-left">Fecha</th>
                                <th class="px-5 py-4 text-right">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($entregasEpp) > 0): ?>
                                <?php foreach ($entregasEpp as $ee): ?>
                                    <tr class="border-b last:border-0 hover:bg-green-50/50">

                                        <td class="px-5 py-4 font-bold">
                                            <?= htmlspecialchars($ee['epp']) ?>
                                        </td>

                                        <td class="px-5 py-4">
                                            <?= htmlspecialchars($ee['trabajador']) ?>
                                        </td>

                                        <td class="px-5 py-4">
                                            <?= htmlspecialchars($ee['fecha_entrega']) ?>

                                            <?php if (!empty($ee['fecha_devolucion'])): ?>
                                                <p class="text-xs text-green-600">
                                                    Devuelto: <?= htmlspecialchars($ee['fecha_devolucion']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            <?php if (empty($ee['fecha_devolucion'])): ?>

                                                <form action="../../controllers/InventarioController.php" method="POST" onsubmit="return confirm('¿Confirmas la devolución de este EPP?')">
                                                    <input type="hidden" name="accion" value="devolverEpp">
                                                    <input type="hidden" name="id_entrega" value="<?= $ee['id_entrega'] ?>">

                                                    <button class="px-3 py-2 rounded-xl bg-green-50 text-green-700 hover:bg-green-100">
                                                        Devolver
                                                    </button>
                                                </form>

                                            <?php else: ?>

                                                <span class="text-xs text-gray-400">
                                                    Finalizada
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                                        No hay entregas de EPP.
                                    </td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </section>

    </main>

</div>

<?php if ($alert): ?>
<script>
Swal.fire({
    icon: '<?= htmlspecialchars($alert['icon'] ?? 'success') ?>',
    title: '<?= htmlspecialchars($alert['title'] ?? '') ?>',
    text: '<?= htmlspecialchars($alert['text'] ?? '') ?>',
    confirmButtonColor: '#16a34a'
});
</script>
<?php endif; ?>

</body>
</html>