<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->conectar();
        $this->usuarioModel = new Usuario($db);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/usuarios/login.php');
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Campos incompletos',
                'text'  => 'Por favor ingresa tu correo y contraseña.'
            ];
            header('Location: ../views/usuarios/login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Correo inválido',
                'text'  => 'El formato del correo electrónico no es válido.'
            ];
            header('Location: ../views/usuarios/login.php');
            exit;
        }

        try {
            $usuario = $this->usuarioModel->obtenerPorEmail($email);

            if (!$usuario || !password_verify($password, $usuario['contrasena'])) {
                sleep(1);
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Credenciales incorrectas',
                    'text'  => 'El correo o la contraseña no son correctos.'
                ];
                header('Location: ../views/usuarios/login.php');
                exit;
            }

            if (strtolower(trim($usuario['estado'])) !== 'activo') {
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Cuenta desactivada',
                    'text'  => 'Tu cuenta está desactivada. Contacta al administrador.'
                ];
                header('Location: ../views/usuarios/login.php');
                exit;
            }

            session_regenerate_id(true);

            $_SESSION['usuario'] = [
                'id'         => $usuario['id_usuario'],
                'id_usuario' => $usuario['id_usuario'],
                'id_rol'     => $usuario['id_rol'],
                'nombre'     => $usuario['nombre'],
                'nombres'    => $usuario['nombre'],
                'apellidos'  => '',
                'email'      => $usuario['correo'],
                'correo'     => $usuario['correo'],
                'rol'        => strtolower(trim($usuario['rol']))
            ];

            $this->usuarioModel->actualizarUltimoAcceso($usuario['id_usuario']);

            if (!empty($_POST['remember'])) {
                setcookie('sc360_uid', $usuario['id_usuario'], time() + (7 * 86400), '/', '', false, true);
            }

            $_SESSION['alert'] = [
                'icon'  => 'success',
                'title' => '¡Bienvenido!',
                'text'  => 'Hola ' . $usuario['nombre'] . ', acceso exitoso.'
            ];

            $this->redirigirPorRol($usuario['rol']);

        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ../views/usuarios/login.php');
        exit;
    }

    private function redirigirPorRol($rol)
    {
        switch (strtolower(trim($rol))) {
            case 'administrador':
            case 'admin':
                header('Location: ../views/dashboard/admin.php');
                break;
            case 'trabajador':
                header('Location: ../views/dashboard/trabajador.php');
                break;
            default:
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Rol no válido',
                    'text'  => 'Tu usuario no tiene un rol válido asignado.'
                ];
                header('Location: ../views/usuarios/login.php');
                break;
        }
        exit;
    }

    private function handleError($e)
    {
        echo "<h2>Error en AuthController.php</h2>";
        echo "<b>Mensaje:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<b>Archivo:</b> " . htmlspecialchars($e->getFile()) . "<br>";
        echo "<b>Línea:</b> " . htmlspecialchars($e->getLine()) . "<br>";
        exit;
    }
}

// Enrutamiento manual
$controller = new AuthController();
if (isset($_POST['logout'])) {
    $controller->logout();
} else {
    $controller->login();
}