<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Inventario.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../views/usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();
$inventarioModel = new Inventario($db);

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

function volverInventario()
{
    header('Location: ../views/dashboard/inventario.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    volverInventario();
}

if ($accion === 'crearInsumo') {
    $nombre = trim($_POST['nombre'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $unidad = trim($_POST['unidad'] ?? '');
    $stock_actual = trim($_POST['stock_actual'] ?? '0');
    $stock_minimo = trim($_POST['stock_minimo'] ?? '0');
    $precio_unidad = trim($_POST['precio_unidad'] ?? '0');

    if ($nombre === '' || $tipo === '' || $unidad === '') {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Campos incompletos',
            'text' => 'Nombre, tipo y unidad son obligatorios.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->crearInsumo([
        'nombre' => $nombre,
        'tipo' => $tipo,
        'unidad' => $unidad,
        'stock_actual' => $stock_actual,
        'stock_minimo' => $stock_minimo,
        'precio_unidad' => $precio_unidad
    ]);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? '¡Insumo creado!' : 'Error',
        'text' => $resultado === true ? 'El insumo fue registrado correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo crear el insumo.')
    ];

    volverInventario();
}

if ($accion === 'movimientoInsumo') {
    $id_insumo = (int)($_POST['id_insumo'] ?? 0);
    $tipo_movimiento = $_POST['tipo_movimiento'] ?? '';
    $cantidad = trim($_POST['cantidad'] ?? '0');

    if ($id_insumo <= 0 || !in_array($tipo_movimiento, ['entrada', 'salida'], true)) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Datos inválidos',
            'text' => 'Selecciona un insumo y un tipo de movimiento válido.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->actualizarStockInsumo($id_insumo, $tipo_movimiento, $cantidad);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? 'Stock actualizado' : 'Error',
        'text' => $resultado === true ? 'El stock del insumo fue actualizado correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo actualizar el stock.')
    ];

    volverInventario();
}

if ($accion === 'crearHerramienta') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fecha_registro = $_POST['fecha_registro'] ?? date('Y-m-d');

    if ($nombre === '') {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Campo obligatorio',
            'text' => 'El nombre de la herramienta es obligatorio.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->crearHerramienta([
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'fecha_registro' => $fecha_registro
    ]);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? '¡Herramienta creada!' : 'Error',
        'text' => $resultado === true ? 'La herramienta fue registrada correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo crear la herramienta.')
    ];

    volverInventario();
}

if ($accion === 'entregarHerramienta') {
    $id_herramienta = (int)($_POST['id_herramienta'] ?? 0);
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $fecha_entrega = $_POST['fecha_entrega'] ?? date('Y-m-d');
    $estado_herramienta = trim($_POST['estado_herramienta'] ?? 'bueno');
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($id_herramienta <= 0 || $id_usuario <= 0) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Campos incompletos',
            'text' => 'Selecciona herramienta y trabajador.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->entregarHerramienta([
        'id_herramienta' => $id_herramienta,
        'id_usuario' => $id_usuario,
        'fecha_entrega' => $fecha_entrega,
        'estado_herramienta' => $estado_herramienta,
        'observaciones' => $observaciones
    ]);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? '¡Herramienta entregada!' : 'Error',
        'text' => $resultado === true ? 'La herramienta fue asignada correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo entregar la herramienta.')
    ];

    volverInventario();
}

if ($accion === 'devolverHerramienta') {
    $id_entrega = (int)($_POST['id_entrega'] ?? 0);

    if ($id_entrega <= 0) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'ID inválido',
            'text' => 'No se pudo identificar la entrega.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->devolverHerramienta($id_entrega);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? 'Herramienta devuelta' : 'Error',
        'text' => $resultado === true ? 'La herramienta quedó disponible nuevamente.' : (is_string($resultado) ? $resultado : 'No se pudo devolver la herramienta.')
    ];

    volverInventario();
}

if ($accion === 'crearEpp') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $cantidad_total = (int)($_POST['cantidad_total'] ?? 0);
    $stock_disponible = (int)($_POST['stock_disponible'] ?? 0);
    $talla = trim($_POST['talla'] ?? '');

    if ($nombre === '' || $cantidad_total < 0 || $stock_disponible < 0) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Datos inválidos',
            'text' => 'Completa correctamente los datos del EPP.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->crearEpp([
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'cantidad_total' => $cantidad_total,
        'stock_disponible' => $stock_disponible,
        'talla' => $talla
    ]);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? '¡EPP creado!' : 'Error',
        'text' => $resultado === true ? 'El elemento de protección fue registrado.' : (is_string($resultado) ? $resultado : 'No se pudo crear el EPP.')
    ];

    volverInventario();
}

if ($accion === 'entregarEpp') {
    $id_epp = (int)($_POST['id_epp'] ?? 0);
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $fecha_entrega = $_POST['fecha_entrega'] ?? date('Y-m-d');
    $estado_elemento = trim($_POST['estado_elemento'] ?? 'bueno');
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($id_epp <= 0 || $id_usuario <= 0) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Campos incompletos',
            'text' => 'Selecciona EPP y trabajador.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->entregarEpp([
        'id_epp' => $id_epp,
        'id_usuario' => $id_usuario,
        'fecha_entrega' => $fecha_entrega,
        'estado_elemento' => $estado_elemento,
        'observaciones' => $observaciones
    ]);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? '¡EPP entregado!' : 'Error',
        'text' => $resultado === true ? 'El EPP fue entregado correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo entregar el EPP.')
    ];

    volverInventario();
}

if ($accion === 'devolverEpp') {
    $id_entrega = (int)($_POST['id_entrega'] ?? 0);

    if ($id_entrega <= 0) {
        $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'ID inválido',
            'text' => 'No se pudo identificar la entrega.'
        ];
        volverInventario();
    }

    $resultado = $inventarioModel->devolverEpp($id_entrega);

    $_SESSION['alert'] = [
        'icon' => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? 'EPP devuelto' : 'Error',
        'text' => $resultado === true ? 'El EPP fue devuelto y el stock actualizado.' : (is_string($resultado) ? $resultado : 'No se pudo devolver el EPP.')
    ];

    volverInventario();
}

volverInventario();