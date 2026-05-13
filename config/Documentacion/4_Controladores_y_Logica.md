# Controladores y Lógica

## Funcionalidad
Los controladores interceptan las peticiones de los formularios. La lógica generalmente incluye:
1. Validar el método HTTP (`$_SERVER['REQUEST_METHOD'] == 'POST'`).
2. Leer los datos enviados (`$_POST`).
3. Instanciar el modelo correspondiente.
4. Ejecutar la acción requerida.
5. Generar una alerta (`SweetAlert`) guardada en `$_SESSION['alert']`.
6. Redirigir (`header('Location: ...')`) a la vista correspondiente.

## Controladores Principales
- **`AuthController.php`**: Inicia y destruye sesiones. Maneja el login verificando el rol para redirigir al panel correcto (`admin.php` o `trabajador.php`).
- **`UsuarioController.php`**: Registro y gestión de usuarios desde el administrador.
- **`InventarioController.php`**: Determina la `accion` (crearInsumo, movimientoInsumo, etc.) usando un switch o if/else y llama al método apropiado del modelo `Inventario`.
