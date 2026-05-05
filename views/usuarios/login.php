<?php
session_start();
$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SystemCOFF 360 — Iniciar Sesión</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .gradient-bg {
            background: linear-gradient(135deg, #14532d 0%, #16a34a 60%, #22c55e 100%);
        }

        /* Panel izquierdo */
        .left-panel {
            background: linear-gradient(150deg, #041f0a 0%, #062910 60%, #031207 100%);
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            width: 380px; height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34,197,94,.1), transparent 65%);
            top: -90px; right: -70px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(234,179,8,.07), transparent 65%);
            bottom: -60px; left: -40px;
        }

        /* Input styles */
        .field-input {
            background: #ffffff;
            border: 1.5px solid #bbf7d0;
            color: #166534;
            transition: border-color .15s, background .15s;
        }
        .field-input:focus {
            outline: none;
            border-color: #22c55e;
            background: #f0fdf4;
            box-shadow: 0 0 0 3px rgba(34,197,94,.12);
        }
        .field-input::placeholder { color: #9ca3af; }

        /* Botón principal */
        .btn-primary {
            background: linear-gradient(135deg, #15803d, #22c55e);
            transition: all .2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(34,197,94,.4);
            opacity: .92;
        }

        /* Feature dots */
        .feat-dot       { width:6px;height:6px;border-radius:50%;background:#22c55e;box-shadow:0 0 7px rgba(34,197,94,.5);flex-shrink:0; }
        .feat-dot.gold  { background:#eab308;box-shadow:0 0 7px rgba(234,179,8,.4); }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <!-- GRID DE FONDO -->
    <div class="fixed inset-0 pointer-events-none" style="background-image: repeating-linear-gradient(0deg,transparent,transparent 48px,rgba(34,197,94,.025) 48px,rgba(34,197,94,.025) 49px),repeating-linear-gradient(90deg,transparent,transparent 48px,rgba(34,197,94,.025) 48px,rgba(34,197,94,.025) 49px);z-index:0"></div>

    <div class="w-full max-w-4xl relative z-10">

        <!-- CARD PRINCIPAL -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[580px]"
             style="border: 1px solid rgba(34,197,94,.1)">

            <!-- ── PANEL IZQUIERDO ──────────────────────── -->
            <div class="left-panel hidden md:flex md:w-5/12 p-12 flex-col justify-between">
                <div class="relative z-10">
                    <!-- Logo -->
                    <div class="flex items-center gap-3 mb-10">
                        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center shadow-lg shadow-green-900/40">
                            <i class="fas fa-seedling text-white"></i>
                        </div>
                        <div>
                            <div class="text-green-300 font-bold text-base leading-none">SystemCOFF 360</div>
                            <div class="text-green-700 text-[9px] uppercase tracking-widest mt-0.5">Gestión de Fincas</div>
                        </div>
                    </div>

                    <h2 class="text-3xl font-bold text-white mb-4 leading-tight">
                        ¡Bienvenido<br>de nuevo!
                    </h2>
                    <p class="text-sm text-green-500 leading-relaxed mb-8">
                        Accede a tu panel para registrar cosechas, gestionar tareas, controlar inventario y
                        analizar el rendimiento de tu finca.
                    </p>

                    <!-- Features -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3 text-sm text-green-400">
                            <span class="feat-dot"></span>Registro de cosechas y ventas
                        </div>
                        <div class="flex items-center gap-3 text-sm text-green-400">
                            <span class="feat-dot"></span>Control de inventario con alertas
                        </div>
                        <div class="flex items-center gap-3 text-sm text-green-400">
                            <span class="feat-dot"></span>Gestión de tareas para trabajadores
                        </div>
                        <div class="flex items-center gap-3 text-sm text-green-400">
                            <span class="feat-dot gold"></span>Vista móvil para campo sin internet
                        </div>
                        <div class="flex items-center gap-3 text-sm text-green-400">
                            <span class="feat-dot gold"></span>Reportes y análisis financiero
                        </div>
                    </div>
                </div>

                <!-- Badges roles -->
                <div class="relative z-10">
                    <div class="bg-green-500/8 border border-green-500/15 rounded-2xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-lock text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-green-400 leading-tight">Acceso seguro con cifrado bcrypt.</p>
                            <p class="text-xs text-green-700 mt-0.5">Finca Los Guácimos · Tesalia, Huila</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── PANEL DERECHO (FORM) ─────────────────── -->
            <div class="w-full md:w-7/12 p-8 md:p-12 flex flex-col justify-center bg-green-50 border-l border-green-100">

                <!-- Logo mobile -->
                <div class="flex items-center justify-center gap-3 mb-8 md:hidden">
                    <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-seedling text-white text-sm"></i>
                    </div>
                    <span class="text-green-800 font-bold text-base">SystemCOFF 360</span>
                </div>

                <!-- Title -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-green-500" style="box-shadow:0 0 8px rgba(34,197,94,.6)"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-green-600">Iniciar sesión</span>
                    </div>
                    <div class="inline-block bg-green-100/60 border border-green-200 rounded-2xl px-6 py-2.5 mb-3 shadow-sm">
                        <h1 class="text-3xl font-bold text-green-800 m-0">Ingresar al sistema</h1>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Ingresa tus credenciales para continuar</p>
                </div>

                <!-- FORM -->
                <form action="../../controllers/AuthController.php" method="POST" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-green-800 mb-1.5">
                            Correo electrónico <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" required placeholder="admin@finca.com"
                                   class="field-input w-full pl-10 pr-4 py-3 rounded-xl text-sm">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-green-800 mb-1.5">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="password-input" required placeholder="••••••••"
                                   class="field-input w-full pl-10 pr-12 py-3 rounded-xl text-sm">
                            <button type="button" id="toggle-pass"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 text-sm hover:text-green-600 transition">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                        <div class="text-right mt-2">
                            <a href="#" class="text-xs text-green-600 hover:underline">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <!-- Recordar -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember"
                               class="h-4 w-4 rounded" style="accent-color:#22c55e">
                        <label for="remember" class="text-sm text-gray-600">Recordar mi sesión por 7 días</label>
                    </div>

                    <!-- Roles info -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                        <p class="text-xs text-gray-600 mb-2">Roles del sistema:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">
                                <i class="fas fa-crown mr-1"></i>Administrador
                            </span>
                            <span class="bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                <i class="fas fa-hard-hat mr-1"></i>Trabajador
                            </span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="btn-primary w-full text-white font-bold py-3.5 rounded-xl text-sm shadow-xl shadow-green-900/30">
                        <i class="fas fa-sign-in-alt mr-2"></i> Entrar al sistema
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-8 flex flex-col gap-2 items-center">
                    <p class="text-sm text-gray-600">
                        ¿No tienes cuenta?
                        <a href="registre.php" class="text-green-600 font-bold hover:underline ml-1">
                            Regístrate aquí
                        </a>
                    </p>
                    <a href="../../index.php" class="text-xs text-gray-500 hover:text-green-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
                    </a>
                </div>

                <!-- Footer branding -->
                <div class="mt-8 pt-6 text-center border-t border-green-100">
                    <p class="text-xs text-gray-500">
                        SystemCOFF 360 v2.0 · <strong class="text-green-700">JD Solutions</strong> · SENA Ficha 3230026
                    </p>
                </div>
            </div>

        </div>
    </div>


    <!-- ── SWEETALERT ──────────────────────────────────────── -->
    <?php if ($alert): ?>
    <script>
        Swal.fire({
            icon:  '<?= htmlspecialchars($alert['icon'])  ?>',
            title: '<?= htmlspecialchars($alert['title']) ?>',
            text:  '<?= htmlspecialchars($alert['text'])  ?>',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#16a34a',
            background: '#ffffff',
            color: '#1f2937',
            iconColor: '<?= $alert['icon'] === 'success' ? '#22c55e' : '#f87171' ?>'
        });
    </script>
    <?php endif; ?>

    <script>
        // Toggle ver/ocultar contraseña
        document.getElementById('toggle-pass').addEventListener('click', function () {
            const input = document.getElementById('password-input');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type    = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type    = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    </script>

</body>
</html>
