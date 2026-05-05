<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
    header('Location: ../views/usuarios/login.php');
    exit;
}

class AdminUsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->conectar();
        $this->usuarioModel = new Usuario($db);
    }

    private function redirigir()
    {
        $volver = $_POST['volver'] ?? $_GET['volver'] ?? '';

        if ($volver === 'usuarios') {
            header('Location: ../views/dashboard/usuarios.php');
            exit;
        }

        header('Location: ../views/dashboard/admin.php');
        exit;
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/dashboard/admin.php');
            exit;
        }

        $nombres = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';
        $rol = strtolower(trim($_POST['rol'] ?? 'trabajador'));

        if ($nombres === '' || $apellidos === '' || $email === '' || $password === '') {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Campos incompletos',
                'text'  => 'Completa todos los campos obligatorios.'
            ];
            header('Location: ../views/dashboard/usuario_crear.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Correo inválido',
                'text'  => 'El formato del correo electrónico no es válido.'
            ];
            header('Location: ../views/dashboard/usuario_crear.php');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Contraseña muy corta',
                'text'  => 'La contraseña debe tener al menos 8 caracteres.'
            ];
            header('Location: ../views/dashboard/usuario_crear.php');
            exit;
        }

        if ($password !== $confirmar_password) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Contraseñas no coinciden',
                'text'  => 'La contraseña y su confirmación deben ser iguales.'
            ];
            header('Location: ../views/dashboard/usuario_crear.php');
            exit;
        }

        if (!in_array($rol, ['trabajador', 'administrador', 'admin'], true)) {
            $rol = 'trabajador';
        }

        if ($this->usuarioModel->existeCorreo($email)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Correo ya registrado',
                'text'  => 'Ya existe un usuario con ese correo electrónico.'
            ];
            header('Location: ../views/dashboard/usuario_crear.php');
            exit;
        }

        $creado = $this->usuarioModel->registrar([
            'nombres'   => $nombres,
            'apellidos' => $apellidos,
            'email'     => $email,
            'telefono'  => $telefono,
            'password'  => $password,
            'rol'       => $rol
        ]);

        if ($creado === true) {
            $_SESSION['alert'] = [
                'icon'  => 'success',
                'title' => '¡Trabajador creado!',
                'text'  => $nombres . ' fue registrado exitosamente en el sistema.'
            ];
            header('Location: ../views/dashboard/usuarios.php');
            exit;
        }

        $_SESSION['alert'] = [
            'icon'  => 'error',
            'title' => 'Error al crear',
            'text'  => is_string($creado) ? $creado : 'Ocurrió un error al guardar el usuario.'
        ];
        header('Location: ../views/dashboard/usuario_crear.php');
        exit;
    }

    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir();
        }

        $id = (int)($_POST['id'] ?? 0);
        $estado = strtolower(trim($_POST['estado'] ?? ''));

        if ($id <= 0) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'ID inválido',
                'text'  => 'No se pudo identificar el usuario.'
            ];
            $this->redirigir();
        }

        if ($id === (int)($_SESSION['usuario']['id'] ?? 0)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Acción no permitida',
                'text'  => 'No puedes cambiar el estado de tu propia cuenta.'
            ];
            $this->redirigir();
        }

        if (!in_array($estado, ['activo', 'inactivo'], true)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Estado inválido',
                'text'  => 'El estado enviado no es válido.'
            ];
            $this->redirigir();
        }

        $ok = $this->usuarioModel->cambiarEstado($id, $estado);

        $_SESSION['alert'] = [
            'icon'  => $ok === true ? 'success' : 'error',
            'title' => $ok === true
                ? ($estado === 'activo' ? '¡Usuario activado!' : '¡Usuario desactivado!')
                : 'Error al cambiar estado',
            'text'  => $ok === true
                ? 'El estado del usuario fue actualizado correctamente.'
                : (is_string($ok) ? $ok : 'No se pudo cambiar el estado.')
        ];

        $this->redirigir();
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir();
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'ID inválido',
                'text'  => 'No se pudo identificar el usuario a eliminar.'
            ];
            $this->redirigir();
        }

        if ($id === (int)($_SESSION['usuario']['id'] ?? 0)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Acción no permitida',
                'text'  => 'No puedes eliminar tu propia cuenta.'
            ];
            $this->redirigir();
        }

        $ok = $this->usuarioModel->eliminar($id);

        $_SESSION['alert'] = [
            'icon'  => $ok === true ? 'success' : 'error',
            'title' => $ok === true ? '¡Usuario eliminado!' : 'Error al eliminar',
            'text'  => $ok === true
                ? 'El usuario fue eliminado del sistema.'
                : (is_string($ok) ? $ok : 'No se pudo eliminar el usuario.')
        ];

        $this->redirigir();
    }
}

$controller = new AdminUsuarioController();
$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'crear';

switch ($accion) {
    case 'crear':
        $controller->crear();
        break;

    case 'cambiarEstado':
        $controller->cambiarEstado();
        break;

    case 'eliminar':
        $controller->eliminar();
        break;

    default:
        header('Location: ../views/dashboard/admin.php');
        exit;
}