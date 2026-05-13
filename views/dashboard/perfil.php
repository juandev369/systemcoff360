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

/* CONSULTAR DATOS DEL USUARIO */
$stmt = $conexion->prepare("
    SELECT 
        u.id_usuario,
        u.nombre,
        u.DNI,
        u.telefono,
        u.correo,
        u.estado,
        u.fecha_registro,
        r.nombre AS rol
    FROM usuario u
    INNER JOIN rol r ON u.id_rol = r.id_rol
    WHERE u.id_usuario = ?
    LIMIT 1
");

$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("No se encontró la información del usuario.");
}

/* ACTUALIZAR PERFIL */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);

    $actualizar = $conexion->prepare("
        UPDATE usuario 
        SET nombre = ?, DNI = ?, telefono = ?, correo = ?
        WHERE id_usuario = ?
    ");

    $actualizar->execute([
        $nombre,
        $dni,
        $telefono,
        $correo,
        $idUsuario
    ]);

    $_SESSION['usuario']['nombre'] = $nombre;

    header("Location: perfil.php?actualizado=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Trabajador</title>
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

        <section class="bg-white rounded-3xl shadow-sm border border-green-100 p-8 mb-8">
            <p class="text-sm font-bold text-green-600 uppercase tracking-widest">
                Zona del trabajador
            </p>

            <h1 class="text-4xl font-extrabold text-green-950 mt-2">
                Mi perfil
            </h1>

            <p class="text-gray-600 mt-2">
                Consulta y actualiza tu información personal.
            </p>
        </section>

        <?php if (isset($_GET['actualizado'])): ?>
            <div class="bg-green-100 border border-green-300 text-green-800 rounded-2xl p-4 mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                Perfil actualizado correctamente.
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- CARD PERFIL -->
            <section class="bg-white rounded-3xl shadow-sm border border-green-100 p-8">

                <div class="flex flex-col items-center text-center">

                    <div class="w-28 h-28 bg-green-600 rounded-full flex items-center justify-center text-white text-5xl mb-4">
                        <i class="fas fa-user"></i>
                    </div>

                    <h2 class="text-2xl font-extrabold text-green-950">
                        <?= htmlspecialchars($usuario['nombre']) ?>
                    </h2>

                    <p class="text-green-600 font-bold mt-1">
                        <?= htmlspecialchars($usuario['rol']) ?>
                    </p>

                    <span class="mt-4 px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-700">
                        <?= htmlspecialchars($usuario['estado']) ?>
                    </span>

                </div>

                <div class="mt-8 space-y-4 text-sm">

                    <div class="flex items-center gap-3 text-gray-600">
                        <i class="fas fa-id-card text-green-600 w-5"></i>
                        <?= htmlspecialchars($usuario['DNI']) ?>
                    </div>

                    <div class="flex items-center gap-3 text-gray-600">
                        <i class="fas fa-phone text-green-600 w-5"></i>
                        <?= htmlspecialchars($usuario['telefono'] ?? 'Sin teléfono') ?>
                    </div>

                    <div class="flex items-center gap-3 text-gray-600">
                        <i class="fas fa-envelope text-green-600 w-5"></i>
                        <?= htmlspecialchars($usuario['correo']) ?>
                    </div>

                    <div class="flex items-center gap-3 text-gray-600">
                        <i class="fas fa-calendar text-green-600 w-5"></i>
                        Registrado: <?= htmlspecialchars($usuario['fecha_registro']) ?>
                    </div>

                </div>

            </section>

            <!-- FORMULARIO -->
            <section class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-green-100 p-8">

                <h2 class="text-2xl font-bold text-green-950 mb-6">
                    Actualizar información
                </h2>

                <form method="POST" class="space-y-5">

                    <div>
                        <label class="block text-sm font-bold text-green-900 mb-2">
                            Nombre completo
                        </label>
                        <input 
                            type="text" 
                            name="nombre"
                            value="<?= htmlspecialchars($usuario['nombre']) ?>"
                            required
                            class="w-full border border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-green-900 mb-2">
                            DNI
                        </label>
                        <input 
                            type="text" 
                            name="dni"
                            value="<?= htmlspecialchars($usuario['DNI']) ?>"
                            required
                            class="w-full border border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-green-900 mb-2">
                            Teléfono
                        </label>
                        <input 
                            type="text" 
                            name="telefono"
                            value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                            class="w-full border border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-green-900 mb-2">
                            Correo electrónico
                        </label>
                        <input 
                            type="email" 
                            name="correo"
                            value="<?= htmlspecialchars($usuario['correo']) ?>"
                            required
                            class="w-full border border-green-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="pt-4">
                        <button 
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl">
                            <i class="fas fa-save mr-2"></i>
                            Guardar cambios
                        </button>
                    </div>

                </form>

            </section>

        </div>

    </main>

</div>

</body>
</html>