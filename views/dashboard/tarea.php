<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] ?? '') !== 'trabajador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();

$usuarioSesion = $_SESSION['usuario'];
$idUsuario = $usuarioSesion['id_usuario'] ?? $usuarioSesion['id'] ?? 0;



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completar_tarea'])) {
    $idTarea = $_POST['id_tarea'] ?? 0;
    $descripcionEvidencia = trim($_POST['descripcion_evidencia'] ?? '');

    if ($idTarea <= 0) {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => 'Tarea inválida.'
        ];
        header('Location: tarea.php');
        exit;
    }

    $carpetaServidor = __DIR__ . '/../../public/uploads/evidencias/';

    if (!is_dir($carpetaServidor)) {
        mkdir($carpetaServidor, 0777, true);
    }

    if (empty($_FILES['evidencia']['name'])) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Evidencia requerida',
            'text' => 'Debes subir una imagen como evidencia.'
        ];
        header('Location: tarea.php');
        exit;
    }

    $extension = strtolower(pathinfo($_FILES['evidencia']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidas)) {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Archivo no permitido',
            'text' => 'Solo puedes subir imágenes JPG, PNG o WEBP.'
        ];
        header('Location: tarea.php');
        exit;
    }

    $nombreArchivo = 'evidencia_tarea_' . $idTarea . '_' . time() . '.' . $extension;
    $rutaServidor = $carpetaServidor . $nombreArchivo;
    $rutaBD = 'public/uploads/evidencias/' . $nombreArchivo;

    if (!move_uploaded_file($_FILES['evidencia']['tmp_name'], $rutaServidor)) {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => 'No se pudo subir la evidencia.'
        ];
        header('Location: tarea.php');
        exit;
    }

    try {
        $db->beginTransaction();

        $verificar = $db->prepare("
            SELECT COUNT(*) 
            FROM asignacion_tarea 
            WHERE id_tarea = ? AND id_usuario = ?
        ");
        $verificar->execute([$idTarea, $idUsuario]);

        if ($verificar->fetchColumn() == 0) {
            throw new Exception('Esta tarea no pertenece al trabajador.');
        }

        $guardarEvidencia = $db->prepare("
            INSERT INTO evidencia_tarea 
            (id_tarea, id_usuario, archivo, fecha_subida)
            VALUES (?, ?, ?, NOW())
        ");

        $guardarEvidencia->execute([
            $idTarea,
            $idUsuario,
            $rutaBD
        ]);

        $actualizarAsignacion = $db->prepare("
            UPDATE asignacion_tarea
            SET estado = 'completada'
            WHERE id_tarea = ? AND id_usuario = ?
        ");
        $actualizarAsignacion->execute([$idTarea, $idUsuario]);

        $actualizarTarea = $db->prepare("
            UPDATE tarea
            SET estado = 'completada'
            WHERE id_tarea = ?
        ");

        $actualizarTarea->execute([$idTarea]);

        $db->commit();

        $_SESSION['alert'] = [
            'icon' => 'success',
            'title' => 'Tarea completada',
            'text' => 'La evidencia fue enviada correctamente al administrador.'
        ];

    } catch (Exception $e) {
        $db->rollBack();

        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => $e->getMessage()
        ];
    }

    header('Location: tarea.php');
    exit;
}

$stmt = $db->prepare("
    SELECT 
        t.id_tarea,
        t.descripcion,
        t.prioridad,
        at.estado,
        t.fecha_creacion,
        t.fecha_limite,
        e.archivo AS evidencia,
        '' AS descripcion_evidencia,
        e.fecha_subida AS fecha_evidencia
    FROM asignacion_tarea at
    INNER JOIN tarea t ON at.id_tarea = t.id_tarea
    LEFT JOIN evidencia_tarea e ON e.id_tarea = t.id_tarea AND e.id_usuario = at.id_usuario
    WHERE at.id_usuario = ?
    ORDER BY t.id_tarea DESC
");

$stmt->execute([$idUsuario]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis tareas — SystemCOFF 360</title>
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
                <p class="text-green-200 text-xs">Panel Trabajador</p>
            </div>
        </div>

        <nav class="space-y-2 flex-1">
            <a href="trabajador.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-home w-5"></i>
                Inicio
            </a>

            <a href="tarea.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/20">
                <i class="fas fa-clipboard-list w-5"></i>
                Mis tareas
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-4 md:p-8">

        <header class="bg-white rounded-3xl p-6 border border-green-100 mb-8">
            <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                Trabajador
            </p>

            <h2 class="text-3xl md:text-4xl font-extrabold text-green-950">
                Mis tareas asignadas
            </h2>

            <p class="text-gray-500 mt-2">
                Aquí puedes ver tus tareas, subir evidencia y marcarlas como completadas.
            </p>
        </header>

        <section class="grid grid-cols-1 gap-5">

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

                    <article class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm">

                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div>
                                <h3 class="text-2xl font-extrabold text-green-950">
                                    Tarea #<?= htmlspecialchars($tarea['id_tarea']) ?>
                                </h3>

                                <p class="text-gray-600 mt-2">
                                    <?= htmlspecialchars($tarea['descripcion']) ?>
                                </p>

                                <div class="flex flex-wrap gap-3 mt-4 text-sm">
                                    <span class="px-3 py-1 rounded-full <?= $colorEstado ?> font-bold">
                                        <?= htmlspecialchars($tarea['estado']) ?>
                                    </span>

                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                                        Prioridad: <?= htmlspecialchars($tarea['prioridad']) ?>
                                    </span>

                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                                        Creada: <?= htmlspecialchars($tarea['fecha_creacion']) ?>
                                    </span>

                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                                        Límite: <?= htmlspecialchars($tarea['fecha_limite']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center">
                                <i class="fas fa-tasks text-xl"></i>
                            </div>
                        </div>

                        <?php if ($estado !== 'completada' && $estado !== 'finalizada'): ?>

                            <form method="POST" enctype="multipart/form-data" class="mt-6 bg-green-50 rounded-2xl p-5">
                                <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea['id_tarea']) ?>">

                                <label class="block text-sm font-bold text-green-950 mb-2">
                                    Descripción de la evidencia
                                </label>

                                <textarea
                                    name="descripcion_evidencia"
                                    rows="3"
                                    required
                                    class="w-full rounded-xl border border-green-200 p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    placeholder="Describe brevemente cómo realizaste la tarea"></textarea>

                                <label class="block text-sm font-bold text-green-950 mt-4 mb-2">
                                    Subir evidencia fotográfica
                                </label>

                                <input
                                    type="file"
                                    name="evidencia"
                                    accept="image/*"
                                    required
                                    class="w-full bg-white rounded-xl border border-green-200 p-3">

                                <button
                                    type="submit"
                                    name="completar_tarea"
                                    class="mt-5 px-5 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Marcar como completada
                                </button>
                            </form>

                        <?php else: ?>

                            <div class="mt-6 bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                                <p class="font-bold text-emerald-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Esta tarea ya fue completada y enviada al administrador.
                                </p>

                                <?php if (!empty($tarea['evidencia'])): ?>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <?= htmlspecialchars($tarea['descripcion_evidencia']) ?>
                                    </p>

                                    <a href="../../<?= htmlspecialchars($tarea['evidencia']) ?>" target="_blank" class="inline-block mt-3 text-green-700 font-bold hover:underline">
                                        Ver evidencia enviada
                                    </a>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="bg-white rounded-3xl p-10 text-center border border-green-100">
                    <i class="fas fa-clipboard-list text-5xl text-green-600 mb-4"></i>
                    <h3 class="text-2xl font-extrabold text-green-950">
                        No tienes tareas asignadas
                    </h3>
                    <p class="text-gray-500 mt-2">
                        Cuando el administrador te asigne una tarea aparecerá aquí.
                    </p>
                </div>

            <?php endif; ?>

        </section>

    </main>
</div>

</body>
</html>