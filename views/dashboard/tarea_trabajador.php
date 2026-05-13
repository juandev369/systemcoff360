<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

/* CONEXIÓN */
$database = new Database();
$conexion = $database->conectar();

/* VALIDAR SESIÓN */
if (!isset($_SESSION['usuario']) && !isset($_SESSION['id_usuario'])) {
    header("Location: ../usuarios/login.php");
    exit;
}

/* OBTENER ID USUARIO */
if (isset($_SESSION['usuario']['id_usuario'])) {
    $idUsuario = $_SESSION['usuario']['id_usuario'];
} elseif (isset($_SESSION['usuario']['id'])) {
    $idUsuario = $_SESSION['usuario']['id'];
} else {
    $idUsuario = $_SESSION['id_usuario'];
}

/* COMPLETAR TAREA */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completar_tarea'])) {

    $idTarea = $_POST['id_tarea'];
    $comentario = trim($_POST['comentario'] ?? '');

    if (!empty($_FILES['evidencia']['name'])) {

        $carpeta = __DIR__ . '/../../public/uploads/evidencias/';

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['evidencia']['name'], PATHINFO_EXTENSION));
        $nombreArchivo = 'evidencia_' . $idUsuario . '_' . time() . '.' . $extension;

        move_uploaded_file(
            $_FILES['evidencia']['tmp_name'],
            $carpeta . $nombreArchivo
        );

        $guardarEvidencia = $conexion->prepare("
            INSERT INTO evidencia_tarea 
            (id_tarea, id_usuario, archivo, tipo_archivo, comentario, fecha_subida)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $guardarEvidencia->execute([
            $idTarea,
            $idUsuario,
            $nombreArchivo,
            $extension,
            $comentario
        ]);
    }

    $actualizar = $conexion->prepare("
        UPDATE asignacion_tarea
        SET estado = 'finalizada'
        WHERE id_tarea = ?
        AND id_usuario = ?
    ");

    $actualizar->execute([$idTarea, $idUsuario]);

    header("Location: tarea.php?ok=1");
    exit;
}

/* CONSULTAR TAREAS */
$stmt = $conexion->prepare("
    SELECT 
        a.id_asignacion,
        a.estado AS estado_asignacion,
        a.fecha_asignacion,

        t.id_tarea,
        t.descripcion,
        t.prioridad,
        t.estado,
        t.fecha_creacion,
        t.fecha_limite

    FROM asignacion_tarea a
    INNER JOIN tarea t ON a.id_tarea = t.id_tarea
    WHERE a.id_usuario = ?

    ORDER BY 
        CASE 
            WHEN a.estado = 'pendiente' THEN 1
            WHEN a.estado = 'en proceso' THEN 2
            WHEN a.estado = 'finalizada' THEN 3
            ELSE 4
        END,
        t.fecha_limite ASC
");

$stmt->execute([$idUsuario]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($tareas);
$pendientes = 0;
$proceso = 0;
$finalizadas = 0;

foreach ($tareas as $t) {
    if ($t['estado_asignacion'] == 'pendiente') $pendientes++;
    if ($t['estado_asignacion'] == 'en proceso') $proceso++;
    if ($t['estado_asignacion'] == 'finalizada') $finalizadas++;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis tareas</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-green-50 min-h-screen">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-green-950 text-white p-6 flex flex-col justify-between">

        <div>
            <div class="flex items-center gap-3 mb-10">
                <div class="w-11 h-11 bg-green-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-seedling text-white"></i>
                </div>

                <div>
                    <h2 class="font-bold text-lg">SystemCOFF 360</h2>
                    <p class="text-xs text-green-300">Panel Trabajador</p>
                </div>
            </div>

            <?php $paginaActual = basename($_SERVER['PHP_SELF']); ?>
            <nav class="space-y-3">


                <a href="trabajador.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'trabajador.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                    <i class="fas fa-home w-5"></i>
                    Inicio
                </a>

                <a href="tarea_trabajador.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'tarea_trabajador.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                    <i class="fas fa-clipboard-list w-5"></i>
                    Mis tareas
                </a>

                <a href="historial.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'historial.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                    <i class="fas fa-history w-5"></i>
                    Historial
                </a>

                <a href="perfil.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl <?= $paginaActual === 'perfil.php' ? 'bg-green-500/20 text-green-100' : 'hover:bg-white/10 transition' ?>">
                    <i class="fas fa-user w-5"></i>
                    Mi perfil
                </a>

            </nav>
        </div>

        <a href="../../usuarios/logout.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-yellow-900/30 hover:bg-yellow-900/50">

            <i class="fas fa-sign-out-alt"></i>
            Cerrar sesión

        </a>

    </aside>

    <!-- CONTENIDO -->
    <main class="flex-1 p-8">

        <section class="bg-white rounded-3xl shadow-sm border border-green-100 p-8 mb-8">

            <p class="text-sm font-bold text-green-600 uppercase tracking-widest">
                Zona del trabajador
            </p>

            <h1 class="text-4xl font-extrabold text-green-950 mt-2">
                Mis tareas
            </h1>

            <p class="text-gray-600 mt-2">
                Revisa tus tareas asignadas, sube evidencias y agrega comentarios.
            </p>

        </section>

        <?php if (isset($_GET['ok'])): ?>
            <div class="bg-green-100 border border-green-300 text-green-800 rounded-2xl p-4 mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                Tarea completada correctamente.
            </div>
        <?php endif; ?>

        <!-- RESUMEN -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-2xl border border-green-100 p-6 shadow-sm">
                <p class="text-gray-500">Total</p>
                <h2 class="text-4xl font-extrabold text-green-900 mt-2"><?= $total ?></h2>
            </div>

            <div class="bg-white rounded-2xl border border-yellow-100 p-6 shadow-sm">
                <p class="text-gray-500">Pendientes</p>
                <h2 class="text-4xl font-extrabold text-yellow-600 mt-2"><?= $pendientes ?></h2>
            </div>

            <div class="bg-white rounded-2xl border border-blue-100 p-6 shadow-sm">
                <p class="text-gray-500">En proceso</p>
                <h2 class="text-4xl font-extrabold text-blue-600 mt-2"><?= $proceso ?></h2>
            </div>

            <div class="bg-white rounded-2xl border border-green-100 p-6 shadow-sm">
                <p class="text-gray-500">Finalizadas</p>
                <h2 class="text-4xl font-extrabold text-green-600 mt-2"><?= $finalizadas ?></h2>
            </div>

        </section>

        <!-- LISTADO -->
        <section class="bg-white rounded-3xl shadow-sm border border-green-100 p-6">

            <h2 class="text-2xl font-bold text-green-950 mb-6">
                Listado de tareas
            </h2>

            <?php if (empty($tareas)): ?>

                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-2xl p-5">
                    <i class="fas fa-info-circle mr-2"></i>
                    No tienes tareas asignadas.
                </div>

            <?php else: ?>

                <div class="space-y-6">

                    <?php foreach ($tareas as $tarea): ?>

                        <div class="border border-green-100 rounded-3xl p-6 bg-white shadow-sm">

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                <!-- INFORMACIÓN TAREA -->
                                <div>

                                    <h3 class="text-2xl font-bold text-green-950">
                                        <?= htmlspecialchars($tarea['descripcion']) ?>
                                    </h3>

                                    <div class="flex gap-3 mt-4 flex-wrap">

                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                            Prioridad: <?= htmlspecialchars($tarea['prioridad']) ?>
                                        </span>

                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            <?=
                                                $tarea['estado_asignacion'] == 'pendiente'
                                                ? 'bg-yellow-100 text-yellow-700'
                                                : (
                                                    $tarea['estado_asignacion'] == 'en proceso'
                                                    ? 'bg-blue-100 text-blue-700'
                                                    : 'bg-green-100 text-green-700'
                                                )
                                            ?>
                                        ">
                                            <?= htmlspecialchars($tarea['estado_asignacion']) ?>
                                        </span>

                                    </div>

                                    <div class="mt-5 text-sm text-gray-600 space-y-2">

                                        <p>
                                            <i class="fas fa-calendar-plus text-green-600 mr-2"></i>
                                            <strong>Fecha asignación:</strong>
                                            <?= htmlspecialchars($tarea['fecha_asignacion']) ?>
                                        </p>

                                        <p>
                                            <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                                            <strong>Fecha límite:</strong>
                                            <?= !empty($tarea['fecha_limite']) 
                                                ? htmlspecialchars($tarea['fecha_limite']) 
                                                : 'Sin fecha límite' ?>
                                        </p>

                                    </div>

                                </div>

                                <!-- EVIDENCIA -->
                                <div>

                                    <?php if ($tarea['estado_asignacion'] != 'finalizada'): ?>

                                        <form method="POST"
                                            enctype="multipart/form-data"
                                            class="bg-green-50 border border-green-200 rounded-3xl p-5">

                                            <input type="hidden"
                                                name="id_tarea"
                                                value="<?= $tarea['id_tarea'] ?>">

                                            <div class="flex items-center gap-3 mb-4">

                                                <div class="w-12 h-12 rounded-2xl bg-green-600 text-white flex items-center justify-center">
                                                    <i class="fas fa-cloud-upload-alt text-xl"></i>
                                                </div>

                                                <div>
                                                    <h4 class="font-bold text-green-950">
                                                        Subir evidencia
                                                    </h4>

                                                    <p class="text-xs text-gray-500">
                                                        Adjunta foto, PDF o documento.
                                                    </p>
                                                </div>

                                            </div>

                                            <label class="block border-2 border-dashed border-green-300 rounded-2xl bg-white p-6 text-center cursor-pointer hover:bg-green-100 transition">

                                                <i class="fas fa-file-upload text-4xl text-green-600 mb-3"></i>

                                                <p class="text-sm font-bold text-green-900">
                                                    Selecciona tu archivo
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    JPG, PNG, PDF, DOC o DOCX
                                                </p>

                                                <input type="file"
                                                    name="evidencia"
                                                    required
                                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                    class="hidden">

                                            </label>

                                            <div class="mt-4">

                                                <label class="block text-sm font-bold text-green-900 mb-2">
                                                    Comentario
                                                </label>

                                                <textarea
                                                    name="comentario"
                                                    rows="4"
                                                    required
                                                    placeholder="Escribe una observación sobre la tarea realizada..."
                                                    class="w-full border border-green-200 rounded-2xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>

                                            </div>

                                            <button type="submit"
                                                name="completar_tarea"
                                                class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-2xl shadow-sm">

                                                <i class="fas fa-check-circle mr-2"></i>
                                                Enviar evidencia y completar

                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <div class="bg-green-100 border border-green-300 text-green-800 rounded-3xl p-6 text-center">

                                            <i class="fas fa-check-circle text-4xl mb-3"></i>

                                            <h4 class="font-bold text-lg">
                                                Tarea finalizada
                                            </h4>

                                            <p class="text-sm mt-1">
                                                Esta tarea ya fue marcada como completada.
                                            </p>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>
</html>