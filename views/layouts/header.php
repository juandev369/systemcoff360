<?php
/**
 * ╔══════════════════════════════════════════════╗
 * ║  SystemCOFF 360 — Layout: Header            ║
 * ║  Ruta: views/layouts/header.php             ║
 * ╚══════════════════════════════════════════════╝
 *
 * Uso: include al inicio de cada vista del dashboard
 *   <?php $titulo = 'Gestión de Usuarios'; require_once '../layouts/header.php'; ?>
 */

// Proteger acceso sin sesión
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../usuarios/login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$titulo  = $titulo ?? 'Dashboard';
$alert   = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ── Scrollbar verde ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #020d06; }
        ::-webkit-scrollbar-thumb { background: rgba(34,197,94,.35); border-radius: 10px; }

        /* ── Inputs del sistema ── */
        .sc-input {
            background: rgba(34,197,94,.05);
            border: 1.5px solid rgba(34,197,94,.15);
            color: #ecfdf5;
            transition: all .15s;
        }
        .sc-input:focus {
            outline: none;
            border-color: #22c55e;
            background: rgba(34,197,94,.08);
            box-shadow: 0 0 0 3px rgba(34,197,94,.1);
        }
        .sc-input::placeholder { color: #3a6b4a; }
        .sc-input option { background: #030f08; color: #ecfdf5; }

        /* ── Botón principal ── */
        .btn-primary {
            background: linear-gradient(135deg, #15803d, #22c55e);
            transition: all .2s;
        }
        .btn-primary:hover {
            opacity: .88;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(34,197,94,.35);
        }

        /* ── Card del dashboard ── */
        .sc-card {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(34,197,94,.1);
        }

        /* ── Topbar ── */
        #topbar {
            height: 52px;
            background: #030f08;
            border-bottom: 1px solid rgba(34,197,94,.1);
        }

        /* ── Sidebar ── */
        #sidebar {
            width: 222px;
            background: linear-gradient(180deg, #030f08, #020d06);
            border-right: 1px solid rgba(34,197,94,.09);
        }

        .nav-item {
            color: #3a6b4a;
            font-size: 12px;
            transition: all .12s;
            border-left: 3px solid transparent;
        }
        .nav-item:hover {
            background: rgba(34,197,94,.04);
            color: #86efac;
        }
        .nav-item.active {
            background: rgba(34,197,94,.08);
            color: #4ade80;
            font-weight: 600;
            border-left-color: #22c55e;
        }
    </style>
</head>
<body class="bg-[#020d06] text-gray-100">

<!-- ════════════════════════════════════════
     TOPBAR
════════════════════════════════════════ -->
<header id="topbar" class="fixed top-0 left-0 right-0 z-50 flex items-center px-5 gap-4">

    <!-- Hamburger (mobile) -->
    <button id="sidebar-toggle" class="text-green-600 text-base lg:hidden">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Logo -->
    <div class="flex items-center gap-2.5">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center shadow-lg"
             style="background: linear-gradient(135deg,#15803d,#22c55e)">
            <i class="fas fa-seedling text-white text-xs"></i>
        </div>
        <span style="font-size:13.5px;font-weight:700;color:#86efac;letter-spacing:.2px">SystemCOFF 360</span>
    </div>

    <!-- Título de página -->
    <div class="hidden md:block ml-4 pl-4 border-l border-green-900/40">
        <span style="font-size:13px;color:#3a6b4a"><?= htmlspecialchars($titulo) ?></span>
    </div>

    <div class="ml-auto flex items-center gap-3">
        <!-- Notificaciones (enlace) -->
        <a href="../dashboard/notificaciones.php" title="Alertas"
           class="relative w-8 h-8 rounded-xl bg-green-500/8 border border-green-900/30 flex items-center justify-center text-green-600 hover:text-green-400 hover:bg-green-500/14 transition">
            <i class="fas fa-bell text-sm"></i>
            <?php /* Badge dinámico — integrar con NotificacionModel */ ?>
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-white text-[9px] font-bold flex items-center justify-center">5</span>
        </a>

        <!-- Usuario -->
        <div class="flex items-center gap-2 bg-green-500/6 border border-green-900/30 rounded-xl px-3 py-1.5">
            <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                 style="background: linear-gradient(135deg,#15803d,#22c55e)">
                <?= strtoupper(substr($usuario['nombres'], 0, 1)) ?>
            </div>
            <div class="hidden sm:block">
                <div style="font-size:11.5px;font-weight:600;color:#ecfdf5;line-height:1"><?= htmlspecialchars($usuario['nombres']) ?></div>
                <div style="font-size:9px;color:#3a6b4a;text-transform:capitalize"><?= $usuario['rol'] ?></div>
            </div>
        </div>

        <!-- Logout -->
        <form method="POST" action="../../Controllers/AuthControllers.php" class="flex">
            <input type="hidden" name="logout" value="1">
            <button type="submit" title="Cerrar sesión"
                    class="w-8 h-8 rounded-xl bg-red-500/8 border border-red-900/30 flex items-center justify-center text-red-500 hover:bg-red-500/16 transition">
                <i class="fas fa-sign-out-alt text-sm"></i>
            </button>
        </form>
    </div>
</header>

<!-- ════════════════════════════════════════
     WRAPPER (sidebar + main)
════════════════════════════════════════ -->
<div class="flex" style="padding-top:52px;min-height:100vh">
<?php include_once __DIR__ . '/sidebar.php'; ?>
<main class="flex-1 min-w-0">
<!-- ↑ El contenido de cada vista va aquí ↑ -->

<?php if ($alert): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon:  '<?= htmlspecialchars($alert['icon'])  ?>',
        title: '<?= htmlspecialchars($alert['title']) ?>',
        text:  '<?= htmlspecialchars($alert['text'])  ?>',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#16a34a',
        background: '#030f08',
        color: '#ecfdf5'
    });
});
</script>
<?php endif; ?>
