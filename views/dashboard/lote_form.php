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

$id_lote = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editando = $id_lote > 0;
$lote = null;

if ($editando) {
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
}

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar lote' : 'Crear lote' ?> — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-green-50 min-h-screen">

<main class="max-w-3xl mx-auto p-5 md:p-10">

    <div class="bg-white rounded-3xl p-8 shadow-sm border border-green-100">
        <div class="mb-8">
            <a href="lotes.php" class="text-green-700 font-bold text-sm hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Volver a lotes
            </a>

            <h1 class="text-3xl font-extrabold text-green-950 mt-5">
                <?= $editando ? 'Editar lote' : 'Crear nuevo lote' ?>
            </h1>
            <p class="text-gray-500 mt-2">
                Registra o actualiza la información del lote de cultivo.
            </p>
        </div>

        <form action="../../controllers/LoteController.php" method="POST" class="space-y-5">
            <input type="hidden" name="accion" value="<?= $editando ? 'actualizar' : 'crear' ?>">

            <?php if ($editando): ?>
                <input type="hidden" name="id_lote" value="<?= $lote['id_lote'] ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm font-bold text-green-900 mb-2">Nombre del lote *</label>
                <input type="text" name="nombre" required maxlength="80"
                       value="<?= htmlspecialchars($lote['nombre'] ?? '') ?>"
                       placeholder="Ej. Lote El Mirador"
                       class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-green-900 mb-2">Tipo de plantación *</label>
                <input type="text" name="tipo_plantacion" required maxlength="80"
                       value="<?= htmlspecialchars($lote['tipo_plantacion'] ?? '') ?>"
                       placeholder="Ej. Café arábica variedad Castillo"
                       class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-green-900 mb-2">Ubicación</label>
                <textarea name="ubicacion" rows="3"
                          placeholder="Ej. Sector norte, cerca al beneficiadero"
                          class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500"><?= htmlspecialchars($lote['ubicacion'] ?? '') ?></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Área en hectáreas</label>
                    <input type="number" step="0.01" min="0" name="area_hectareas"
                           value="<?= htmlspecialchars($lote['area_hectareas'] ?? '') ?>"
                           placeholder="Ej. 2.50"
                           class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <?php if ($editando): ?>
                    <div>
                        <label class="block text-sm font-bold text-green-900 mb-2">Estado</label>
                        <select name="estado" class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="activo" <?= ($lote['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= ($lote['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="block text-sm font-bold text-green-900 mb-2">Fecha de registro *</label>
                        <input type="date" name="fecha_registro" required
                               value="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex flex-col md:flex-row gap-3 pt-4">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-2xl font-bold">
                    <i class="fas fa-save mr-2"></i>
                    <?= $editando ? 'Guardar cambios' : 'Crear lote' ?>
                </button>

                <a href="lotes.php" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-2xl font-bold">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

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