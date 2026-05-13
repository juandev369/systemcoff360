<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Tarea.php';

class TareaController
{
    private $tareaModel;

    public function __construct()
    {
        if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
            header('Location: ../views/usuarios/login.php');
            exit;
        }

        $database = new Database();
        $db = $database->conectar();
        $this->tareaModel = new Tarea($db);
    }

    private function volverTareas()
    {
        header('Location: ../views/dashboard/tareas.php');
        exit;
    }

    public function handleRequest()
    {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->volverTareas();
        }

        switch ($accion) {
            case 'crear':
                $this->crear();
                break;
            case 'cambiarEstadoTarea':
                $this->cambiarEstadoTarea();
                break;
            case 'eliminar':
                $this->eliminar();
                break;
            default:
                $this->volverTareas();
                break;
        }
    }

    private function crear()
    {
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
            $this->volverTareas();
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
            $this->volverTareas();
        }

        $resultado = $this->tareaModel->crear([
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

        $this->volverTareas();
    }

    private function cambiarEstadoTarea()
    {
        $id_tarea = (int)($_POST['id_tarea'] ?? 0);
        $estado = strtolower(trim($_POST['estado'] ?? ''));

        if ($id_tarea <= 0 || !in_array($estado, ['pendiente', 'en_proceso', 'completada', 'cancelada'], true)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Datos inválidos',
                'text'  => 'No se pudo actualizar el estado de la tarea.'
            ];
            $this->volverTareas();
        }

        $resultado = $this->tareaModel->cambiarEstadoTarea($id_tarea, $estado);

        $_SESSION['alert'] = [
            'icon'  => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? 'Estado actualizado' : 'Error',
            'text'  => $resultado === true
                ? 'El estado de la tarea fue actualizado.'
                : (is_string($resultado) ? $resultado : 'No se pudo actualizar el estado.')
        ];

        $this->volverTareas();
    }

    private function eliminar()
    {
        $id_tarea = (int)($_POST['id_tarea'] ?? 0);

        if ($id_tarea <= 0) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'ID inválido',
                'text'  => 'No se pudo identificar la tarea.'
            ];
            $this->volverTareas();
        }

        $resultado = $this->tareaModel->eliminar($id_tarea);

        $_SESSION['alert'] = [
            'icon'  => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? 'Tarea eliminada' : 'Error al eliminar',
            'text'  => $resultado === true
                ? 'La tarea fue eliminada correctamente.'
                : (is_string($resultado) ? $resultado : 'No se pudo eliminar la tarea.')
        ];

        $this->volverTareas();
    }
}

// Enrutamiento
$controller = new TareaController();
$controller->handleRequest();