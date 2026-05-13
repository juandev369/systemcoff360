<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Inventario.php';

class InventarioController
{
    private $inventarioModel;

    public function __construct()
    {
        if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
            header('Location: ../views/usuarios/login.php');
            exit;
        }

        $database = new Database();
        $db = $database->conectar();
        $this->inventarioModel = new Inventario($db);
    }

    private function volverInventario()
    {
        header('Location: ../views/dashboard/inventario.php');
        exit;
    }

    public function handleRequest()
    {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->volverInventario();
        }

        switch ($accion) {
            case 'crearInsumo':
                $this->crearInsumo();
                break;
            case 'movimientoInsumo':
                $this->movimientoInsumo();
                break;
            case 'crearHerramienta':
                $this->crearHerramienta();
                break;
            case 'entregarHerramienta':
                $this->entregarHerramienta();
                break;
            case 'devolverHerramienta':
                $this->devolverHerramienta();
                break;
            case 'crearEpp':
                $this->crearEpp();
                break;
            case 'entregarEpp':
                $this->entregarEpp();
                break;
            case 'devolverEpp':
                $this->devolverEpp();
                break;
            default:
                $this->volverInventario();
                break;
        }
    }

    private function crearInsumo()
    {
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
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->crearInsumo([
            'nombre' => $nombre,
            'tipo' => $tipo,
            'unidad' => $unidad,
            'stock_actual' => $stock_actual,
            'stock_minimo' => $stock_minimo,
            'precio_unidad' => $precio_unidad,
            'imagen_url' => trim($_POST['imagen_url'] ?? '')
        ]);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? '¡Insumo creado!' : 'Error',
            'text' => $resultado === true ? 'El insumo fue registrado correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo crear el insumo.')
        ];

        $this->volverInventario();
    }

    private function movimientoInsumo()
    {
        $id_insumo = (int)($_POST['id_insumo'] ?? 0);
        $tipo_movimiento = $_POST['tipo_movimiento'] ?? '';
        $cantidad = trim($_POST['cantidad'] ?? '0');

        if ($id_insumo <= 0 || !in_array($tipo_movimiento, ['entrada', 'salida'], true)) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Datos inválidos',
                'text' => 'Selecciona un insumo y un tipo de movimiento válido.'
            ];
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->actualizarStockInsumo($id_insumo, $tipo_movimiento, $cantidad);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? 'Stock actualizado' : 'Error',
            'text' => $resultado === true ? 'El stock del insumo fue actualizado correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo actualizar el stock.')
        ];

        $this->volverInventario();
    }

    private function crearHerramienta()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha_registro = $_POST['fecha_registro'] ?? date('Y-m-d');

        if ($nombre === '') {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Campo obligatorio',
                'text' => 'El nombre de la herramienta es obligatorio.'
            ];
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->crearHerramienta([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha_registro' => $fecha_registro,
            'imagen_url' => trim($_POST['imagen_url'] ?? '')
        ]);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? '¡Herramienta creada!' : 'Error',
            'text' => $resultado === true ? 'La herramienta fue registrada correctamente.' : (is_string($resultado) ? $resultado : 'No se pudo crear la herramienta.')
        ];

        $this->volverInventario();
    }

    private function entregarHerramienta()
    {
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
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->entregarHerramienta([
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

        $this->volverInventario();
    }

    private function devolverHerramienta()
    {
        $id_entrega = (int)($_POST['id_entrega'] ?? 0);

        if ($id_entrega <= 0) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'ID inválido',
                'text' => 'No se pudo identificar la entrega.'
            ];
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->devolverHerramienta($id_entrega);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? 'Herramienta devuelta' : 'Error',
            'text' => $resultado === true ? 'La herramienta quedó disponible nuevamente.' : (is_string($resultado) ? $resultado : 'No se pudo devolver la herramienta.')
        ];

        $this->volverInventario();
    }

    private function crearEpp()
    {
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
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->crearEpp([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'cantidad_total' => $cantidad_total,
            'stock_disponible' => $stock_disponible,
            'talla' => $talla,
            'imagen_url' => trim($_POST['imagen_url'] ?? '')
        ]);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? '¡EPP creado!' : 'Error',
            'text' => $resultado === true ? 'El elemento de protección fue registrado.' : (is_string($resultado) ? $resultado : 'No se pudo crear el EPP.')
        ];

        $this->volverInventario();
    }

    private function entregarEpp()
    {
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
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->entregarEpp([
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

        $this->volverInventario();
    }

    private function devolverEpp()
    {
        $id_entrega = (int)($_POST['id_entrega'] ?? 0);

        if ($id_entrega <= 0) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'ID inválido',
                'text' => 'No se pudo identificar la entrega.'
            ];
            $this->volverInventario();
        }

        $resultado = $this->inventarioModel->devolverEpp($id_entrega);

        $_SESSION['alert'] = [
            'icon' => $resultado === true ? 'success' : 'error',
            'title' => $resultado === true ? 'EPP devuelto' : 'Error',
            'text' => $resultado === true ? 'El EPP fue devuelto y el stock actualizado.' : (is_string($resultado) ? $resultado : 'No se pudo devolver el EPP.')
        ];

        $this->volverInventario();
    }
}

// Enrutamiento manual
$controller = new InventarioController();
$controller->handleRequest();