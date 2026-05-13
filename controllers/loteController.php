<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Lote.php';

class LoteController
{
    private $loteModel;

    public function __construct()
    {
        if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
            header('Location: ../views/usuarios/login.php');
            exit;
        }

        $database = new Database();
        $db = $database->conectar();
        $this->loteModel = new Lote($db);
    }

    private function redirigirLotes()
    {
        header('Location: ../views/dashboard/lotes.php');
        exit;
    }

    private function redirigirDetalle($id_lote)
    {
        header('Location: ../views/dashboard/lote_detalle.php?id=' . $id_lote);
        exit;
    }

    public function handleRequest()
    {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirLotes();
        }

        switch ($accion) {
            case 'crear':
                $this->crear();
                break;
            case 'actualizar':
                $this->actualizar();
                break;
            case 'cambiarEstado':
                $this->cambiarEstado();
                break;
            case 'registrarActividad':
                $this->registrarActividad();
                break;
            default:
                $this->redirigirLotes();
                break;
        }
    }

    private function crear()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $tipo_plantacion = trim($_POST['tipo_plantacion'] ?? '');
        $area_hectareas = trim($_POST['area_hectareas'] ?? '');
        $fecha_registro = $_POST['fecha_registro'] ?? date('Y-m-d');

        if ($nombre === '' || $tipo_plantacion === '' || $fecha_registro === '') {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Campos incompletos',
                'text' => 'Nombre, tipo de plantación y fecha son obligatorios.'
            ];
            header('Location: ../views/dashboard/lote_form.php');
            exit;
        }

        $resultado = $this->loteModel->registrar([
            'nombre' => $nombre,
            'ubicacion' => $ubicacion,
            'tipo_plantacion' => $tipo_plantacion,
            'area_hectareas' => $area_hectareas,
            'fecha_registro' => $fecha_registro
        ]);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? '¡Lote creado!' : 'Error al crear',
            'text' => $resultado === true ? 'El lote fue registrado correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo registrar el lote.')
        ];

        $this->redirigirLotes();
    }

    private function actualizar()
    {
        $id_lote = (int)($_POST['id_lote'] ?? 0);

        if ($id_lote <= 0) {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'ID inválido',
                'text' => 'No se pudo identificar el lote.'
            ];
            $this->redirigirLotes();
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $tipo_plantacion = trim($_POST['tipo_plantacion'] ?? '');
        $area_hectareas = trim($_POST['area_hectareas'] ?? '');
        $estado = trim($_POST['estado'] ?? 'activo');

        if ($nombre === '' || $tipo_plantacion === '') {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Campos incompletos',
                'text' => 'Nombre y tipo de plantación son obligatorios.'
            ];
            header('Location: ../views/dashboard/lote_form.php?id=' . $id_lote);
            exit;
        }

        $resultado = $this->loteModel->actualizar($id_lote, [
            'nombre' => $nombre,
            'ubicacion' => $ubicacion,
            'tipo_plantacion' => $tipo_plantacion,
            'area_hectareas' => $area_hectareas,
            'estado' => $estado
        ]);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? '¡Lote actualizado!' : 'Error al actualizar',
            'text' => $resultado === true ? 'La información del lote fue actualizada.' : (is_string($resultado) ? $resultado : 'No se pudo actualizar el lote.')
        ];

        $this->redirigirLotes();
    }

    private function cambiarEstado()
    {
        $id_lote = (int)($_POST['id_lote'] ?? 0);
        $estado = $_POST['estado'] ?? 'activo';

        if ($id_lote <= 0) {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'ID inválido',
                'text' => 'No se pudo identificar the lote.'
            ];
            $this->redirigirLotes();
        }

        $nuevoEstado = $estado === 'activo' ? 'inactivo' : 'activo';
        $resultado = $this->loteModel->cambiarEstado($id_lote, $nuevoEstado);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? 'Estado actualizado' : 'Error',
            'text' => $resultado === true ? 'El estado del lote fue actualizado correctamente.' : 'No se pudo cambiar el estado.'
        ];

        $this->redirigirLotes();
    }

    private function registrarActividad()
    {
        $id_lote = (int)($_POST['id_lote'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? '');
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $id_responsable = (int)($_POST['id_responsable'] ?? ($_SESSION['usuario']['id'] ?? 0));
        $descripcion = trim($_POST['descripcion'] ?? '');
        $costo = trim($_POST['costo'] ?? '0');
        $proxima_fecha = trim($_POST['proxima_fecha'] ?? '');

        if ($id_lote <= 0 || $tipo === '' || $fecha === '' || $id_responsable <= 0) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Campos incompletos',
                'text' => 'Tipo, fecha y responsable son obligatorios.'
            ];
            $this->redirigirDetalle($id_lote);
        }

        $resultado = $this->loteModel->registrarActividad([
            'id_lote' => $id_lote,
            'id_responsable' => $id_responsable,
            'tipo' => $tipo,
            'fecha' => $fecha,
            'descripcion' => $descripcion,
            'costo' => $costo,
            'proxima_fecha' => $proxima_fecha
        ]);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? '¡Actividad registrada!' : 'Error',
            'text' => $resultado === true ? 'La actividad del lote fue guardada correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo registrar la actividad.')
        ];

        $this->redirigirDetalle($id_lote);
    }
}

// Enrutamiento
$controller = new LoteController();
$controller->handleRequest();