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
    <title>SystemCOFF 360 — Registro de Usuario</title>
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

    <div class="w-full max-w-2xl relative z-10 animate-up my-12">

        <!-- CARD -->
        <div class="bg-white rounded-[40px] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden border border-gray-100">

            <!-- HEADER -->
            <div class="gradient-bg px-8 py-14 text-center text-white relative overflow-hidden">
                <!-- Decoración -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>

                <div class="relative z-10">
                    <!-- Logo -->
                    <div class="flex items-center justify-center gap-4 mb-10">
                        <div class="w-11 h-11 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/30">
                            <i class="fas fa-seedling text-white text-lg"></i>
                        </div>
                        <span class="text-2xl font-black tracking-tighter">SystemCOFF 360</span>
                    </div>

                    <h2 class="text-4xl font-black mb-4 tracking-tight">Únete a la Revolución</h2>
                    <p class="text-white/80 font-medium max-w-sm mx-auto leading-relaxed">
                        Crea tu cuenta hoy mismo y transforma la gestión de tu finca cafetera.
                    </p>
                </div>
            </div>

            <!-- FORM BODY -->
            <form action="../../controllers/UsuarioController.php" method="POST" id="reg-form"
                  class="p-8 md:p-10">
                <input type="hidden" name="rol" value="trabajador">

                <!-- SECCIÓN 1: DATOS PERSONALES -->
                <div class="mb-7">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-6 h-6 gradient-bg rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0">1</div>
                        <span class="text-xs font-bold uppercase tracking-wider text-green-600">Datos personales</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-green-800">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 z-10 pointer-events-none">
                                    <i class="fas fa-user text-sm"></i>
                                </span>
                                <input type="text" name="nombres" required maxlength="100"
                                       placeholder="Ej. Juan Carlos"
                                       class="field-input w-full pl-10 pr-4 py-3 rounded-xl text-sm">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-green-800">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 z-10 pointer-events-none">
                                    <i class="fas fa-user-tag text-sm"></i>
                                </span>
                                <input type="text" name="apellidos" required maxlength="100"
                                       placeholder="Ej. Pérez Rodríguez"
                                       class="field-input w-full pl-10 pr-4 py-3 rounded-xl text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: CONTACTO -->
                <div class="mb-7">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-6 h-6 gradient-bg rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0">2</div>
                        <span class="text-xs font-bold uppercase tracking-wider text-green-600">Información de contacto</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <!-- Correo -->
                    <div class="space-y-1 mb-5">
                        <label class="text-xs font-bold text-green-800">
                            Correo electrónico <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 z-10 pointer-events-none">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" required maxlength="150"
                                   placeholder="correo@ejemplo.com"
                                   class="field-input w-full pl-10 pr-4 py-3 rounded-xl text-sm">
                        </div>
                    </div>

                    <!-- Teléfono + Rol -->
                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-green-800">
                                Teléfono <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 z-10 pointer-events-none">
                                    <i class="fas fa-phone text-sm"></i>
                                </span>
                                <input type="text" name="telefono" required maxlength="30"
                                       placeholder="Ej. 3001234567"
                                       class="field-input w-full pl-10 pr-4 py-3 rounded-xl text-sm">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SECCIÓN 3: CONTRASEÑA -->
                <div class="mb-7">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-6 h-6 gradient-bg rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0">3</div>
                        <span class="text-xs font-bold uppercase tracking-wider text-green-600">Seguridad de acceso</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <!-- Contraseña -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-green-800">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 z-10 pointer-events-none">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                <input type="password" name="password" id="pass1" required
                                       placeholder="Mínimo 8 caracteres"
                                       class="field-input w-full pl-10 pr-11 py-3 rounded-xl text-sm"
                                       oninput="checkStrength(this.value)">
                                <button type="button" onclick="togglePass('pass1','eye1')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 text-sm hover:text-green-600 transition z-10">
                                    <i class="fas fa-eye" id="eye1"></i>
                                </button>
                            </div>
                            <!-- Barra de seguridad -->
                            <div class="mt-2">
                                <div class="grid grid-cols-4 gap-1 mb-1">
                                    <div class="str-bar bg-gray-200" id="s1"></div>
                                    <div class="str-bar bg-gray-200" id="s2"></div>
                                    <div class="str-bar bg-gray-200" id="s3"></div>
                                    <div class="str-bar bg-gray-200" id="s4"></div>
                                </div>
                                <p class="text-xs text-gray-500" id="strength-label">Escribe tu contraseña</p>
                            </div>
                        </div>

                        <!-- Confirmar -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-green-800">
                                Confirmar contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 z-10 pointer-events-none">
                                    <i class="fas fa-check-double text-sm"></i>
                                </span>
                                <input type="password" name="confirmar_password" id="pass2" required
                                       placeholder="Repite la contraseña"
                                       class="field-input w-full pl-10 pr-11 py-3 rounded-xl text-sm"
                                       oninput="checkMatch()">
                                <button type="button" onclick="togglePass('pass2','eye2')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 text-sm hover:text-green-600 transition z-10">
                                    <i class="fas fa-eye" id="eye2"></i>
                                </button>
                            </div>
                            <p class="text-xs mt-1 min-h-[16px]" id="match-label"></p>
                        </div>
                    </div>

                    <!-- Hints de contraseña -->
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-700 mb-3">La contraseña debe tener:</p>
                        <div class="grid grid-cols-2 gap-1.5">
                            <div class="hint-off flex items-center gap-2 text-xs" id="h-len">
                                <i class="fas fa-circle text-[5px]"></i><span>Mínimo 8 caracteres</span>
                            </div>
                            <div class="hint-off flex items-center gap-2 text-xs" id="h-num">
                                <i class="fas fa-circle text-[5px]"></i><span>Al menos un número</span>
                            </div>
                            <div class="hint-off flex items-center gap-2 text-xs" id="h-upper">
                                <i class="fas fa-circle text-[5px]"></i><span>Una letra mayúscula</span>
                            </div>
                            <div class="hint-off flex items-center gap-2 text-xs" id="h-spec">
                                <i class="fas fa-circle text-[5px]"></i><span>Un símbolo (recomendado)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TÉRMINOS -->
                <div class="mb-10 flex items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="flex items-center h-5 mt-0.5 flex-shrink-0">
                        <input id="terms" type="checkbox" name="terms" required
                               class="w-5 h-5 rounded-lg border-gray-300 text-green-600 focus:ring-green-500/20">
                    </div>
                    <label for="terms" class="text-xs text-gray-500 leading-relaxed font-medium">
                        Acepto los <a href="#" class="text-green-600 font-black hover:text-green-700 transition-colors">términos de servicio</a> y la política de privacidad de SystemCOFF 360.
                    </label>
                </div>

                <!-- SUBMIT -->
                <button type="submit" id="submit-btn"
                        class="btn-primary w-full py-5 rounded-2xl text-white font-black text-base shadow-xl tracking-tight">
                    Crear Cuenta Ahora
                </button>

                <!-- Links -->
                <div class="mt-10 text-center">
                    <p class="text-sm font-bold text-gray-400">
                        ¿Ya tienes una cuenta? 
                        <a href="login.php" class="text-green-600 hover:text-green-700 font-black ml-1">Inicia sesión</a>
                    </p>
                    <a href="../../public/index.php" class="inline-flex items-center gap-2 mt-8 text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            </form>

            <!-- FOOTER CARD -->
            <div class="px-8 py-6 text-center border-t border-gray-50 bg-gray-50/30">
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black">
                    Desarrollado por JD Solutions
                </p>
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
        }).then(() => {
            <?php if (!empty($alert['redirect'])): ?>
                window.location.href = '<?= htmlspecialchars($alert['redirect']) ?>';
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>


    <script>
        // ── TOGGLE PASSWORD ─────────────────────────────────
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type     = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type     = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // ── PASSWORD STRENGTH ────────────────────────────────
        function checkStrength(val) {
            const colors = ['', '#dc2626', '#eab308', '#84cc16', '#22c55e'];
            const labels = ['', 'Muy débil', 'Débil', 'Media', 'Fuerte'];
            const label  = document.getElementById('strength-label');
            const bars   = ['s1','s2','s3','s4'];

            let score = 0;
            if (val.length >= 8)               score++;
            if (/[A-Z]/.test(val))             score++;
            if (/[0-9]/.test(val))             score++;
            if (/[^A-Za-z0-9]/.test(val))      score++;

            bars.forEach((id, i) => {
                document.getElementById(id).style.background = i < score ? colors[score] : '#e5e7eb';
            });

            label.textContent = val.length > 0 ? labels[score] : 'Escribe tu contraseña';
            label.style.color = score > 0 ? colors[score] : '#6b7280';

            // Hints
            setHint('h-len',   val.length >= 8);
            setHint('h-num',   /[0-9]/.test(val));
            setHint('h-upper', /[A-Z]/.test(val));
            setHint('h-spec',  /[^A-Za-z0-9]/.test(val));
        }

        function setHint(id, ok) {
            const el  = document.getElementById(id);
            const ico = el.querySelector('i');
            if (ok) {
                el.className  = 'hint-ok flex items-center gap-2 text-xs';
                ico.className = 'fas fa-check-circle';
            } else {
                el.className  = 'hint-off flex items-center gap-2 text-xs';
                ico.className = 'fas fa-circle text-[5px]';
            }
        }

        // ── MATCH CHECK ──────────────────────────────────────
        function checkMatch() {
            const p1  = document.getElementById('pass1').value;
            const p2  = document.getElementById('pass2').value;
            const lbl = document.getElementById('match-label');
            if (!p2) { lbl.textContent = ''; return; }
            if (p1 === p2) {
                lbl.textContent = '✓ Las contraseñas coinciden';
                lbl.style.color = '#22c55e';
            } else {
                lbl.textContent = '✕ Las contraseñas no coinciden';
                lbl.style.color = '#f87171';
            }
        }

        // ── VALIDACIÓN FORM ──────────────────────────────────
        document.getElementById('reg-form').addEventListener('submit', function (e) {
            const p1 = document.getElementById('pass1').value;
            const p2 = document.getElementById('pass2').value;

            if (p1 !== p2) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseñas diferentes',
                    text: 'Las contraseñas no coinciden. Por favor verifica.',
                    confirmButtonText: 'Corregir',
                    confirmButtonColor: '#16a34a',
                    background: '#ffffff',
                    color: '#1f2937'
                });
                return;
            }

            if (p1.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña muy corta',
                    text: 'La contraseña debe tener al menos 8 caracteres.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#16a34a',
                    background: '#ffffff',
                    color: '#1f2937'
                });
            }
        });
    </script>

    <?php include __DIR__ . '/../layouts/assistant_widget.php'; ?>

</body>
</html>
