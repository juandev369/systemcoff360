<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();
$usuarioModel = new Usuario($db);

$usuariosOriginales = $usuarioModel->obtenerTodos();
$usuarios = $usuariosOriginales;

$buscar = strtolower(trim($_GET['buscar'] ?? ''));
$filtroRol = strtolower(trim($_GET['rol'] ?? ''));
$filtroEstado = strtolower(trim($_GET['estado'] ?? ''));

if ($buscar !== '' || $filtroRol !== '' || $filtroEstado !== '') {
    $usuarios = array_filter($usuarios, function ($u) use ($buscar, $filtroRol, $filtroEstado) {
        $nombre = strtolower($u['nombre'] ?? '');
        $correo = strtolower($u['correo'] ?? '');
        $rol = strtolower($u['rol'] ?? '');
        $estado = strtolower($u['estado'] ?? '');

        $coincideBusqueda = $buscar === '' || str_contains($nombre, $buscar) || str_contains($correo, $buscar);
        $coincideRol = $filtroRol === '' || $rol === $filtroRol;
        $coincideEstado = $filtroEstado === '' || $estado === $filtroEstado;

        return $coincideBusqueda && $coincideRol && $coincideEstado;
    });
}

$totalUsuarios = count($usuariosOriginales);
$totalAdmins = 0;
$totalTrabajadores = 0;
$totalActivos = 0;
$totalInactivos = 0;

foreach ($usuariosOriginales as $u) {
    $rol = strtolower($u['rol'] ?? '');
    $estado = strtolower($u['estado'] ?? '');

    if ($rol === 'administrador') {
        $totalAdmins++;
    }

    if ($rol === 'trabajador') {
        $totalTrabajadores++;
    }

    if ($estado === 'activo') {
        $totalActivos++;
    } else {
        $totalInactivos++;
    }
}

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

// IMPORTANTE: este nombre debe coincidir con tu archivo real en controllers
$controladorAdmin = '../../controllers/adminUsuariocontrollers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios — SystemCOFF 360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0fdf4;
        }

        .sidebar {
            background: linear-gradient(180deg, #052e16 0%, #064e3b 60%, #022c22 100%);
        }

        .card-hover {
            transition: all .2s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(22, 101, 52, .12);
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
            <a href="admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
                <i class="fas fa-chart-line w-5"></i>
                Dashboard
            </a>

            <a href="usuarios.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/20 text-green-100">
                <i class="fas fa-users w-5"></i>
                Usuarios
            </a>

            <a href="usuario_crear.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
                <i class="fas fa-user-plus w-5"></i>
                Crear usuario
            </a>

            <a href="lotes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition">
                <i class="fas fa-map-marked-alt w-5"></i>
                Lotes
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
                        Gestión administrativa
                    </p>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-green-950">
                        Usuarios del sistema
                    </h1>
                    <p class="text-gray-500 mt-2">
                        Consulta, filtra, activa, desactiva o elimina usuarios registrados en SystemCOFF 360.
                    </p>
                </div>

                <a href="usuario_crear.php" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-2xl font-bold">
                    <i class="fas fa-user-plus"></i>
                    Crear trabajador
                </a>
            </div>
        </header>

        <!-- TARJETAS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <p class="text-sm text-gray-500">Total usuarios</p>
                <h3 class="text-4xl font-extrabold text-green-950 mt-2"><?= $totalUsuarios ?></h3>
                <p class="text-xs text-gray-500 mt-2">Registrados en el sistema</p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <p class="text-sm text-gray-500">Administradores</p>
                <h3 class="text-4xl font-extrabold text-green-950 mt-2"><?= $totalAdmins ?></h3>
                <p class="text-xs text-gray-500 mt-2">Usuarios con acceso total</p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <p class="text-sm text-gray-500">Trabajadores</p>
                <h3 class="text-4xl font-extrabold text-green-950 mt-2"><?= $totalTrabajadores ?></h3>
                <p class="text-xs text-gray-500 mt-2">Personal operativo</p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <p class="text-sm text-gray-500">Usuarios activos</p>
                <h3 class="text-4xl font-extrabold text-green-950 mt-2"><?= $totalActivos ?></h3>
                <p class="text-xs text-gray-500 mt-2"><?= $totalInactivos ?> usuarios inactivos</p>
            </div>
        </section>

        <!-- FILTROS -->
        <section class="bg-white rounded-3xl p-6 border border-green-100 shadow-sm mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-green-900 mb-2">Buscar usuario</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>"
                           placeholder="Buscar por nombre o correo..."
                           class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Rol</label>
                    <select name="rol" class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Todos</option>
                        <option value="administrador" <?= $filtroRol === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="trabajador" <?= $filtroRol === 'trabajador' ? 'selected' : '' ?>>Trabajador</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-green-900 mb-2">Estado</label>
                    <select name="estado" class="w-full px-4 py-3 rounded-2xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Todos</option>
                        <option value="activo" <?= $filtroEstado === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $filtroEstado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex flex-col md:flex-row gap-3">
                    <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-2xl font-bold">
                        <i class="fas fa-search mr-2"></i>
                        Filtrar
                    </button>

                    <a href="usuarios.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-2xl font-bold text-center">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </section>

        <!-- TABLA -->
        <section class="bg-white rounded-3xl border border-green-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-green-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-extrabold text-green-950">
                        Lista completa de usuarios
                    </h2>
                    <p class="text-sm text-gray-500">
                        Mostrando <?= count($usuarios) ?> usuario(s).
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-green-50 text-green-900">
                        <tr>
                            <th class="px-5 py-4 text-left">Usuario</th>
                            <th class="px-5 py-4 text-left">Contacto</th>
                            <th class="px-5 py-4 text-left">Rol</th>
                            <th class="px-5 py-4 text-left">Estado</th>
                            <th class="px-5 py-4 text-left">Registro</th>
                            <th class="px-5 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $u): ?>
                            <?php
                                $idUsuario = (int)($u['id_usuario'] ?? 0);
                                $estadoUsuario = strtolower($u['estado'] ?? '');
                                $rolUsuario = strtolower($u['rol'] ?? '');
                                $esYo = $idUsuario === (int)($_SESSION['usuario']['id'] ?? 0);
                                $nuevoEstado = $estadoUsuario === 'activo' ? 'inactivo' : 'activo';
                            ?>
                            <tr class="border-b last:border-0 hover:bg-green-50/50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center font-bold">
                                            <?= strtoupper(substr($u['nombre'] ?? 'U', 0, 1)) ?>
                                        </div>

                                        <div>
                                            <p class="font-extrabold text-green-950">
                                                <?= htmlspecialchars($u['nombre'] ?? '') ?>
                                                <?php if ($esYo): ?>
                                                    <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Tú</span>
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                DNI: <?= htmlspecialchars($u['DNI'] ?? 'Sin DNI') ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-700"><?= htmlspecialchars($u['correo'] ?? '') ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($u['telefono'] ?? 'Sin teléfono') ?></p>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $rolUsuario === 'administrador' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' ?>">
                                        <?= htmlspecialchars($u['rol'] ?? 'Sin rol') ?>
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $estadoUsuario === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                                        <?= htmlspecialchars($u['estado'] ?? '') ?>
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-gray-600">
                                    <?= htmlspecialchars($u['fecha_registro'] ?? '') ?>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">

                                        <?php if (!$esYo): ?>
                                            <!-- ACTIVAR / DESACTIVAR -->
                                            <form action="<?= $controladorAdmin ?>" method="POST" class="form-cambiar-estado">
                                                <input type="hidden" name="accion" value="cambiarEstado">
                                                <input type="hidden" name="id" value="<?= $idUsuario ?>">
                                                <input type="hidden" name="estado" value="<?= $nuevoEstado ?>">
                                                <input type="hidden" name="volver" value="usuarios">

                                                <button type="submit"
                                                        class="px-3 py-2 rounded-xl <?= $estadoUsuario === 'activo' ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' ?>"
                                                        title="<?= $estadoUsuario === 'activo' ? 'Desactivar usuario' : 'Activar usuario' ?>">
                                                    <i class="fas <?= $estadoUsuario === 'activo' ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                                </button>
                                            </form>

                                            <!-- ELIMINAR -->
                                            <form action="<?= $controladorAdmin ?>" method="POST" class="form-eliminar">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?= $idUsuario ?>">
                                                <input type="hidden" name="volver" value="usuarios">

                                                <button type="submit" class="px-3 py-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Sin acciones</span>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                No se encontraron usuarios con esos filtros.
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
    icon: '<?= htmlspecialchars($alert['icon'] ?? 'success') ?>',
    title: '<?= htmlspecialchars($alert['title'] ?? '') ?>',
    text: '<?= htmlspecialchars($alert['text'] ?? '') ?>',
    confirmButtonColor: '#16a34a'
});
</script>
<?php endif; ?>

<script>
document.querySelectorAll('.form-cambiar-estado').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const estado = this.querySelector('input[name="estado"]').value;
        const texto = estado === 'activo'
            ? '¿Deseas volver a activar este usuario?'
            : '¿Deseas desactivar este usuario?';

        Swal.fire({
            icon: 'question',
            title: 'Confirmar acción',
            text: texto,
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});

document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>

</body>
</html>