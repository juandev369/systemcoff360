<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/usuarios/registre.php');
    exit;
}

$nombres = trim($_POST['nombres'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';
$confirmar_password = $_POST['confirmar_password'] ?? '';
$rol = strtolower(trim($_POST['rol'] ?? 'trabajador'));

if ($nombres === '' || $apellidos === '' || $email === '' || $telefono === '' || $password === '') {
    $_SESSION['alert'] = [
        'icon'  => 'warning',
        'title' => 'Campos incompletos',
        'text'  => 'Por favor completa todos los campos obligatorios.'
    ];

    header('Location: ../views/usuarios/registre.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['alert'] = [
        'icon'  => 'warning',
        'title' => 'Correo inválido',
        'text'  => 'El formato del correo electrónico no es válido.'
    ];

    header('Location: ../views/usuarios/registre.php');
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['alert'] = [
        'icon'  => 'warning',
        'title' => 'Contraseña muy corta',
        'text'  => 'La contraseña debe tener al menos 8 caracteres.'
    ];

    header('Location: ../views/usuarios/registre.php');
    exit;
}

if ($password !== $confirmar_password) {
    $_SESSION['alert'] = [
        'icon'  => 'warning',
        'title' => 'Las contraseñas no coinciden',
        'text'  => 'La contraseña y su confirmación deben ser iguales.'
    ];

    header('Location: ../views/usuarios/registre.php');
    exit;
}

if (!in_array($rol, ['administrador', 'trabajador', 'admin'], true)) {
    $rol = 'trabajador';
}

try {
    $database = new Database();
    $db = $database->conectar();

    $usuarioModel = new Usuario($db);

    if ($usuarioModel->existeCorreo($email)) {
        $_SESSION['alert'] = [
            'icon'  => 'warning',
            'title' => 'Correo ya registrado',
            'text'  => 'Ya existe una cuenta con ese correo.'
        ];

        header('Location: ../views/usuarios/registre.php');
        exit;
    }

    $resultado = $usuarioModel->registrar([
        'nombres'   => $nombres,
        'apellidos' => $apellidos,
        'email'     => $email,
        'telefono'  => $telefono,
        'password'  => $password,
        'rol'       => $rol
    ]);

    if ($resultado !== true) {
        $_SESSION['alert'] = [
            'icon'  => 'error',
            'title' => 'Error al registrar',
            'text'  => is_string($resultado) ? $resultado : 'Ocurrió un error al crear la cuenta.'
        ];

        header('Location: ../views/usuarios/registre.php');
        exit;
    }

    $_SESSION['alert'] = [
        'icon'     => 'success',
        'title'    => '¡Cuenta creada!',
        'text'     => 'Tu cuenta fue creada exitosamente. Ahora puedes iniciar sesión.',
        'redirect' => 'login.php'
    ];

    header('Location: ../views/usuarios/registre.php');
    exit;

} catch (Throwable $e) {
    echo "<h2>Error en UsuarioController.php</h2>";
    echo "<b>Mensaje:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<b>Archivo:</b> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<b>Línea:</b> " . htmlspecialchars($e->getLine()) . "<br>";
    exit;
}