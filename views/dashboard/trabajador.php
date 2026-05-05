<?php
session_start();

require_once __DIR__ . '/../../Config/database.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] ?? '') !== 'trabajador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();

$usuarioSesion = $_SESSION['usuario'];
$idUsuario = $usuarioSesion['id_usuario'] ?? 0;

function valorSeguro(PDO $db, string $sql, array $params = [], $default = 0) {
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() ?? $default;
    } catch (Throwable $e) {
        return $default;
    }
}

$totalTareas = valorSeguro(
    $db,
    "SELECT COUNT(*) FROM tarea WHERE id_usuario = ?",
    [$idUsuario]
);

$tareasPendientes = valorSeguro(
    $db,
    "SELECT COUNT(*) FROM tarea WHERE id_usuario = ? AND estado = 'pendiente'",
    [$idUsuario]
);

$tareasProceso = valorSeguro(
    $db,
    "SELECT COUNT(*) FROM tarea WHERE id_usuario = ? AND estado = 'en proceso'",
    [$idUsuario]
);

$tareasFinalizadas = valorSeguro(
    $db,
    "SELECT COUNT(*) FROM tarea WHERE id_usuario = ? AND estado = 'finalizada'",
    [$idUsuario]
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Trabajador — SystemCOFF 360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="min-h-screen bg-green-50">

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
            <a href="trabajador.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/20">
                <i class="fas fa-home w-5"></i>
                Inicio
            </a>

            <a href="tarea.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-clipboard-list w-5"></i>
                Mis tareas
            </a>

            <a href="../trabajador/historial.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-history w-5"></i>
                Historial
            </a>

            <a href="../trabajador/perfil.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10">
                <i class="fas fa-user w-5"></i>
                Mi perfil
            </a>
        </nav>

        <form action="../../controllers/AuthController.php" method="POST">
            <input type="hidden" name="logout" value="1">
            <button class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar sesión
            </button>
        </form>
    </aside>

    <main class="flex-1 p-4 md:p-8">

        <header class="bg-white rounded-3xl p-6 shadow-sm border border-green-100 mb-8">
            <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                Zona del trabajador
            </p>

            <h2 class="text-3xl md:text-4xl font-extrabold text-green-950">
                Bienvenido, <?= htmlspecialchars($usuarioSesion['nombre'] ?? 'Trabajador') ?>
            </h2>

            <p class="text-gray-500 mt-2">
                Aquí puedes consultar tus tareas asignadas, revisar tu historial y actualizar tu información.
            </p>
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

          <a href="tarea.php" class="block">
             <div class="bg-white rounded-3xl p-6 border border-green-100 hover:bg-green-50 transition cursor-pointer">
                 <p class="text-sm text-gray-500">Mis tareas</p>
                <h3 class="text-4xl font-extrabold text-green-950"><?= $totalTareas ?></h3>
                <i class="fas fa-clipboard-list text-green-600 text-2xl mt-4"></i>
    </div>
</a>

            <div class="bg-white rounded-3xl p-6 border border-yellow-100">
                <p class="text-sm text-gray-500">Pendientes</p>
                <h3 class="text-4xl font-extrabold text-yellow-700"><?= $tareasPendientes ?></h3>
                <i class="fas fa-clock text-yellow-600 text-2xl mt-4"></i>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-blue-100">
                <p class="text-sm text-gray-500">En proceso</p>
                <h3 class="text-4xl font-extrabold text-blue-700"><?= $tareasProceso ?></h3>
                <i class="fas fa-spinner text-blue-600 text-2xl mt-4"></i>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-emerald-100">
                <p class="text-sm text-gray-500">Finalizadas</p>
                <h3 class="text-4xl font-extrabold text-emerald-700"><?= $tareasFinalizadas ?></h3>
                <i class="fas fa-check-circle text-emerald-600 text-2xl mt-4"></i>
            </div>

        </section>

        <section class="bg-white rounded-3xl p-6 border border-green-100">
            <h3 class="text-xl font-extrabold text-green-950 mb-4">
                Acciones rápidas
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="tarea.php" class="p-5 rounded-2xl bg-green-50 hover:bg-green-100">
                    <i class="fas fa-tasks text-green-700 text-2xl mb-3"></i>
                    <p class="font-bold text-green-950">Ver mis tareas</p>
                    <p class="text-sm text-gray-500">Consulta tus actividades asignadas.</p>
                </a>

                <a href="../trabajador/historial.php" class="p-5 rounded-2xl bg-yellow-50 hover:bg-yellow-100">
                    <i class="fas fa-history text-yellow-600 text-2xl mb-3"></i>
                    <p class="font-bold text-green-950">Ver historial</p>
                    <p class="text-sm text-gray-500">Revisa tareas y pagos anteriores.</p>
                </a>

                <a href="../trabajador/perfil.php" class="p-5 rounded-2xl bg-blue-50 hover:bg-blue-100">
                    <i class="fas fa-user-edit text-blue-600 text-2xl mb-3"></i>
                    <p class="font-bold text-green-950">Mi perfil</p>
                    <p class="text-sm text-gray-500">Actualiza tus datos personales.</p>
                </a>
            </div>
        </section>

    </main>
</div>

</body>
</html>