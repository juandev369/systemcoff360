<?php
session_start();

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../models/Inventario.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();

$inventarioModel = new Inventario($db);

$insumos = $inventarioModel->obtenerInsumos();
$herramientas = $inventarioModel->obtenerHerramientas();
$epps = $inventarioModel->obtenerEpp();
$totales = $inventarioModel->totales();

$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

$paginaActual = basename($_SERVER['PHP_SELF']);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: #f0fdf4;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #052e16 0%, #064e3b 60%, #022c22 100%);
        }

        .tab-btn.active {
            background: #16a34a;
            color: white;
            box-shadow: 0 10px 25px rgba(22, 163, 74, .25);
        }

        .panel {
            display: none;
        }

        .panel.active {
            display: block;
        }

        .field {
            width: 100%;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 11px 13px;
            outline: none;
            background: white;
        }

        .field:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .15);
        }

        .card-hover {
            transition: all .2s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(22, 101, 52, .14);
        }

        .chart-card {
            background:
                radial-gradient(circle at top right, rgba(34,197,94,.10), transparent 35%),
                radial-gradient(circle at bottom left, rgba(234,179,8,.08), transparent 35%),
                white;
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
                        Gestión de finca
                    </p>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-green-950">
                        Inventario
                    </h1>

                    <p class="text-gray-500 mt-2">
                        Control de insumos, herramientas, EPP y alertas de stock.
                    </p>
                </div>

                <div class="flex items-center gap-3">

                    <a href="reporte_inventario_pdf.php" target="_blank"
                       class="bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-3 rounded-2xl shadow-lg flex items-center gap-2 transition">
                        <i class="fas fa-file-pdf"></i>
                        Generar PDF
                    </a>

                    <div class="w-16 h-16 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-warehouse text-2xl"></i>
                    </div>

                </div>

            </div>

        </header>

        <!-- TARJETAS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Insumos</p>
                        <h3 class="text-4xl font-extrabold text-green-950 mt-2">
                            <?= $totales['insumos'] ?>
                        </h3>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center">
                        <i class="fas fa-flask text-xl"></i>
                    </div>
                </div>

                <p class="text-xs text-red-600 mt-4">
                    <?= $totales['insumos_bajos'] ?> con stock bajo
                </p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Herramientas</p>
                        <h3 class="text-4xl font-extrabold text-green-950 mt-2">
                            <?= $totales['herramientas'] ?>
                        </h3>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                </div>

                <p class="text-xs text-green-600 mt-4">
                    <?= $totales['herramientas_disponibles'] ?> disponibles
                </p>
            </div>

            <div class="card-hover bg-white rounded-3xl p-6 border border-green-100">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">EPP</p>
                        <h3 class="text-4xl font-extrabold text-green-950 mt-2">
                            <?= $totales['epp'] ?>
                        </h3>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                        <i class="fas fa-hard-hat text-xl"></i>
                    </div>
                </div>

                <p class="text-xs text-red-600 mt-4">
                    <?= $totales['epp_bajo'] ?> con stock bajo
                </p>
            </div>

            <a href="reporte_inventario_pdf.php" target="_blank"
               class="card-hover bg-white rounded-3xl p-6 border border-red-100 block">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Reporte</p>
                        <h3 class="text-2xl font-extrabold text-red-700 mt-2">
                            PDF
                        </h3>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center">
                        <i class="fas fa-file-pdf text-xl"></i>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-4">
                    Generar reporte de inventario
                </p>
            </a>

        </section>

        <!-- GRÁFICAS PRINCIPALES -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

            <div class="xl:col-span-2 chart-card rounded-3xl p-6 border border-green-100 shadow-sm">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                            Resumen visual
                        </p>

                        <h2 class="text-2xl font-extrabold text-green-950">
                            Estado general del inventario
                        </h2>

                        <p class="text-gray-500 text-sm mt-1">
                            Comparación entre insumos, herramientas y elementos de protección.
                        </p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-chart-column text-xl"></i>
                    </div>
                </div>

                <div class="h-[340px]">
                    <canvas id="graficaInventario"></canvas>
                </div>

            </div>

            <div class="chart-card rounded-3xl p-6 border border-green-100 shadow-sm">

                <div class="mb-6">
                    <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                        Alertas
                    </p>

                    <h2 class="text-2xl font-extrabold text-green-950">
                        Stock crítico
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Elementos que requieren revisión.
                    </p>
                </div>

                <div class="h-[260px]">
                    <canvas id="graficaAlertas"></canvas>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-red-50 p-4">
                        <p class="text-xs text-red-600 font-bold">Insumos bajos</p>
                        <p class="text-3xl font-extrabold text-red-700">
                            <?= $totales['insumos_bajos'] ?>
                        </p>
                    </div>

                    <div class="rounded-2xl bg-yellow-50 p-4">
                        <p class="text-xs text-yellow-600 font-bold">EPP bajo</p>
                        <p class="text-3xl font-extrabold text-yellow-700">
                            <?= $totales['epp_bajo'] ?>
                        </p>
                    </div>
                </div>

            </div>

        </section>

        <!-- GRÁFICAS SECUNDARIAS -->
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

            <div class="chart-card rounded-3xl p-6 border border-green-100 shadow-sm">
                <div class="mb-6">
                    <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                        Insumos
                    </p>

                    <h2 class="text-2xl font-extrabold text-green-950">
                        Stock actual vs mínimo
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Identifica rápidamente qué insumos están por debajo del nivel mínimo.
                    </p>
                </div>

                <div class="h-[330px]">
                    <canvas id="graficaInsumos"></canvas>
                </div>
            </div>

            <div class="chart-card rounded-3xl p-6 border border-green-100 shadow-sm">
                <div class="mb-6">
                    <p class="text-green-700 font-bold uppercase tracking-widest text-xs mb-2">
                        EPP
                    </p>

                    <h2 class="text-2xl font-extrabold text-green-950">
                        Total vs disponible
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Visualiza la disponibilidad de elementos de protección personal.
                    </p>
                </div>

                <div class="h-[330px]">
                    <canvas id="graficaEpp"></canvas>
                </div>
            </div>

        </section>

        <!-- TABS -->
        <section class="bg-white rounded-3xl p-4 border border-green-100 mb-8">
            <div class="flex flex-wrap gap-3">

                <button class="tab-btn active px-5 py-3 rounded-2xl font-bold bg-green-50 text-green-700 transition" onclick="openTab('insumos', this)">
                    <i class="fas fa-flask mr-2"></i>
                    Insumos
                </button>

                <button class="tab-btn px-5 py-3 rounded-2xl font-bold bg-green-50 text-green-700 transition" onclick="openTab('herramientas', this)">
                    <i class="fas fa-tools mr-2"></i>
                    Herramientas
                </button>

                <button class="tab-btn px-5 py-3 rounded-2xl font-bold bg-green-50 text-green-700 transition" onclick="openTab('epp', this)">
                    <i class="fas fa-hard-hat mr-2"></i>
                    EPP
                </button>

            </div>
        </section>

        <!-- PANEL INSUMOS -->
        <section id="insumos" class="panel active">

            

            <div class="grid xl:grid-cols-3 gap-6">

                <div class="xl:col-span-1 bg-white rounded-3xl p-6 border border-green-100 shadow-sm">

                    <h2 class="text-xl font-extrabold text-green-950 mb-4">
                        Nuevo insumo
                    </h2>

                    <form action="../../controllers/InventarioController.php" method="POST" class="space-y-4">

                        <input type="hidden" name="accion" value="crearInsumo">

                        <input class="field" type="text" name="nombre" placeholder="Nombre del insumo" required>

                        <select class="field" name="tipo" required>
                            <option value="">Tipo</option>
                            <option value="fertilizante">Fertilizante</option>
                            <option value="herbicida">Herbicida</option>
                            <option value="fungicida">Fungicida</option>
                            <option value="pesticida">Pesticida</option>
                            <option value="otro">Otro</option>
                        </select>

                        <input class="field" type="text" name="unidad" placeholder="Unidad: kg, L, unidad" required>

                        <div class="grid grid-cols-2 gap-3">
                            <input class="field" type="number" step="0.01" min="0" name="stock_actual" placeholder="Stock actual" required>
                            <input class="field" type="number" step="0.01" min="0" name="stock_minimo" placeholder="Stock mínimo" required>
                        </div>

                        <input class="field" type="number" step="0.01" min="0" name="precio_unidad" placeholder="Precio unidad">

                        
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-2xl font-bold">
                            <i class="fas fa-save mr-2"></i>
                            Guardar insumo
                        </button>

                    </form>

                    <hr class="my-6">

                    <h2 class="text-xl font-extrabold text-green-950 mb-4">
                        Movimiento de stock
                    </h2>

                    <form action="../../controllers/InventarioController.php" method="POST" class="space-y-4">

                        <input type="hidden" name="accion" value="movimientoInsumo">

                        <select class="field" name="id_insumo" required>
                            <option value="">Seleccione insumo</option>

                            <?php foreach ($insumos as $i): ?>
                                <option value="<?= $i['id_insumo'] ?>">
                                    <?= htmlspecialchars($i['nombre']) ?> — <?= $i['stock_actual'] ?> <?= htmlspecialchars($i['unidad']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select class="field" name="tipo_movimiento" required>
                            <option value="entrada">Entrada / compra</option>
                            <option value="salida">Salida / uso</option>
                        </select>

                        <input class="field" type="number" step="0.01" min="0.01" name="cantidad" placeholder="Cantidad" required>

                        <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-2xl font-bold">
                            <i class="fas fa-arrows-rotate mr-2"></i>
                            Actualizar stock
                        </button>

                    </form>

                </div>

                <div class="xl:col-span-2 bg-white rounded-3xl border border-green-100 overflow-hidden shadow-sm">

                    <div class="p-6 border-b border-green-100">
                        <h2 class="text-xl font-extrabold text-green-950">
                            Lista de insumos
                        </h2>

                        <p class="text-sm text-gray-500">
                            Control de existencias y alertas de stock mínimo.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="bg-green-50 text-green-900">
                                <tr>
                                                                        <th class="px-5 py-4 text-left">Insumo</th>
                                    <th class="px-5 py-4 text-left">Tipo</th>
                                    <th class="px-5 py-4 text-left">Stock</th>
                                    <th class="px-5 py-4 text-left">Mínimo</th>
                                    <th class="px-5 py-4 text-left">Precio</th>
                                    <th class="px-5 py-4 text-left">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($insumos) > 0): ?>
                                    <?php foreach ($insumos as $i): ?>
                                        <tr class="border-b last:border-0 hover:bg-green-50/50">
                                                                                        <td class="px-5 py-4 font-bold text-green-950">
                                                <?= htmlspecialchars($i['nombre'] ?? '') ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= htmlspecialchars($i['tipo']) ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= number_format((float)$i['stock_actual'], 2) ?> <?= htmlspecialchars($i['unidad']) ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= number_format((float)$i['stock_minimo'], 2) ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                $<?= number_format((float)$i['precio_unidad'], 0, ',', '.') ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $i['alerta_stock'] === 'bajo' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                                    <?= $i['alerta_stock'] === 'bajo' ? 'Stock bajo' : 'Normal' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                            No hay insumos registrados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </section>

        <!-- PANEL HERRAMIENTAS -->
        <section id="herramientas" class="panel">

            

            <div class="grid xl:grid-cols-3 gap-6">

                <div class="xl:col-span-1 bg-white rounded-3xl p-6 border border-green-100 shadow-sm">

                    <h2 class="text-xl font-extrabold text-green-950 mb-4">
                        Nueva herramienta
                    </h2>

                    <form action="../../controllers/InventarioController.php" method="POST" class="space-y-4">

                        <input type="hidden" name="accion" value="crearHerramienta">

                        <input class="field" type="text" name="nombre" placeholder="Nombre de la herramienta" required>

                        <textarea class="field" name="descripcion" placeholder="Descripción"></textarea>

                        <input class="field" type="date" name="fecha_registro" value="<?= date('Y-m-d') ?>" required>

                        
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-2xl font-bold">
                            <i class="fas fa-save mr-2"></i>
                            Guardar herramienta
                        </button>

                    </form>

                </div>

                <div class="xl:col-span-2 bg-white rounded-3xl border border-green-100 overflow-hidden shadow-sm">

                    <div class="p-6 border-b border-green-100">
                        <h2 class="text-xl font-extrabold text-green-950">
                            Herramientas
                        </h2>

                        <p class="text-sm text-gray-500">
                            Control de disponibilidad de herramientas.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="bg-green-50 text-green-900">
                                <tr>
                                                                        <th class="px-5 py-4 text-left">Herramienta</th>
                                    <th class="px-5 py-4 text-left">Descripción</th>
                                    <th class="px-5 py-4 text-left">Responsable</th>
                                    <th class="px-5 py-4 text-left">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($herramientas) > 0): ?>
                                    <?php foreach ($herramientas as $h): ?>
                                        <tr class="border-b last:border-0 hover:bg-green-50/50">
                                                                                        <td class="px-5 py-4 font-bold text-green-950">
                                                <?= htmlspecialchars($h['nombre'] ?? '') ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= htmlspecialchars($h['descripcion'] ?? '') ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= htmlspecialchars($h['responsable_actual'] ?? 'Sin asignar') ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $h['estado'] === 'disponible' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                                    <?= htmlspecialchars($h['estado']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                                            No hay herramientas registradas.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </section>

        <!-- PANEL EPP -->
        <section id="epp" class="panel">

            

            <div class="grid xl:grid-cols-3 gap-6">

                <div class="xl:col-span-1 bg-white rounded-3xl p-6 border border-green-100 shadow-sm">

                    <h2 class="text-xl font-extrabold text-green-950 mb-4">
                        Nuevo EPP
                    </h2>

                    <form action="../../controllers/InventarioController.php" method="POST" class="space-y-4">

                        <input type="hidden" name="accion" value="crearEpp">

                        <input class="field" type="text" name="nombre" placeholder="Nombre EPP" required>

                        <textarea class="field" name="descripcion" placeholder="Descripción"></textarea>

                        <div class="grid grid-cols-2 gap-3">
                            <input class="field" type="number" min="0" name="cantidad_total" placeholder="Cantidad total" required>
                            <input class="field" type="number" min="0" name="stock_disponible" placeholder="Disponible" required>
                        </div>

                        <input class="field" type="text" name="talla" placeholder="Talla, si aplica">

                        
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-2xl font-bold">
                            <i class="fas fa-save mr-2"></i>
                            Guardar EPP
                        </button>

                    </form>

                </div>

                <div class="xl:col-span-2 bg-white rounded-3xl border border-green-100 overflow-hidden shadow-sm">

                    <div class="p-6 border-b border-green-100">
                        <h2 class="text-xl font-extrabold text-green-950">
                            Elementos de protección personal
                        </h2>

                        <p class="text-sm text-gray-500">
                            Control de dotación y disponibilidad.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="bg-green-50 text-green-900">
                                <tr>
                                                                        <th class="px-5 py-4 text-left">EPP</th>
                                    <th class="px-5 py-4 text-left">Talla</th>
                                    <th class="px-5 py-4 text-left">Total</th>
                                    <th class="px-5 py-4 text-left">Disponible</th>
                                    <th class="px-5 py-4 text-left">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($epps) > 0): ?>
                                    <?php foreach ($epps as $e): ?>
                                        <tr class="border-b last:border-0 hover:bg-green-50/50">
                                                                                        <td class="px-5 py-4">
                                                <p class="font-bold text-green-950">
                                                    <?= htmlspecialchars($e['nombre'] ?? '') ?>
                                                </p>

                                                <p class="text-xs text-gray-500">
                                                    <?= htmlspecialchars($e['descripcion'] ?? '') ?>
                                                </p>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= htmlspecialchars($e['talla'] ?? 'N/A') ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= (int)$e['cantidad_total'] ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <?= (int)$e['stock_disponible'] ?>
                                            </td>

                                            <td class="px-5 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $e['alerta_stock'] === 'bajo' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                                    <?= $e['alerta_stock'] === 'bajo' ? 'Stock bajo' : 'Normal' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                            No hay EPP registrado.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>

                </div>

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
function openTab(id, btn) {
    document.querySelectorAll('.panel').forEach(panel => {
        panel.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(button => {
        button.classList.remove('active');
    });

    document.getElementById(id).classList.add('active');
    btn.classList.add('active');
}

const inventarioData = {
    insumos: <?= (int)$totales['insumos'] ?>,
    herramientas: <?= (int)$totales['herramientas'] ?>,
    epp: <?= (int)$totales['epp'] ?>,
    herramientasDisponibles: <?= (int)$totales['herramientas_disponibles'] ?>,
    insumosBajos: <?= (int)$totales['insumos_bajos'] ?>,
    eppBajo: <?= (int)$totales['epp_bajo'] ?>
};

const nombresInsumos = <?= json_encode(array_column($insumos, 'nombre')) ?>;
const stockInsumos = <?= json_encode(array_map('floatval', array_column($insumos, 'stock_actual'))) ?>;
const stockMinimoInsumos = <?= json_encode(array_map('floatval', array_column($insumos, 'stock_minimo'))) ?>;

const nombresEpp = <?= json_encode(array_column($epps, 'nombre')) ?>;
const totalEpp = <?= json_encode(array_map('intval', array_column($epps, 'cantidad_total'))) ?>;
const disponibleEpp = <?= json_encode(array_map('intval', array_column($epps, 'stock_disponible'))) ?>;

Chart.defaults.font.family = 'Arial';
Chart.defaults.color = '#374151';

new Chart(document.getElementById('graficaInventario'), {
    type: 'bar',
    data: {
        labels: ['Insumos', 'Herramientas', 'EPP'],
        datasets: [
            {
                label: 'Total registrado',
                data: [
                    inventarioData.insumos,
                    inventarioData.herramientas,
                    inventarioData.epp
                ],
                backgroundColor: [
                    'rgba(22, 163, 74, 0.78)',
                    'rgba(59, 130, 246, 0.78)',
                    'rgba(234, 179, 8, 0.78)'
                ],
                borderColor: [
                    'rgb(22, 163, 74)',
                    'rgb(59, 130, 246)',
                    'rgb(234, 179, 8)'
                ],
                borderWidth: 2,
                borderRadius: 16,
                barThickness: 70
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

new Chart(document.getElementById('graficaAlertas'), {
    type: 'doughnut',
    data: {
        labels: ['Insumos bajos', 'EPP bajo', 'Sin alerta'],
        datasets: [
            {
                data: [
                    inventarioData.insumosBajos,
                    inventarioData.eppBajo,
                    Math.max(0, inventarioData.insumos + inventarioData.epp - inventarioData.insumosBajos - inventarioData.eppBajo)
                ],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.85)',
                    'rgba(234, 179, 8, 0.85)',
                    'rgba(22, 163, 74, 0.85)'
                ],
                borderWidth: 0
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

new Chart(document.getElementById('graficaInsumos'), {
    type: 'bar',
    data: {
        labels: nombresInsumos,
        datasets: [
            {
                label: 'Stock actual',
                data: stockInsumos,
                backgroundColor: 'rgba(22, 163, 74, 0.75)',
                borderRadius: 12
            },
            {
                label: 'Stock mínimo',
                data: stockMinimoInsumos,
                backgroundColor: 'rgba(239, 68, 68, 0.65)',
                borderRadius: 12
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

new Chart(document.getElementById('graficaEpp'), {
    type: 'bar',
    data: {
        labels: nombresEpp,
        datasets: [
            {
                label: 'Cantidad total',
                data: totalEpp,
                backgroundColor: 'rgba(234, 179, 8, 0.75)',
                borderRadius: 12
            },
            {
                label: 'Disponible',
                data: disponibleEpp,
                backgroundColor: 'rgba(22, 163, 74, 0.75)',
                borderRadius: 12
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

</script>
    <?php include __DIR__ . '/../layouts/assistant_widget.php'; ?>
</body>
</html>