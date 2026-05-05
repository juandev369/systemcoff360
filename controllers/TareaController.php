<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Tarea.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../views/usuarios/login.php');
    exit;
}

$database = new Database();
$db = $database->conectar();
$tareaModel = new Tarea($db);

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

function volverTareas()
{
    header('Location: ../views/dashboard/tareas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    volverTareas();
}

if ($accion === 'crear') {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = strtolower(trim($_POST['prioridad'] ?? 'media'));
    $fecha_limite = trim($_POST['fecha_limite'] ?? '');
    $trabajadores = $_POST['trabajadores'] ?? [];

    if ($descripcion === '') {
        $_SESSION['alert'] = [
            'icon'  => 'warning',
            'title' => 'Descripción obligatoria',
            'text'  => 'Debes escribir la descripción de la tarea.'
        ];

        volverTareas();
    }

    if (!in_array($prioridad, ['baja', 'media', 'alta'], true)) {
        $prioridad = 'media';
    }

    if (empty($trabajadores)) {
        $_SESSION['alert'] = [
            'icon'  => 'warning',
            'title' => 'Sin trabajador asignado',
            'text'  => 'Debes seleccionar al menos un trabajador.'
        ];

        volverTareas();
    }

    $resultado = $tareaModel->crear([
        'descripcion'  => $descripcion,
        'prioridad'    => $prioridad,
        'fecha_limite' => $fecha_limite,
        'trabajadores' => $trabajadores
    ]);

    $_SESSION['alert'] = [
        'icon'  => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? '¡Tarea asignada!' : 'Error al asignar',
        'text'  => $resultado === true
            ? 'La tarea fue creada y asignada correctamente.'
            : (is_string($resultado) ? $resultado : 'No se pudo crear la tarea.')
    ];

    volverTareas();
}

if ($accion === 'cambiarEstadoTarea') {
    $id_tarea = (int)($_POST['id_tarea'] ?? 0);
    $estado = strtolower(trim($_POST['estado'] ?? ''));

    if ($id_tarea <= 0 || !in_array($estado, ['pendiente', 'en_proceso', 'completada', 'cancelada'], true)) {
        $_SESSION['alert'] = [
            'icon'  => 'warning',
            'title' => 'Datos inválidos',
            'text'  => 'No se pudo actualizar el estado de la tarea.'
        ];

        volverTareas();
    }

    $resultado = $tareaModel->cambiarEstadoTarea($id_tarea, $estado);

    $_SESSION['alert'] = [
        'icon'  => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? 'Estado actualizado' : 'Error',
        'text'  => $resultado === true
            ? 'El estado de la tarea fue actualizado.'
            : (is_string($resultado) ? $resultado : 'No se pudo actualizar el estado.')
    ];

    volverTareas();
}

if ($accion === 'eliminar') {
    $id_tarea = (int)($_POST['id_tarea'] ?? 0);

    if ($id_tarea <= 0) {
        $_SESSION['alert'] = [
            'icon'  => 'warning',
            'title' => 'ID inválido',
            'text'  => 'No se pudo identificar la tarea.'
        ];

        volverTareas();
    }

    $resultado = $tareaModel->eliminar($id_tarea);

    $_SESSION['alert'] = [
        'icon'  => $resultado === true ? 'success' : 'error',
        'title' => $resultado === true ? 'Tarea eliminada' : 'Error al eliminar',
        'text'  => $resultado === true
            ? 'La tarea fue eliminada correctamente.'
            : (is_string($resultado) ? $resultado : 'No se pudo eliminar la tarea.')
    ];

    volverTareas();
}

volverTareas();