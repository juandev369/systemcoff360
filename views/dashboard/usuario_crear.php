<?php
session_start();

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Trabajador — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
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

        .field {
            width: 100%;
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 13px 15px;
            outline: none;
            transition: all .15s ease;
        }

        .field:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22,163,74,.15);
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

        <form action="../../controllers/adminUsuariocontrollers.php" method="POST" id="formCrearUsuario">
            <input type="hidden" name="logout" value="1">
            <button class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-100 transition">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar sesión
            </button>
        </form>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-5 md:p-10">

        <div class="max-w-4xl mx-auto">

            <!-- HEADER -->
            <div class="bg-white rounded-3xl p-7 shadow-sm border border-green-100 mb-8">
                <a href="admin.php" class="text-green-700 font-bold text-sm hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al dashboard
                </a>

                <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div>
                        <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                            Gestión de usuarios
                        </p>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-green-950">
                            Registrar trabajador
                        </h1>
                        <p class="text-gray-500 mt-2">
                            Crea una cuenta para un trabajador sin salir del panel administrativo.
                        </p>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="bg-white rounded-3xl p-7 md:p-9 shadow-sm border border-green-100">
                <form action="../../controllers/adminUsuariocontrollers.php" method="POST" id="formCrearUsuario">
                    <input type="hidden" name="accion" value="crear">
                    <input type="hidden" name="rol" value="trabajador">

                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-xl flex items-center justify-center font-bold">1</div>
                            <h2 class="text-lg font-extrabold text-green-950">Datos personales</h2>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-green-900 mb-2">
                                    Nombres <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nombres" required maxlength="100" class="field" placeholder="Ej. Juan Carlos">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-green-900 mb-2">
                                    Apellidos <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="apellidos" required maxlength="100" class="field" placeholder="Ej. Pérez Rodríguez">
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-xl flex items-center justify-center font-bold">2</div>
                            <h2 class="text-lg font-extrabold text-green-950">Información de contacto</h2>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-green-900 mb-2">
                                    Correo electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" required maxlength="150" class="field" placeholder="trabajador@correo.com">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-green-900 mb-2">
                                    Teléfono
                                </label>
                                <input type="text" name="telefono" maxlength="30" class="field" placeholder="Ej. 3001234567">
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-xl flex items-center justify-center font-bold">3</div>
                            <h2 class="text-lg font-extrabold text-green-950">Acceso al sistema</h2>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-green-900 mb-2">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" required minlength="8" class="field" placeholder="Mínimo 8 caracteres">
                                <p class="text-xs text-gray-500 mt-2">Debe tener mínimo 8 caracteres.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-green-900 mb-2">
                                    Confirmar contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="confirmar_password" id="confirmar_password" required minlength="8" class="field" placeholder="Repite la contraseña">
                                <p id="mensajePassword" class="text-xs mt-2"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-2xl p-5 mb-8">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <p class="font-bold text-green-950">Rol asignado</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Este formulario registra únicamente usuarios con rol <strong>trabajador</strong>.
                                    El trabajador podrá acceder al sistema con el correo y contraseña asignados.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-3">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-5 py-4 rounded-2xl font-bold shadow-lg shadow-green-900/20">
                            <i class="fas fa-save mr-2"></i>
                            Registrar trabajador
                        </button>

                        <a href="admin.php" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-4 rounded-2xl font-bold">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>

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

<script>
const password = document.getElementById('password');
const confirmar = document.getElementById('confirmar_password');
const mensaje = document.getElementById('mensajePassword');

function validarPasswords() {
    if (!confirmar.value) {
        mensaje.textContent = '';
        return;
    }

    if (password.value === confirmar.value) {
        mensaje.textContent = '✓ Las contraseñas coinciden';
        mensaje.style.color = '#16a34a';
    } else {
        mensaje.textContent = '✕ Las contraseñas no coinciden';
        mensaje.style.color = '#dc2626';
    }
}

password.addEventListener('input', validarPasswords);
confirmar.addEventListener('input', validarPasswords);

document.getElementById('formCrearUsuario').addEventListener('submit', function(e) {
    if (password.value !== confirmar.value) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Contraseñas diferentes',
            text: 'La contraseña y su confirmación deben ser iguales.',
            confirmButtonColor: '#16a34a'
        });
    }
});
</script>

</body>
</html>