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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        .gradient-bg {
            background: linear-gradient(135deg, #14532d 0%, #16a34a 100%);
        }

        /* Panel izquierdo */
        .left-panel {
            background: linear-gradient(160deg, #064e3b 0%, #16a34a 100%);
            position: relative;
            overflow: hidden;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Input styles */
        .field-input {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .field-input:focus {
            outline: none;
            border-color: #22c55e;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #15803d, #22c55e);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 12px 24px rgba(34, 197, 94, 0.3);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:32px_32px]">

    <div class="w-full max-w-5xl relative z-10 animate-up">

        <!-- CARD PRINCIPAL -->
        <div class="bg-white rounded-[40px] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col md:flex-row min-h-[640px] border border-gray-100">

            <!-- ── PANEL IZQUIERDO ──────────────────────── -->
            <div class="left-panel hidden md:flex md:w-5/12 p-14 flex-col justify-between text-white">
                <div class="relative z-10">
                    <!-- Logo -->
                    <div class="flex items-center gap-4 mb-14">
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/20 shadow-2xl">
                            <i class="fas fa-seedling text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-white font-black text-xl tracking-tight leading-none">SystemCOFF <span class="text-green-400">360</span></div>
                            <div class="text-green-400/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Gestión Inteligente</div>
                        </div>
                    </div>

                    <h2 class="text-4xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                        Cultiva el <br><span class="text-green-400 text-gradient bg-clip-text">Éxito</span> de tu Finca.
                    </h2>
                    <p class="text-base text-green-100/60 leading-relaxed mb-10 font-medium">
                        Accede a la plataforma líder en gestión cafetera y toma el control total de tu producción.
                    </p>

                    <!-- Features -->
                    <div class="space-y-5">
                        <div class="flex items-center gap-4 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-green-500/20 transition-colors">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-green-100/80">Cosechas en tiempo real</span>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-green-500/20 transition-colors">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-green-100/80">Inventario inteligente</span>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-green-500/20 transition-colors">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-green-100/80">Reportes detallados</span>
                        </div>
                    </div>
                </div>

                <!-- Footer del panel -->
                <div class="glass-panel rounded-3xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center text-green-400">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-wider">Acceso Seguro</p>
                            <p class="text-[11px] text-green-100/50 mt-0.5 font-medium">Protección de datos garantizada</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── PANEL DERECHO (FORM) ─────────────────── -->
            <div class="w-full md:w-7/12 p-10 md:p-16 flex flex-col justify-center bg-white">
                
                <div class="mb-10 text-center md:text-left">
                    <h3 class="text-3xl font-black text-green-950 mb-2 tracking-tight">Iniciar Sesión</h3>
                    <p class="text-gray-500 font-medium">Ingresa tus credenciales para continuar</p>
                </div>

                <form action="../../controllers/AuthController.php" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-green-900/40 mb-2 ml-1">Correo Electrónico</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm z-10 pointer-events-none"></i>
                            <input type="email" name="email" required placeholder="tu@ejemplo.com"
                                class="field-input w-full pl-12 pr-5 py-4 rounded-2xl text-sm font-semibold text-green-950 placeholder:text-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-green-900/40 mb-2 ml-1">Contraseña</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm z-10 pointer-events-none"></i>
                            <input type="password" name="password" id="password-input" required placeholder="••••••••"
                                class="field-input w-full pl-12 pr-12 py-4 rounded-2xl text-sm font-semibold text-green-950 placeholder:text-gray-300">
                            <button type="button" id="toggle-pass" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors z-10">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-gray-300 text-green-600 focus:ring-green-500/20">
                            <span class="text-xs font-bold text-gray-500 group-hover:text-green-700 transition-colors">Recordarme</span>
                        </label>
                        <a href="#" class="text-xs font-black text-green-600 hover:text-green-700 transition-colors">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" name="login"
                        class="btn-primary w-full py-5 rounded-2xl text-white font-black text-base shadow-xl tracking-tight">
                        Entrar al Sistema
                    </button>
                </form>

                <div class="mt-12 text-center">
                    <p class="text-sm font-bold text-gray-400">
                        ¿No tienes una cuenta? 
                        <a href="registre.php" class="text-green-600 hover:text-green-700 font-black ml-1">Regístrate gratis</a>
                    </p>
                    <a href="../../public/index.php" class="inline-flex items-center gap-2 mt-8 text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left"></i> Volver al Inicio
                    </a>
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

    <?php include __DIR__ . '/../layouts/assistant_widget.php'; ?>

</body>
</html>
