# Arquitectura del Proyecto

## Resumen
SystemCOFF 360 es una plataforma web desarrollada en PHP orientada a la gestión inteligente de fincas cafeteras. Utiliza una arquitectura basada en el patrón Modelo-Vista-Controlador (MVC).

## Estructura de Directorios
- `config/`: Archivos de configuración (conexión a la base de datos `database.php`, documentación).
- `controllers/`: Contiene los controladores que procesan la lógica de negocio (ej. `AuthController.php`, `InventarioController.php`).
- `models/`: Clases que representan las entidades del negocio y gestionan la interacción con la base de datos (ej. `Usuario.php`, `Inventario.php`, `Lote.php`).
- `views/`: Interfaces de usuario desarrolladas con HTML, Tailwind CSS y JS. Separadas en dashboards de administrador y trabajador.
- `public/`: Archivos estáticos accesibles públicamente como `index.php` (landing page), hojas de estilo e imágenes.
- `sql/`: Scripts de base de datos.
- `vendor/`: Dependencias de Composer (si las hay).

## Flujo de Datos (MVC)
1. El usuario interactúa con una **Vista** (`views/`).
2. La vista envía una petición (GET/POST) a un **Controlador** (`controllers/`).
3. El controlador procesa la solicitud, instancia los **Modelos** (`models/`) necesarios y ejecuta las consultas.
4. El controlador decide a qué vista redirigir y envía mensajes de éxito o error usando sesiones (`$_SESSION['alert']`).
