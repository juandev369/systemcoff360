<?php
session_start();

require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../models/Inventario.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

$totalAlertas = ($totales['insumos_bajos'] ?? 0) + ($totales['epp_bajo'] ?? 0);
$usuarioNombre = $_SESSION['usuario']['nombre'] ?? $_SESSION['usuario']['nombres'] ?? 'Administrador';
$usuarioRol = ucfirst($_SESSION['usuario']['rol'] ?? 'Administrador');

function getImageUrl($url, $name, $category = 'agriculture') {
    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
        return $url;
    }
    // Usamos Unsplash para obtener imágenes profesionales
    return "https://source.unsplash.com/320x240/?" . urlencode($category . "," . $name);
}

ob_start();

// Preparar logos
$logoPath = __DIR__ . '/../../img/logo.png';
$logoSrc = '';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,' . $logoData;
}

$icoPath = __DIR__ . '/../../img/ico.png';
$icoSrc = '';
if (file_exists($icoPath)) {
    $icoData = base64_encode(file_get_contents($icoPath));
    $icoSrc = 'data:image/png;base64,' . $icoData;
}

// Icono genérico para insumos (una bolsa/frasco verde)
$insumoIconBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAADJklEQVR4nO2Yv2vUUBTHPyeitX9AtXV0E8XByVmsm90cHBy6OujmJm5u4uDg6OLo4uji6ODi6ODgIPrHODm5ioM4OIiD6B/QStXm8vIuL7m8vByS964fCHmX38m77yS599w8REREhIQUXWAD2AV2gAnpZreA38Ar4DfAnKAn9vA7oD0oYF6mAsEhsAn8BD4DP4HfgCHpYreALvAY6AALAn8EPI/L3wZ6pC72COgF7oMvAAb8BJ6Sj3AHOAnclZf9XvIeYMDX3N894D7QE5e/Gf87V9Z+Rz6iA9yS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyfA86f8XFqVqYDPwBfAnDByf5S9Z0m4D5O9/U8A90vAn8CPiR7O+3z+X008B/yY+PwTcC20X/8p4F8LPTu0/iX/O9H8Bf87XfbOfVn7fXmPNfHO78p7rIsXfl9e9mshXfOAt6S7N29n7Xf9v0Daf9v/p/fA87i/n0O6f+A16f4nAn9E6v+vAn+N3f9X6f/7Cvx5u//p0P7fI939v0v7v6L3v9eBv2z3P8v6f0P7/36U/r9P7P+nSft/X/r//X96D7yL9r/Dof3/IOn+P0u6/2m7/0fU/+vS/n8+tP+fId3/fOn+Z0v7P6H3PxH4nO3+N0L7f0+6+zvS/c+X7v/v0P7f9/+9/+/8f/9/+P9eK/+eLe9eKe+Wv8/Xv7Xf/4u/e97ue6Xvle5fLu9m7bft7/9m76mB65G677P7F0f7P6X3v6L3f066/7PS/U+V9n9G6v95pP4/n9r/zyPd/0zo//e83fdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yv9ArZ3H4X/AAnHAAAAAElFTkSuQmCC';

// Icono genérico para herramientas (una llave inglesa azul)
$herramientaIconBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC8klEQVR4nO2Zv2vUUBTHPyeitX9AtXV0E8XByVmsm90cHBy6OujmJm5u4uDg6OLo4uji6ODi6ODgIPrHODm5ioM4OIiD6B/QStXm8vIuL7m8vByS964fCHmX38m77yS599w8REREhIQUXWAD2AV2gAnpZreA38Ar4DfAnKAn9vA7oD0oYF6mAsEhsAn8BD4DP4HfgCHpYreALvAY6AALAn8EPI/L3wZ6pC72COgF7oMvAAb8BJ6Sj3AHOAnclZf9XvIeYMDX3N894D7QE5e/Gf87V9Z+Rz6iA9yS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyfA86f8XFqVqYDPwBfAnDByf5S9Z0m4D5O9/U8A90vAn8CPiR7O+3z+X008B/yY+PwTcC20X/8p4F8LPTu0/iX/O9H8Bf87XfbOfVn7fXmPNfHO78p7rIsXfl9e9mshXfOAt6S7N29n7Xf9v0Daf9v/p/fA87i/n0O6f+A16f4nAn9E6v+vAn+N3f9X6f/7Cvx5u//p0P7fI939v0v7v6L3v9eBv2z3P8v6f0P7/36U/r9P7P+nSft/X/r//X96D7yL9r/Dof3/IOn+P0u6/2m7/0fU/+vS/n8+tP+fId3/fOn+Z0v7P6H3PxH4nO3+N0L7f0+6+zvS/c+X7v/v0P7f9/+9/+/8f/9/+P9eK/+eLe9eKe+Wv8/Xv7Xf/4u/e97ue6Xvle5fLu9m7bft7/9m76mB65G677P7F0f7P6X3v6L3f066/7PS/U+V9n9G6v95pP4/n9r/zyPd/0zo//e83fdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yt9r/S90vdK3yv9ArZ3H4X/AAnHAAAAAElFTkSuQmCC';

// Icono genérico para EPP (un casco amarillo)
$eppIconBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC6klEQVR4nO2Zv2vUUBTHPyeitX9AtXV0E8XByVmsm90cHBy6OujmJm5u4uDg6OLo4uji6ODi6ODgIPrHODm5ioM4OIiD6B/QStXm8vIuL7m8vByS964fCHmX38m77yS599w8REREhIQUXWAD2AV2gAnpZreA38Ar4DfAnKAn9vA7oD0oYF6mAsEhsAn8BD4DP4HfgCHpYreALvAY6AALAn8EPI/L3wZ6pC72COgF7oMvAAb8BJ6Sj3AHOAnclZf9XvIeYMDX3N894D7QE5e/Gf87V9Z+Rz6iA9yS99hI3vV6/D6Pffw+0BWf78XlP5f32ELe+WpM+0ZcPof3fX7f/xPwNf7m9YjP5fG78Xke966T7/Uv/p8re98lvshrvylf59P8/S/+Xv/6+DqP+9fN4/f9P4Hv8beZ698k/u8BPyfA86f8XFqVqYDPwBfAnDByf5S9Z0m4D5O9/U8A90vAn8CPiR7O+3z+X008B/yY+PwTcC20X/8p4F8LPTu0/iX/O9H8Bf87XfbOfVn7fXmPNfHO78p7rIsXfl9e9mshXfOAt6S7N29n7Xf9v0Daf9v/p/fA87i/n0O6f+A16f4nAn9E6v+vAn+N3f9X6f/7Cvx5u//p0P7fI939v0v7v6L3v9eBv2z3P8v6f0P7/36U/r9P7P+nSft/X/r//X96D7yL9r/Dof3/IOn+P0u6/2m7/0fU/+vS/n8+tP+fId3/fOn+Z0v7P6H3PxH4nO3+N0L7f0+6+zvS/c+X7v/v0P7f9/+9/+/8f/9/+P9eK/+eLe9eKe+vFf/vV/7N3vMD9yN132f3f472f0rvf0Xv/5x0/2el+58q7f+M1P/zSP1/PrX/n0e6/5nQ/+95u++Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pe+Vvlf6Xul7pf+Bdwu9dx/A/wAIBwAAAAElFTkSuQmCC';

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin: 0;
    }

    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #1f2937;
        font-size: 10px;
        background: #fdfdfd;
        margin: 0;
        padding: 0;
    }

    .main-container {
        padding: 20px 30px;
    }

    /* HEADER */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .header-left {
        width: 60%;
        vertical-align: middle;
    }

    .header-right {
        width: 40%;
        vertical-align: middle;
        text-align: right;
    }

    .brand-container {
        display: table;
        width: 100%;
    }

    .logo-box {
        display: table-cell;
        width: 100px;
        vertical-align: middle;
    }

    .brand-info {
        display: table-cell;
        vertical-align: middle;
        padding-left: 15px;
    }

    .brand-title {
        font-size: 32px;
        font-weight: bold;
        color: #064e3b;
        margin: 0;
        line-height: 1;
    }

    .brand-tagline {
        font-size: 13px;
        color: #059669;
        margin: 3px 0 8px 0;
        font-weight: 500;
    }

    .badges {
        margin-top: 5px;
    }

    .badge-item {
        display: inline-block;
        font-size: 8px;
        font-weight: bold;
        color: #4b5563;
        margin-right: 10px;
        text-transform: uppercase;
    }

    .badge-dot {
        width: 6px;
        height: 6px;
        background: #10b981;
        display: inline-block;
        margin-right: 4px;
        border-radius: 50%;
    }

    .report-meta {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 15px;
        display: inline-block;
        text-align: left;
    }

    .report-meta table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-meta td {
        padding: 2px 5px;
        font-size: 9px;
    }

    .meta-label {
        font-weight: bold;
        color: #374151;
        width: 100px;
    }

    .meta-value {
        color: #4b5563;
    }

    .report-title {
        font-size: 20px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    /* CARDS */
    .cards-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 12px 0;
        margin-bottom: 20px;
    }

    .card {
        width: 25%;
        background: white;
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
    }

    .card-icon {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: table-cell;
        vertical-align: middle;
        text-align: center;
        color: white;
        font-size: 16px;
        font-weight: bold;
    }

    .card-info {
        display: table-cell;
        vertical-align: middle;
        padding-left: 10px;
    }

    .card-title {
        font-size: 9px;
        font-weight: bold;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .card-number {
        font-size: 22px;
        font-weight: bold;
        color: #111827;
        margin: 0;
    }

    .card-label {
        font-size: 8px;
        color: #9ca3af;
    }

    .card-insumos { border-left: 4px solid #10b981; }
    .card-herramientas { border-left: 4px solid #3b82f6; }
    .card-epp { border-left: 4px solid #f59e0b; }
    .card-alertas { border-left: 4px solid #ef4444; }

    .bg-insumos { background: #10b981; }
    .bg-herramientas { background: #3b82f6; }
    .bg-epp { background: #f59e0b; }
    .bg-alertas { background: #ef4444; }

    /* TABLES LAYOUT */
    .sections-row {
        width: 100%;
        margin-bottom: 15px;
    }

    .section-container {
        width: 49%;
        vertical-align: top;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
    }

    .section-title {
        font-size: 11px;
        font-weight: bold;
        color: #064e3b;
        margin-bottom: 10px;
        text-transform: uppercase;
        display: block;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 5px;
    }

    .section-title-icon {
        color: #10b981;
        margin-right: 5px;
    }

    table.data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5px;
    }

    table.data-table th {
        background: #064e3b;
        color: white;
        text-align: left;
        padding: 5px 8px;
        font-weight: bold;
    }

    table.data-table td {
        padding: 4px 8px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }

    table.data-table tr:nth-child(even) td {
        background: #f9fafb;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }

    .dot-green { background: #10b981; }
    .dot-red { background: #ef4444; }
    .dot-orange { background: #f59e0b; }

    .text-green { color: #059669; font-weight: bold; }
    .text-red { color: #dc2626; font-weight: bold; }
    .text-orange { color: #d97706; font-weight: bold; }

    .section-footer {
        margin-top: 8px;
        font-size: 8px;
        color: #6b7280;
        font-weight: 500;
    }

    /* THIRD ROW */
    .bottom-grid {
        width: 100%;
        margin-bottom: 20px;
    }

    .epp-col {
        width: 65%;
        vertical-align: top;
    }

    .obs-col {
        width: 33%;
        vertical-align: top;
        padding-left: 2%;
    }

    .obs-box {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        height: 150px;
    }

    .obs-content {
        font-size: 8.5px;
        color: #4b5563;
        line-height: 1.4;
    }

    .signature-area {
        margin-top: 30px;
        text-align: center;
    }

    .signature-line {
        width: 150px;
        border-top: 1px solid #374151;
        margin: 0 auto 5px auto;
    }

    .signature-name {
        font-size: 9px;
        font-weight: bold;
        color: #111827;
    }

    /* FOOTER */
    .footer-bar {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: #064e3b;
        color: white;
        padding: 10px 30px;
    }

    .footer-content {
        width: 100%;
        border-collapse: collapse;
    }

    .footer-left {
        width: 70%;
        font-size: 9px;
    }

    .footer-right {
        width: 30%;
        text-align: right;
        font-size: 9px;
    }

    .footer-logo-text {
        font-weight: bold;
        vertical-align: middle;
    }

    .footer-logo-small {
        width: 15px;
        height: 15px;
        vertical-align: middle;
        margin-right: 5px;
    }
</style>
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="brand-container">
                    <div class="logo-box">
                        <?php if ($logoSrc): ?>
                            <img src="<?= $logoSrc ?>" style="height: 80px;">
                        <?php endif; ?>
                    </div>
                    <div class="brand-info">
                        <h1 class="brand-title">SYSTEMCOFF 360</h1>
                        <p class="brand-tagline">Todo tu inventario, bajo control.</p>
                        <div class="badges">
                            <span class="badge-item"><span class="badge-dot"></span>INVENTARIO</span>
                            <span class="badge-item"><span class="badge-dot"></span>CONTROL</span>
                            <span class="badge-item"><span class="badge-dot"></span>SEGURIDAD</span>
                            <span class="badge-item"><span class="badge-dot"></span>EFICIENCIA</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="header-right">
                <div class="report-meta">
                    <div class="report-title">Reporte de Inventario</div>
                    <table>
                        <tr>
                            <td class="meta-label">Fecha de generación:</td>
                            <td class="meta-value"><?= date('d \d\e F \d\e Y') ?></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Hora de generación:</td>
                            <td class="meta-value"><?= date('h:i A') ?></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Generado por:</td>
                            <td class="meta-value"><?= htmlspecialchars($usuarioNombre) ?></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Rol:</td>
                            <td class="meta-value"><?= htmlspecialchars($usuarioRol) ?></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- SUMMARY CARDS -->
    <table class="cards-table">
        <tr>
            <td class="card card-insumos">
                <table width="100%">
                    <tr>
                        <td class="card-icon bg-insumos">I</td>
                        <td class="card-info">
                            <div class="card-title">Insumos</div>
                            <div class="card-number"><?= count($insumos) ?></div>
                            <div class="card-label">Total registrados</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="card card-herramientas">
                <table width="100%">
                    <tr>
                        <td class="card-icon bg-herramientas">H</td>
                        <td class="card-info">
                            <div class="card-title">Herramientas</div>
                            <div class="card-number"><?= count($herramientas) ?></div>
                            <div class="card-label">Total registradas</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="card card-epp">
                <table width="100%">
                    <tr>
                        <td class="card-icon bg-epp">E</td>
                        <td class="card-info">
                            <div class="card-title">EPP</div>
                            <div class="card-number"><?= count($epps) ?></div>
                            <div class="card-label">Total registrados</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="card card-alertas">
                <table width="100%">
                    <tr>
                        <td class="card-icon bg-alertas">A</td>
                        <td class="card-info">
                            <div class="card-title">Alertas</div>
                            <div class="card-number"><?= $totalAlertas ?></div>
                            <div class="card-label">Elementos stock bajo</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- FIRST ROW TABLES -->
    <table class="sections-row" style="border-collapse: separate; border-spacing: 15px 0; margin-left: -15px; width: calc(100% + 30px);">
        <tr>
            <td class="section-container">
                <div class="section-title">INSUMOS</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="35">Imagen</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Unidad</th>
                            <th>Stock</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($insumos, 0, 8) as $idx => $i): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td style="text-align: center;">
                                    <div style="background: #f0fdf4; border-radius: 8px; padding: 2px; border: 1px solid #d1fae5; width: 25px; height: 25px; overflow: hidden;">
                                        <img src="<?= getImageUrl($i['imagen_url'] ?? '', $i['nombre'], 'agriculture') ?>" style="width: 100%; height: 100%; vertical-align: middle; object-fit: cover;">
                                    </div>
                                </td>
                                <td><strong><?= htmlspecialchars($i['nombre']) ?></strong></td>
                                <td><?= htmlspecialchars($i['tipo']) ?></td>
                                <td><?= htmlspecialchars($i['unidad']) ?></td>
                                <td><?= number_format((float)$i['stock_actual'], 2) ?></td>
                                <td>
                                    <?php if (($i['alerta_stock'] ?? '') === 'bajo'): ?>
                                        <span class="status-dot dot-red"></span><span class="text-red">Bajo</span>
                                    <?php else: ?>
                                        <span class="status-dot dot-green"></span><span class="text-green">Normal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="section-footer">
                    Total de insumos registrados: <?= count($insumos) ?> | Insumos con stock bajo: <?= $totales['insumos_bajos'] ?? 0 ?>
                </div>
            </td>
            <td class="section-container">
                <div class="section-title">HERRAMIENTAS</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th width="35">Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($herramientas, 0, 8) as $idx => $h): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td style="text-align: center;">
                                    <div style="background: #eff6ff; border-radius: 8px; padding: 2px; border: 1px solid #dbeafe; width: 25px; height: 25px; overflow: hidden;">
                                        <img src="<?= getImageUrl($h['imagen_url'] ?? '', $h['nombre'], 'tools') ?>" style="width: 100%; height: 100%; vertical-align: middle; object-fit: cover;">
                                    </div>
                                </td>
                                <td><strong><?= htmlspecialchars($h['nombre']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($h['descripcion'] ?? 'Herramienta de uso general', 0, 25)) ?>...</td>
                                <td>
                                    <?php if (($h['estado'] ?? '') === 'disponible'): ?>
                                        <span class="status-dot dot-green"></span><span class="text-green">Disponible</span>
                                    <?php else: ?>
                                        <span class="status-dot dot-red"></span><span class="text-red">En uso</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="section-footer">
                    Total de herramientas registradas: <?= count($herramientas) ?> | Disponibles: <?= $totales['herramientas_disponibles'] ?? 0 ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- SECOND ROW: EPP + OBS -->
    <table class="bottom-grid">
        <tr>
            <td class="epp-col">
                <div class="section-container" style="width: 100%;">
                    <div class="section-title">ELEMENTOS DE PROTECCIÓN PERSONAL (EPP)</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="20">#</th>
                                <th width="35">Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Talla</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($epps, 0, 6) as $idx => $e): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td style="text-align: center;">
                                        <div style="background: #fffbeb; border-radius: 8px; padding: 2px; border: 1px solid #fef3c7; width: 25px; height: 25px; overflow: hidden;">
                                            <img src="<?= getImageUrl($e['imagen_url'] ?? '', $e['nombre'], 'security') ?>" style="width: 100%; height: 100%; vertical-align: middle; object-fit: cover;">
                                        </div>
                                    </td>
                                    <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
                                    <td><?= htmlspecialchars(substr($e['descripcion'] ?? 'Protección personal', 0, 30)) ?></td>
                                    <td><?= htmlspecialchars($e['talla'] ?? 'N/A') ?></td>
                                    <td><?= (int)$e['cantidad_total'] ?></td>
                                    <td>
                                        <?php if (($e['alerta_stock'] ?? '') === 'bajo'): ?>
                                            <span class="status-dot dot-red"></span><span class="text-red">Bajo</span>
                                        <?php else: ?>
                                            <span class="status-dot dot-green"></span><span class="text-green">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="section-footer">
                        Total de EPP registrados: <?= count($epps) ?> | Elementos con stock bajo: <?= $totales['epp_bajo'] ?? 0 ?>
                    </div>
                </div>
            </td>
            <td class="obs-col">
                <div class="obs-box">
                    <div class="section-title">OBSERVACIONES</div>
                    <div class="obs-content">
                        <?php if ($totalAlertas > 0): ?>
                            Se recomienda revisar y reabastecer los elementos con stock bajo para garantizar la continuidad de las operaciones y la seguridad del personal.
                        <?php else: ?>
                            El inventario se encuentra en niveles óptimos. No se requieren acciones inmediatas de reabastecimiento.
                        <?php endif; ?>
                    </div>
                    <div class="signature-area">
                        <div class="signature-line"></div>
                        <div class="signature-name"><?= htmlspecialchars($usuarioRol) ?></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- FOOTER -->
<div class="footer-bar">
    <table class="footer-content">
        <tr>
            <td class="footer-left">
                <?php if ($icoSrc): ?>
                    <img src="<?= $icoSrc ?>" class="footer-logo-small">
                <?php endif; ?>
                <span class="footer-logo-text">SystemCOFF 360</span> — Gestión inteligente para una finca más productiva y segura.
            </td>
            <td class="footer-right">
                www.systemcoff360.com
            </td>
        </tr>
    </table>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("reporte_inventario_" . date('Y-m-d') . ".pdf", [
    "Attachment" => true
]);

exit;