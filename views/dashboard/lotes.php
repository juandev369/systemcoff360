<?php
session_start();

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../models/Lote.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();
$loteModel = new Lote($db);

$lotes = $loteModel->obtenerTodos();
$totalLotes = $loteModel->total();
$totalActivos = $loteModel->totalActivos();
$totalArea = $loteModel->totalArea();

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lotes — SystemCOFF 360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-green-50 min-h-screen">

    <div class="min-h-screen flex">

        <aside class="hidden lg:flex w-72 bg-green-950 text-white p-6 flex-col">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-seedling text-xl"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-lg">SystemCOFF 360</h1>
                    <p class="text-green-200 text-xs">Administrador</p>
                </div>
            </div>

            <nav class="space-y-2 flex-1">
                <a href="admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10">
                    <i class="fas fa-chart-line w-5"></i> Dashboard
                </a>
                <a href="lotes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/20 text-green-100">
                    <i class="fas fa-map-marked-alt w-5"></i> Lotes
                </a>
                <a href="usuario_crear.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10">
                    <i class="fas fa-user-plus w-5"></i> Crear usuario
                </a>
            </nav>

            <form action="../../controllers/AuthController.php" method="POST">
                <input type="hidden" name="logout" value="1">
                <button class="w-full px-4 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                </button>
            </form>
        </aside>

        <main class="flex-1 p-5 md:p-8">

            <header class="bg-white rounded-3xl p-6 shadow-sm border border-green-100 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">Gestión de finca</p>
                        <h2 class="text-3xl font-extrabold text-green-950">Lotes de cultivo</h2>
                        <p class="text-gray-500 mt-2">Administra ubicación, plantación, área y actividades de
                            mantenimiento.</p>
                    </div>

                    <a href="lote_form.php"
                        class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-2xl font-bold">
                        <i class="fas fa-plus"></i> Crear lote
                    </a>
                </div>
            </header>

            <section class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                <div class="bg-white rounded-3xl p-6 border border-green-100">
                    <p class="text-gray-500 text-sm">Total lotes</p>
                    <h3 class="text-4xl font-extrabold text-green-950"><?= $totalLotes ?></h3>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-green-100">
                    <p class="text-gray-500 text-sm">Lotes activos</p>
                    <h3 class="text-4xl font-extrabold text-green-950"><?= $totalActivos ?></h3>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-green-100">
                    <p class="text-gray-500 text-sm">Área total</p>
                    <h3 class="text-4xl font-extrabold text-green-950"><?= number_format($totalArea, 2) ?> ha</h3>
                </div>
            </section>

            <section class="bg-white rounded-3xl border border-green-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-green-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-extrabold text-green-950">Listado de lotes</h3>
                        <p class="text-sm text-gray-500">Crea, edita, consulta detalles y cambia el estado de cada lote.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-green-50 text-green-900">
                            <tr>
                                <th class="px-5 py-4 text-left">Lote</th>
                                <th class="px-5 py-4 text-left">Ubicación</th>
                                <th class="px-5 py-4 text-left">Plantación</th>
                                <th class="px-5 py-4 text-left">Área</th>
                                <th class="px-5 py-4 text-left">Producción</th>
                                <th class="px-5 py-4 text-left">Estado</th>
                                <th class="px-5 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lotes) > 0): ?>
                                <?php foreach ($lotes as $lote): ?>
                                    <tr class="border-b last:border-0 hover:bg-green-50/50">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-green-950"><?= htmlspecialchars($lote['nombre']) ?></p>
                                            <p class="text-xs text-gray-500">Registrado:
                                                <?= htmlspecialchars($lote['fecha_registro']) ?></p>
                                        </td>
                                        <td class="px-5 py-4"><?= htmlspecialchars($lote['ubicacion'] ?? 'Sin ubicación') ?>
                                        </td>
                                        <td class="px-5 py-4"><?= htmlspecialchars($lote['tipo_plantacion']) ?></td>
                                        <td class="px-5 py-4"><?= number_format((float) $lote['area_hectareas'], 2) ?> ha</td>
                                        <td class="px-5 py-4"><?= number_format((float) $lote['total_cosecha'], 2) ?> kg</td>
                                        <td class="px-5 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold <?= $lote['estado'] === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= htmlspecialchars($lote['estado']) ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="lote_detalle.php?id=<?= $lote['id_lote'] ?>"
                                                    class="px-3 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100"
                                                    title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="lote_form.php?id=<?= $lote['id_lote'] ?>"
                                                    class="px-3 py-2 rounded-xl bg-yellow-50 text-yellow-700 hover:bg-yellow-100"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="../../controllers/LoteController.php" method="POST"
                                                    onsubmit="return confirm('¿Deseas cambiar el estado de este lote?')">
                                                    <input type="hidden" name="accion" value="cambiarEstado">
                                                    <input type="hidden" name="id_lote" value="<?= $lote['id_lote'] ?>">
                                                    <input type="hidden" name="estado"
                                                        value="<?= htmlspecialchars($lote['estado']) ?>">
                                                    <button
                                                        class="px-3 py-2 rounded-xl <?= $lote['estado'] === 'activo' ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' ?>"
                                                        title="Cambiar estado">
                                                        <i
                                                            class="fas <?= $lote['estado'] === 'activo' ? 'fa-ban' : 'fa-check' ?>"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                        No hay lotes registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <?php if ($alert): ?>
        <script>
            Swal.fire({
                icon: '<?= htmlspecialchars($alert['icon']) ?>',
                title: '<?= htmlspecialchars($alert['title']) ?>',
                text: '<?= htmlspecialchars($alert['text']) ?>',
                confirmButtonColor: '#16a34a'
            });
        </script>
    <?php endif; ?>

</body>

</html>