<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();

function alerta($icon, $title, $text) {
    $_SESSION['alert'] = [
        'icon' => $icon,
        'title' => $title,
        'text' => $text
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_tarea'])) {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $fecha_limite = $_POST['fecha_limite'] ?? '';
    $id_usuario = $_POST['id_usuario'] ?? '';

    if ($descripcion === '' || $fecha_limite === '' || $id_usuario === '') {
        alerta('warning', 'Campos incompletos', 'Debes llenar todos los campos.');
        header('Location: admin_tareas.php');
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO tarea 
            (descripcion, prioridad, estado, fecha_creacion, fecha_limite)
            VALUES (?, ?, 'pendiente', NOW(), ?)
        ");
        $stmt->execute([$descripcion, $prioridad, $fecha_limite]);

        $id_tarea = $db->lastInsertId();

        $stmtAsignar = $db->prepare("
            INSERT INTO asignacion_tarea
            (id_tarea, id_usuario, fecha_asignacion)
            VALUES (?, ?, NOW())
        ");
        $stmtAsignar->execute([$id_tarea, $id_usuario]);

        $db->commit();

        alerta('success', 'Tarea asignada', 'La tarea fue creada y asignada al trabajador.');

    } catch (Exception $e) {
        $db->rollBack();
        alerta('error', 'Error', $e->getMessage());
    }

    header('Location: admin_tareas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    $id_tarea = $_POST['id_tarea'] ?? 0;
    $estado = $_POST['estado'] ?? 'pendiente';

    $stmt = $db->prepare("
        UPDATE tarea
        SET estado = ?
        WHERE id_tarea = ?
    ");
    $stmt->execute([$estado, $id_tarea]);

    alerta('success', 'Estado actualizado', 'El estado de la tarea fue actualizado.');
    header('Location: admin_tareas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_tarea'])) {
    $id_tarea = $_POST['id_tarea'] ?? 0;

    try {
        $db->beginTransaction();

        $db->prepare("DELETE FROM evidencia_tarea WHERE id_tarea = ?")->execute([$id_tarea]);
        $db->prepare("DELETE FROM asignacion_tarea WHERE id_tarea = ?")->execute([$id_tarea]);
        $db->prepare("DELETE FROM tarea WHERE id_tarea = ?")->execute([$id_tarea]);

        $db->commit();

        alerta('success', 'Tarea eliminada', 'La tarea fue eliminada correctamente.');

    } catch (Exception $e) {
        $db->rollBack();
        alerta('error', 'Error', $e->getMessage());
    }

    header('Location: admin_tareas.php');
    exit;
}

$trabajadores = $db->query("
    SELECT id_usuario, nombre, correo AS email
    FROM usuario
    WHERE id_rol = 2
    ORDER BY nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

$tareas = $db->query("
    SELECT 
        t.id_tarea,
        t.descripcion,
        t.prioridad,
        t.estado,
        t.fecha_creacion,
        t.fecha_limite,
        u.nombre,
        u.correo AS email,
        e.archivo,
        '' AS descripcion_evidencia,
        e.fecha_subida AS fecha_registro
    FROM tarea t
    LEFT JOIN asignacion_tarea at ON at.id_tarea = t.id_tarea
    LEFT JOIN usuario u ON u.id_usuario = at.id_usuario
    LEFT JOIN evidencia_tarea e ON e.id_tarea = t.id_tarea
    ORDER BY t.id_tarea DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de tareas — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="min-h-screen bg-green-50">

<?php if (isset($_SESSION['alert'])): ?>
    <?php $alert = $_SESSION['alert']; unset($_SESSION['alert']); ?>
    <script>
        Swal.fire({
            icon: '<?= htmlspecialchars($alert['icon']) ?>',
            title: '<?= htmlspecialchars($alert['title']) ?>',
            text: '<?= htmlspecialchars($alert['text']) ?>',
            confirmButtonColor: '#16a34a'
        });
    </script>
<?php endif; ?>

<div class="flex min-h-screen">

    <aside class="w-72 hidden lg:flex flex-col text-white p-6 bg-gradient-to-b from-green-950 to-emerald-950">
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center">
                <i class="fas fa-seedling text-xl"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-lg">SystemCOFF 360</h1>
                <p class="text-green-200 text-xs">Panel Administrador</p>
            </div>
        </div>

        <?php $paginaActual = basename($_SERVER['PHP_SELF']); ?>
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
    </aside>

    <main class="flex-1 p-4 md:p-8">

        <header class="bg-white rounded-3xl p-6 border border-green-100 mb-8">
            <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                Administrador
            </p>

            <h2 class="text-3xl md:text-4xl font-extrabold text-green-950">
                Gestión de tareas
            </h2>

            <p class="text-gray-500 mt-2">
                Crea tareas, asígnalas a trabajadores, revisa evidencias y controla el estado.
            </p>
        </header>

        <section class="bg-white rounded-3xl p-6 border border-green-100 mb-8">
            <h3 class="text-2xl font-extrabold text-green-950 mb-5">
                Asignar nueva tarea
            </h3>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-green-950 mb-2">
                        Descripción de la tarea
                    </label>
                    <textarea 
                        name="descripcion" 
                        rows="3" 
                        required
                        class="w-full rounded-xl border border-green-200 p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Ejemplo: Realizar limpieza en el lote La María"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-950 mb-2">
                        Trabajador
                    </label>
                    <select 
                        name="id_usuario" 
                        required
                        class="w-full rounded-xl border border-green-200 p-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione un trabajador</option>

                        <?php foreach ($trabajadores as $trabajador): ?>
                            <option value="<?= htmlspecialchars($trabajador['id_usuario']) ?>">
                                <?= htmlspecialchars($trabajador['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-950 mb-2">
                        Prioridad
                    </label>
                    <select 
                        name="prioridad" 
                        required
                        class="w-full rounded-xl border border-green-200 p-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-950 mb-2">
                        Fecha límite
                    </label>
                    <input 
                        type="date" 
                        name="fecha_limite" 
                        required
                        class="w-full rounded-xl border border-green-200 p-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex items-end">
                    <button 
                        type="submit" 
                        name="crear_tarea"
                        class="w-full px-5 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Crear y asignar tarea
                    </button>
                </div>

            </form>
        </section>

        <section class="bg-white rounded-3xl p-6 border border-green-100">
            <h3 class="text-2xl font-extrabold text-green-950 mb-5">
                Tareas asignadas
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-green-100 text-green-950">
                            <th class="p-3 text-left">ID</th>
                            <th class="p-3 text-left">Tarea</th>
                            <th class="p-3 text-left">Trabajador</th>
                            <th class="p-3 text-left">Prioridad</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-left">Fecha límite</th>
                            <th class="p-3 text-left">Evidencia</th>
                            <th class="p-3 text-left">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($tareas) > 0): ?>
                            <?php foreach ($tareas as $tarea): ?>

                                <?php
                                $estado = strtolower($tarea['estado'] ?? 'pendiente');

                                $colorEstado = match ($estado) {
                                    'completada', 'finalizada' => 'bg-emerald-100 text-emerald-700',
                                    'en proceso' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-yellow-100 text-yellow-700'
                                };
                                ?>

                                <tr class="border-b border-green-100">
                                    <td class="p-3 font-bold">
                                        <?= htmlspecialchars($tarea['id_tarea']) ?>
                                    </td>

                                    <td class="p-3">
                                        <?= htmlspecialchars($tarea['descripcion']) ?>
                                    </td>

                                    <td class="p-3">
                                        <?= htmlspecialchars($tarea['nombre'] ?? 'Sin asignar') ?>
                                    </td>

                                    <td class="p-3">
                                        <?= htmlspecialchars($tarea['prioridad']) ?>
                                    </td>

                                    <td class="p-3">
                                        <span class="px-3 py-1 rounded-full font-bold <?= $colorEstado ?>">
                                            <?= htmlspecialchars($tarea['estado']) ?>
                                        </span>
                                    </td>

                                    <td class="p-3">
                                        <?= htmlspecialchars($tarea['fecha_limite']) ?>
                                    </td>

                                    <td class="p-3">
                                        <?php if (!empty($tarea['archivo'])): ?>
                                            <a 
                                                href="../../<?= htmlspecialchars($tarea['archivo']) ?>" 
                                                target="_blank"
                                                class="text-green-700 font-bold hover:underline">
                                                Ver evidencia
                                            </a>

                                            <p class="text-gray-500 mt-1">
                                                <?= htmlspecialchars($tarea['descripcion_evidencia']) ?>
                                            </p>
                                        <?php else: ?>
                                            <span class="text-gray-400">Sin evidencia</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="p-3">
                                        <div class="flex flex-col gap-2">

                                            <form method="POST" class="flex gap-2">
                                                <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea['id_tarea']) ?>">

                                                <select 
                                                    name="estado"
                                                    class="rounded-lg border border-green-200 p-2">
                                                    <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                    <option value="en proceso" <?= $estado === 'en proceso' ? 'selected' : '' ?>>En proceso</option>
                                                    <option value="completada" <?= $estado === 'completada' ? 'selected' : '' ?>>Completada</option>
                                                    <option value="finalizada" <?= $estado === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                                                </select>

                                                <button 
                                                    type="submit" 
                                                    name="cambiar_estado"
                                                    class="px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                                                    Guardar
                                                </button>
                                            </form>

                                            <form method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta tarea?');">
                                                <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea['id_tarea']) ?>">

                                                <button 
                                                    type="submit" 
                                                    name="eliminar_tarea"
                                                    class="px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white">
                                                    Eliminar
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-500">
                                    No hay tareas registradas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</div>

</body>
</html>