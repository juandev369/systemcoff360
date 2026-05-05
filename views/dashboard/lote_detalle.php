<?php
session_start();

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../models/Lote.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$id_lote = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$database = new Database();
$db = $database->conectar();
$loteModel = new Lote($db);

$lote = $loteModel->obtenerPorId($id_lote);

if (!$lote) {
    $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Lote no encontrado',
        'text' => 'No se encontró el lote solicitado.'
    ];
    header('Location: lotes.php');
    exit;
}

$actividades = $loteModel->obtenerActividades($id_lote);
$trabajadores = $loteModel->obtenerTrabajadores();

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de lote — SystemCOFF 360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-green-50 min-h-screen">

<main class="max-w-6xl mx-auto p-5 md:p-10">

    <div class="mb-6">
        <a href="lotes.php" class="text-green-700 font-bold text-sm hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Volver a lotes
        </a>
    </div>

    <section class="bg-white rounded-3xl p-8 shadow-sm border border-green-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
            <div>
                <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">Detalle del lote</p>
                <h1 class="text-3xl font-extrabold text-green-950"><?= htmlspecialchars($lote['nombre']) ?></h1>
                <p class="text-gray-500 mt-2"><?= htmlspecialchars($lote['ubicacion'] ?? 'Sin ubicación registrada') ?></p>
            </div>

            <span class="px-4 py-2 rounded-full text-sm font-bold <?= $lote['estado'] === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= htmlspecialchars($lote['estado']) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-8">
            <div class="bg-green-50 rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Plantación</p>
                <p class="font-extrabold text-green-950 mt-1"><?= htmlspecialchars($lote['tipo_plantacion']) ?></p>
            </div>

            <div class="bg-green-50 rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Área</p>
                <p class="font-extrabold text-green-950 mt-1"><?= number_format((float)$lote['area_hectareas'], 2) ?> ha</p>
            </div>

            <div class="bg-yellow-50 rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Producción</p>
                <p class="font-extrabold text-yellow-700 mt-1"><?= number_format((float)$lote['total_cosecha'], 2) ?> kg</p>
            </div>

            <div class="bg-blue-50 rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Actividades</p>
                <p class="font-extrabold text-blue-700 mt-1"><?= (int)$lote['total_actividades'] ?></p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        <div class="xl:col-span-1 bg-white rounded-3xl p-6 border border-green-100 shadow-sm">
            <h2 class="text-xl font-extrabold text-green-950 mb-2">Registrar actividad</h2>
            <p class="text-sm text-gray-500 mb-6">Abono, riego, limpieza, poda u otra actividad del lote.</p>

            <form action="../../controllers/LoteController.php" method="POST" class="space-y-4">
                <input type="hidden" name="accion" value="registrarActividad">
                <input type="hidden" name="id_lote" value="<?= $lote['id_lote'] ?>">

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Tipo *</label>
                    <select name="tipo" required class="w-full px-4 py-3 rounded-2xl border border-green-200">
                        <option value="">Seleccione</option>
                        <option value="abono">Abono</option>
                        <option value="riego">Riego</option>
                        <option value="limpieza">Limpieza</option>
                        <option value="poda">Poda</option>
                        <option value="fumigacion">Fumigación</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Fecha *</label>
                    <input type="date" name="fecha" required value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 rounded-2xl border border-green-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Responsable *</label>
                    <select name="id_responsable" required class="w-full px-4 py-3 rounded-2xl border border-green-200">
                        <option value="">Seleccione responsable</option>
                        <?php foreach ($trabajadores as $t): ?>
                            <option value="<?= $t['id_usuario'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Costo</label>
                    <input type="number" step="0.01" min="0" name="costo" placeholder="0.00" class="w-full px-4 py-3 rounded-2xl border border-green-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Próxima fecha</label>
                    <input type="date" name="proxima_fecha" class="w-full px-4 py-3 rounded-2xl border border-green-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Observaciones</label>
                    <textarea name="descripcion" rows="4" class="w-full px-4 py-3 rounded-2xl border border-green-200" placeholder="Describe la actividad realizada..."></textarea>
                </div>

                <button class="w-full bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-2xl font-bold">
                    <i class="fas fa-save mr-2"></i> Guardar actividad
                </button>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white rounded-3xl border border-green-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-green-100">
                <h2 class="text-xl font-extrabold text-green-950">Historial de mantenimiento</h2>
                <p class="text-sm text-gray-500">Actividades realizadas en este lote.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-green-50 text-green-900">
                        <tr>
                            <th class="px-5 py-4 text-left">Fecha</th>
                            <th class="px-5 py-4 text-left">Tipo</th>
                            <th class="px-5 py-4 text-left">Responsable</th>
                            <th class="px-5 py-4 text-left">Costo</th>
                            <th class="px-5 py-4 text-left">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($actividades) > 0): ?>
                        <?php foreach ($actividades as $a): ?>
                            <tr class="border-b last:border-0 hover:bg-green-50/50">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-green-950"><?= htmlspecialchars($a['fecha']) ?></p>
                                    <?php if (!empty($a['proxima_fecha'])): ?>
                                        <p class="text-xs text-gray-500">Próx: <?= htmlspecialchars($a['proxima_fecha']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                        <?= htmlspecialchars($a['tipo']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4"><?= htmlspecialchars($a['responsable'] ?? 'Sin responsable') ?></td>
                                <td class="px-5 py-4">$<?= number_format((float)$a['costo'], 0, ',', '.') ?></td>
                                <td class="px-5 py-4"><?= htmlspecialchars($a['descripcion'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                Este lote todavía no tiene actividades registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

</main>

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