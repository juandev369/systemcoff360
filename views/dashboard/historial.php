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

/* CONSULTAR HISTORIAL */
$stmt = $conexion->prepare("
    SELECT 
        a.id_asignacion,
        a.estado AS estado_asignacion,
        a.fecha_asignacion,

        t.id_tarea,
        t.descripcion,
        t.prioridad,
        t.estado AS estado_tarea,
        t.fecha_creacion,
        t.fecha_limite,

        e.archivo,
        e.tipo_archivo,
        e.fecha_subida

    FROM asignacion_tarea a

    INNER JOIN tarea t 
        ON a.id_tarea = t.id_tarea

    LEFT JOIN evidencia_tarea e 
        ON e.id_tarea = t.id_tarea 
        AND e.id_usuario = a.id_usuario

    WHERE a.id_usuario = ?

    ORDER BY a.fecha_asignacion DESC
");

$stmt->execute([$idUsuario]);

$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalHistorial = count($historial);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial - Trabajador</title>
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

        <a href="../usuarios/logout.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-yellow-900/30 hover:bg-yellow-900/50">

            <i class="fas fa-sign-out-alt"></i>
            Cerrar sesión

        </a>

    </aside>

    <!-- CONTENIDO -->
    <main class="flex-1 p-8">

        <!-- HEADER -->
        <section class="bg-white rounded-3xl shadow-sm border border-green-100 p-8 mb-8">

            <p class="text-sm font-bold text-green-600 uppercase tracking-widest">
                Zona del trabajador
            </p>

            <h1 class="text-4xl font-extrabold text-green-950 mt-2">
                Historial de tareas
            </h1>

            <p class="text-gray-600 mt-2">
                Consulta todas las tareas asignadas y finalizadas.
            </p>

        </section>

        <!-- CARD -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

            <div class="bg-white rounded-2xl border border-green-100 p-6 shadow-sm">

                <p class="text-gray-500">Total tareas</p>

                <h2 class="text-4xl font-extrabold text-green-900 mt-2">
                    <?= $totalHistorial ?>
                </h2>

                <i class="fas fa-history text-green-600 text-2xl mt-4"></i>

            </div>

        </section>

        <!-- TABLA -->
        <section class="bg-white rounded-3xl shadow-sm border border-green-100 p-6">

            <h2 class="text-2xl font-bold text-green-950 mb-6">
                Historial
            </h2>

            <?php if (empty($historial)): ?>

                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-2xl p-5">

                    <i class="fas fa-info-circle mr-2"></i>

                    No hay tareas registradas.

                </div>

            <?php else: ?>

                <div class="overflow-x-auto">

                    <table class="w-full border-collapse">

                        <thead>

                            <tr class="bg-green-100 text-green-900">

                                <th class="p-4 text-left rounded-l-xl">
                                    Descripción
                                </th>

                                <th class="p-4 text-left">
                                    Prioridad
                                </th>

                                <th class="p-4 text-left">
                                    Estado
                                </th>

                                <th class="p-4 text-left">
                                    Fecha asignación
                                </th>

                                <th class="p-4 text-left">
                                    Fecha límite
                                </th>

                                <th class="p-4 text-left rounded-r-xl">
                                    Evidencia
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($historial as $item): ?>

                                <tr class="border-b border-green-100 hover:bg-green-50">

                                    <td class="p-4 text-gray-700">
                                        <?= htmlspecialchars($item['descripcion']) ?>
                                    </td>

                                    <td class="p-4">

                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">

                                            <?= htmlspecialchars($item['prioridad']) ?>

                                        </span>

                                    </td>

                                    <td class="p-4">

                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">

                                            <?= htmlspecialchars($item['estado_asignacion']) ?>

                                        </span>

                                    </td>

                                    <td class="p-4 text-gray-600">

                                        <?= htmlspecialchars($item['fecha_asignacion']) ?>

                                    </td>

                                    <td class="p-4 text-gray-600">

                                        <?= !empty($item['fecha_limite']) 
                                            ? htmlspecialchars($item['fecha_limite']) 
                                            : 'Sin fecha' ?>

                                    </td>

                                    <td class="p-4">

                                        <?php if (!empty($item['archivo'])): ?>

                                            <a href="../../public/uploads/evidencias/<?= htmlspecialchars($item['archivo']) ?>"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-green-700">

                                                <i class="fas fa-eye"></i>

                                                Ver evidencia

                                            </a>

                                        <?php else: ?>

                                            <span class="text-gray-400">
                                                Sin evidencia
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>
</html>