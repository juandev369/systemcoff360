# Flujos y Casos de Uso

## 1. Autenticación (Login)
- Usuario ingresa credenciales en `login.php`.
- `AuthController` verifica el hash de la contraseña en DB.
- Si es válido, se crea la sesión `$_SESSION['usuario']` con sus datos.
- Se redirige: Si es administrador -> `dashboard/admin.php`. Si es trabajador -> `dashboard/trabajador.php`.

## 2. Gestión de Inventario
- El administrador accede a `inventario.php`.
- Se muestran catálogos y gráficas con alertas.
- Puede registrar una entrada o salida. El formulario hace POST a `InventarioController.php`.
- El controlador llama a `actualizarStockInsumo()` en el modelo.
- Se actualiza la DB y redirige de vuelta con alerta de éxito.

## 3. Asistente Virtual (Coffy Pro)
- Presente en la esquina inferior derecha mediante `layouts/assistant_widget.php`.
- Provee un chat dinámico capaz de recuperar información y generar reportes.
