<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SystemCOFF 360 — Gestión de Fincas Cafeteras</title>
    <link rel="shortcut icon" type="image/png" href="../img/ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fcfdfc;
        }

        /* ── GRADIENTE PREMIUM ── */
        .gradient-bg {
            background: linear-gradient(135deg, #064e3b 0%, #16a34a 60%, #22c55e 100%);
        }

        .text-gradient {
            background: linear-gradient(135deg, #064e3b 0%, #16a34a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── GLASS NAVIGATION ── */
        .nav-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(22, 163, 74, 0.1);
        }

        /* ── ANIMACIONES ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* ── CARDS PREMIUM ── */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(6, 78, 59, 0.12);
        }

        .mod-card {
            transition: all 0.3s ease;
            background: white;
            border: 1px solid rgba(22, 163, 74, 0.08);
        }

        .mod-card:hover {
            border-color: #22c55e;
            background: #f0fdf4 !important;
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.08);
        }

        /* ── SCROLL SMOOTH ── */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <!-- ════════════════════════════════════════════════
         NAVBAR
    ════════════════════════════════════════════════ -->
    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">

                <!-- LOGO -->
                <div class="flex items-center gap-4 group cursor-pointer">
                    <div
                        class="w-11 h-11 gradient-bg rounded-2xl flex items-center justify-center shadow-2xl shadow-green-900/20 group-hover:rotate-6 transition-transform duration-300">
                        <i class="fas fa-seedling text-white text-xl"></i>
                    </div>
                    <div class="leading-tight">
                        <div class="text-green-950 font-black text-xl leading-none tracking-tight">SystemCOFF <span class="text-green-600">360</span></div>
                        <div class="text-green-700/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Gestión Inteligente</div>
                    </div>
                </div>

                <!-- LINKS DESKTOP -->
                <div class="hidden md:flex items-center space-x-10">
                    <a href="#inicio" class="text-sm font-bold text-green-900 hover:text-green-600 transition-colors">Inicio</a>
                    <a href="#nosotros" class="text-sm font-bold text-green-900 hover:text-green-600 transition-colors">Nosotros</a>
                    <a href="#modulos" class="text-sm font-bold text-green-900 hover:text-green-600 transition-colors">Módulos</a>
                    <a href="#objetivos" class="text-sm font-bold text-green-900 hover:text-green-600 transition-colors mr-4">Objetivos</a>
                    <a href="../views/usuarios/login.php"
                        class="gradient-bg text-white px-7 py-3 rounded-2xl hover:scale-105 transition-all text-sm font-black shadow-xl shadow-green-900/20">
                        Ingresar
                    </a>
                </div>

                <!-- MOBILE BUTTON -->
                <button id="menu-btn" class="md:hidden text-green-600 text-xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- MOBILE MENU -->
        <div id="mobile-menu" class="hidden md:hidden px-4 pb-4 bg-white border-t border-green-200 shadow-lg">
            <a href="#inicio" class="block py-2 text-sm text-green-700">Inicio</a>
            <a href="#nosotros" class="block py-2 text-sm text-green-700">Nosotros</a>
            <a href="#modulos" class="block py-2 text-sm text-green-700">Módulos</a>
            <a href="#objetivos" class="block py-2 text-sm text-green-700">Objetivos</a>
            <a href="../views/usuarios/login.php"
                class="block mt-3 gradient-bg text-white text-center py-2 rounded-xl text-sm font-semibold shadow-md">
                <i class="fas fa-sign-in-alt mr-1"></i> Ingresar al sistema
            </a>
        </div>
    </nav>


    <!-- ════════════════════════════════════════════════
         HERO SLIDER
    ════════════════════════════════════════════════ -->
    <section id="inicio" class="relative h-[580px] overflow-hidden text-white">

        <!-- SLIDE 1 -->
        <div class="slide active relative h-full">
            <img src="https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=1600&q=80"
                class="absolute inset-0 w-full h-full object-cover" style="filter: brightness(0.32) saturate(1.2)"
                alt="Finca cafetera Colombia">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4 animate-up">
                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 text-green-300 text-[10px] uppercase tracking-[0.3em] font-black px-4 py-2 rounded-full mb-6">
                    <i class="fas fa-seedling"></i> JD Solutions
                </span>
                <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight drop-shadow-2xl text-white">
                    Gestiona tu finca<br>
                    <span class="text-green-400">desde tu bolsillo</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                    La plataforma más completa para el registro de cosechas, control de inventario,
                    tareas y nómina de tu finca cafetera.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                    <a href="../views/usuarios/login.php"
                        class="w-full sm:w-auto gradient-bg text-white px-10 py-4 rounded-2xl font-bold text-base shadow-2xl shadow-green-900/40 hover:scale-105 transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Ingresar al sistema
                    </a>
                    <a href="#modulos"
                        class="w-full sm:w-auto bg-white/10 backdrop-blur-md border border-white/30 text-white px-10 py-4 rounded-2xl font-bold text-base hover:bg-white/20 transition-all">
                        <i class="fas fa-th-large mr-2"></i>Ver módulos
                    </a>
                </div>
            </div>
        </div>

        <!-- SLIDE 2 -->
        <div class="slide relative h-full">
            <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1600&q=80"
                class="absolute inset-0 w-full h-full object-cover" style="filter: brightness(0.3) saturate(1.3)"
                alt="Campo verde Colombia">
            <div class="absolute inset-0"
                style="background: linear-gradient(135deg, rgba(2,44,18,.75) 0%, rgba(2,13,6,.4) 100%)"></div>
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                <span
                    class="inline-flex items-center gap-2 bg-yellow-400/20 border border-yellow-400/30 text-yellow-300 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-6">
                    <i class="fas fa-leaf"></i> Módulo de Producción
                </span>
                <h1 class="text-4xl md:text-6xl font-bold mb-5 leading-tight">
                    Cosechas y ventas<br>
                    <span class="text-yellow-300">en tiempo real</span>
                </h1>
                <p class="text-lg max-w-2xl text-green-100 leading-relaxed mb-8">
                    Registra cada cosecha, controla precios de referencia y genera reportes
                    financieros automáticos para tu finca.
                </p>
                <a
                    class="gradient-bg text-white px-8 py-3 rounded-xl font-bold text-base shadow-xl hover:opacity-90 transition">
                    <i class="fas fa-chart-line mr-2"></i>Puedes ver tus reportes en tiempo real
                </a>
            </div>
        </div>

        <!-- SLIDE 3 -->
        <div class="slide relative h-full">
            <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1600&q=80"
                class="absolute inset-0 w-full h-full object-cover" style="filter: brightness(0.28) saturate(1.1)"
                alt="Gestión agrícola">
            <div class="absolute inset-0"
                style="background: linear-gradient(135deg, rgba(2,44,18,.75) 0%, rgba(2,13,6,.4) 100%)"></div>
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                <span
                    class="inline-flex items-center gap-2 bg-blue-400/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-6">
                    <i class="fas fa-users"></i> Módulo de Trabajadores
                </span>
                <h1 class="text-4xl md:text-6xl font-bold mb-5 leading-tight">
                    Tareas y nómina<br>
                    <span class="text-blue-300">bajo control</span>
                </h1>
                <p class="text-lg max-w-2xl text-green-100 leading-relaxed mb-8">
                    Asigna tareas, registra jornales y controla anticipos de tus trabajadores
                    desde el panel de administrador o la vista móvil.
                </p>
                <a 
                    class="gradient-bg text-white px-8 py-3 rounded-xl font-bold text-base shadow-xl hover:opacity-90 transition">
                    <i class="fas fa-clipboard-check mr-2"></i>Gestionar tareas de tu finca
                </a>
            </div>
        </div>

        <!-- CONTROLES SLIDER -->
        <button onclick="changeSlide(-1)"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/50 transition border border-white/10">
            <i class="fas fa-chevron-left text-sm"></i>
        </button>
        <button onclick="changeSlide(1)"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-black/50 transition border border-white/10">
            <i class="fas fa-chevron-right text-sm"></i>
        </button>

        <!-- DOTS INDICADORES -->
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2" id="dots-container">
            <div class="dot h-2 rounded-full bg-green-400 transition-all duration-300" style="width:28px"></div>
            <div class="dot h-2 w-2 rounded-full bg-white/40 transition-all duration-300"></div>
            <div class="dot h-2 w-2 rounded-full bg-white/40 transition-all duration-300"></div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════════
         ESTADÍSTICAS RÁPIDAS
    ════════════════════════════════════════════════ -->
    <section class="bg-white border-y border-green-100 py-12">
        <div class="max-w-6xl mx-auto px-4 flex flex-wrap justify-center gap-6">
            <?php
            $stats = [
                ['19', 'fas fa-th-large', 'green', 'Módulos del sistema'],
                ['360°', 'fas fa-sync-alt', 'yellow', 'Gestión integral'],
            ];
            foreach ($stats as [$val, $ico, $color, $lbl]):
                $colors = [
                    'green' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-600'],
                    'yellow' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-600'],
                    'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-600'],
                ];
                $c = $colors[$color];
                ?>
                <div class="<?= $c['bg'] ?> border <?= $c['border'] ?> rounded-2xl p-6 text-center shadow-sm w-full sm:w-72">
                    <?php if ($ico): ?>
                        <div class="<?= $c['text'] ?> text-3xl mb-2"><i class="<?= $ico ?>"></i></div>
                    <?php endif; ?>
                    <div class="<?= $c['text'] ?> text-3xl font-bold mb-1"><?= $val ?></div>
                    <div class="text-green-700 text-xs font-semibold uppercase tracking-wide"><?= $lbl ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- ════════════════════════════════════════════════
         NOSOTROS — MISIÓN Y VISIÓN
    ════════════════════════════════════════════════ -->
    <section id="nosotros" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <span
                    class="inline-flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                    <i class="fas fa-leaf"></i> Sobre el sistema
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-4">Misión y Visión</h2>
                <p class="text-green-600 max-w-xl mx-auto text-sm">
                    Construido para la realidad de las fincas cafeteras colombianas
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- MISIÓN -->
                <div
                    class="feature-card bg-green-50 border border-green-100 shadow-sm p-8 rounded-2xl border-t-4 border-t-green-500">
                    <div
                        class="w-14 h-14 gradient-bg rounded-2xl flex items-center justify-center mb-5 shadow-md shadow-green-200">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-green-800 mb-4">Nuestra Misión</h2>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Proveer una herramienta tecnológica intuitiva que facilite la gestión integral de fincas
                        cafeteras, cubriendo desde el registro de cosechas y ventas hasta el control de inventario,
                        tareas de trabajadores, nómina y análisis financiero. Todo en un solo sistema accesible
                        desde cualquier dispositivo.
                    </p>
                </div>

                <!-- VISIÓN -->
                <div
                    class="feature-card bg-green-50 border border-yellow-100 shadow-sm p-8 rounded-2xl border-t-4 border-t-yellow-400">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 shadow-md shadow-yellow-200"
                        style="background: linear-gradient(135deg, #eab308, #facc15)">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-yellow-600 mb-4">Nuestra Visión</h2>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Ser el sistema de gestión agrícola referente para fincas cafeteras en Huila y el eje
                        cafetero colombiano, reconocido por su facilidad de uso, confiabilidad de datos y
                        capacidad para transformar el trabajo en campo en información valiosa para la toma
                        de decisiones del productor cafetero.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════════
         MÓDULOS DEL SISTEMA
    ════════════════════════════════════════════════ -->
    <section id="modulos" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <span
                    class="inline-flex items-center gap-2 bg-green-100 border border-green-200 text-green-700 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                    <i class="fas fa-th-large"></i> 19 pantallas · cobertura total
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-4">Módulos del sistema</h2>
                <p class="text-gray-600 max-w-xl mx-auto text-sm">
                    Cobertura completa de todos los procesos de tu finca cafetera
                </p>
            </div>

            <?php
            $modulos = [
                ['🌱', 'Lotes', 'Gestión de cultivos y sectores de la finca', '#22c55e'],
                ['🌾', 'Cosechas', 'Registro por lote, calidad del grano y estado', '#a3e635'],
                ['💰', 'Ventas', 'Historial financiero y precio de referencia', '#eab308'],
                ['📋', 'Tareas Admin', 'Tablero Kanban para asignar labores en campo', '#84cc16'],
                ['📱', 'Vista Móvil', 'App para trabajadores en campo sin necesidad de PC', '#38bdf8'],
                ['📦', 'Inventario', 'Control de activos, equipos e infraestructura', '#0891b2'],
                ['🧪', 'Insumos', 'Fertilizantes y pesticidas con alerta de stock', '#10b981'],
                ['🦺', 'EPP', 'Dotación y elementos de protección personal', '#d97706'],
                ['🔧', 'Herramientas', 'Registro de préstamos y devolución', '#b45309'],
                ['💳', 'Nómina', 'Jornales, quincenas y anticipos a trabajadores', '#4f46e5'],
                ['🏭', 'Proveedores', 'Directorio y órdenes de compra a proveedores', '#0f766e'],
                ['🛒', 'Compras', 'Registro con actualización automática de stock', '#15803d'],
                ['📊', 'Reportes', 'Análisis del trimestre, gráficos y exportación PDF', '#ca8a04'],
                ['🔔', 'Notificaciones', 'Alertas automáticas de stock bajo y tareas vencidas', '#dc2626'],
            ];
            ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <?php foreach ($modulos as [$ico, $nombre, $desc, $color]): ?>
                    <div class="mod-card bg-white shadow-sm border border-green-100/50 rounded-3xl p-6 cursor-default hover:shadow-xl transition-all">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-sm border border-gray-50"><?= $ico ?></div>
                        <div class="font-black text-sm mb-2 text-green-950 uppercase tracking-tight"><?= $nombre ?></div>
                        <div class="text-gray-500 text-xs leading-relaxed font-medium"><?= $desc ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════════
         OBJETIVOS ESTRATÉGICOS
    ════════════════════════════════════════════════ -->
    <section id="objetivos" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <span
                    class="inline-flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">
                    <i class="fas fa-flag"></i> Propósito del proyecto
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-4">Objetivos Estratégicos</h2>
                <div class="w-24 h-1 bg-green-500 mx-auto mt-2 rounded-full"></div>
            </div>

            <?php
            $objetivos = [
                [
                    'fas fa-bolt',
                    '#22c55e',
                    'Automatización',
                    'Reducir el tiempo de registro de cosechas, gastos e inventario en más del 60% para el administrador de la finca.'
                ],
                [
                    'fas fa-shield-alt',
                    '#eab308',
                    'Seguridad',
                    'Garantizar la integridad y confidencialidad de los registros productivos y financieros de la finca.'
                ],
                [
                    'fas fa-chart-line',
                    '#38bdf8',
                    'Seguimiento',
                    'Facilitar el análisis financiero mensual y trimestral para detectar oportunidades de mejora en producción.'
                ],
            ];
            ?>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($objetivos as [$icon, $color, $titulo, $texto]): ?>
                    <div class="feature-card text-center p-8 bg-green-50 border border-green-100 rounded-2xl shadow-sm">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-5 shadow-md"
                            style="background: linear-gradient(135deg, <?= $color ?>dd, <?= $color ?>)">
                            <i class="<?= $icon ?>"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-3" style="color: <?= $color ?>"><?= $titulo ?></h3>
                        <p class="text-gray-600 text-sm leading-relaxed"><?= $texto ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════════
         CTA FINAL
    ════════════════════════════════════════════════ -->
    <section class="py-24 bg-green-50 border-t border-green-100">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="text-6xl mb-6">🌿</div>
            <h2 class="text-3xl md:text-5xl font-bold text-green-800 mb-5 leading-tight">
                ¿Listo para gestionar<br>tu finca de manera inteligente?
            </h2>
            <p class="text-green-700 text-base mb-10 max-w-xl mx-auto leading-relaxed">
                Accede al sistema y comienza a registrar cosechas, controlar inventario y gestionar
                a tu equipo de trabajo desde cualquier dispositivo.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="../views/usuarios/login.php"
                    class="gradient-bg text-white px-10 py-4 rounded-2xl font-bold text-base shadow-lg shadow-green-200 hover:opacity-90 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Ingresar al sistema
                </a>
                <a href="../views/usuarios/registre.php"
                    class="bg-white border-2 border-green-200 text-green-700 px-10 py-4 rounded-2xl font-bold text-base hover:bg-green-50 transition shadow-sm">
                    <i class="fas fa-user-plus mr-2"></i>Crear cuenta
                </a>
            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════════
         FOOTER
    ════════════════════════════════════════════════ -->
    <footer class="bg-[#022c22] text-white py-20 mt-12">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-16">
            
            <!-- Columna 1: Branding -->
            <div class="col-span-1">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 gradient-bg rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-seedling text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tighter">SystemCOFF 360</span>
                </div>
                <p class="text-green-100/60 text-sm leading-relaxed mb-8 max-w-xs">
                    Impulsando la caficultura colombiana con herramientas de gestión 360°. Tecnología diseñada por y para el campo.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-green-500 transition-all duration-300">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-green-500 transition-all duration-300">
                        <i class="fab fa-whatsapp text-sm"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-green-500 transition-all duration-300">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Columna 2: Módulos -->
            <div>
                <h4 class="text-white font-black uppercase text-xs tracking-[0.2em] mb-8">Módulos principales</h4>
                <ul class="grid grid-cols-1 gap-4 text-green-100/60 text-sm font-medium">
                    <li class="flex items-center gap-3"><i class="fas fa-check text-green-500 text-[10px]"></i> Lotes y Cosechas</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check text-green-500 text-[10px]"></i> Ventas y Reportes</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check text-green-500 text-[10px]"></i> Tareas y Nómina</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check text-green-500 text-[10px]"></i> Inventario e Insumos</li>
                </ul>
            </div>

            <!-- Columna 3: Contacto -->
            <div>
                <h4 class="text-white font-black uppercase text-xs tracking-[0.2em] mb-8">Contacto</h4>
                <div class="space-y-6">
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-400 group-hover:bg-green-500 group-hover:text-white transition-all">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <span class="text-sm text-green-100/60">soporte@systemcoff.com</span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-400 group-hover:bg-green-500 group-hover:text-white transition-all">
                            <i class="fas fa-phone-alt text-sm"></i>
                        </div>
                        <span class="text-sm text-green-100/60">+57 3209737168</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 border-t border-white/5 mt-20 pt-10 text-center">
            <p class="text-[10px] uppercase tracking-[0.3em] text-green-100/20 font-black">
                &copy; <?= date('Y') ?> Todos los derechos reservados a JD Solutions
            </p>
        </div>
    </footer>


    <script>
        // ── SLIDER ────────────────────────────────────────────
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('#dots-container .dot');

        function changeSlide(direction) {
            // Reset dot actual
            dots[currentSlide].style.width = '8px';
            dots[currentSlide].style.backgroundColor = 'rgba(255,255,255,0.4)';

            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + direction + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');

            // Activar dot nuevo
            dots[currentSlide].style.width = '28px';
            dots[currentSlide].style.backgroundColor = '#22c55e';
        }

        // Auto slide cada 6 segundos
        setInterval(() => changeSlide(1), 6000);

        // Dots clickeables
        dots.forEach((dot, i) => {
            dot.style.cursor = 'pointer';
            dot.addEventListener('click', () => {
                const dir = i > currentSlide ? 1 : -1;
                while (currentSlide !== i) changeSlide(dir);
            });
        });

        // ── MOBILE MENU ───────────────────────────────────────
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

        // Cerrar mobile menu al hacer click en link
        mobileMenu.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => mobileMenu.classList.add('hidden'));
        });
    </script>
    <?php include __DIR__ . '/../views/layouts/assistant_widget.php'; ?>
</body>
</html>