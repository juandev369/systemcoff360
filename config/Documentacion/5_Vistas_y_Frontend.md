# Vistas y Frontend

## Tecnologías Utilizadas
- **HTML5 & PHP**: Generación de páginas dinámicas.
- **Tailwind CSS**: Framework utilitario inyectado vía CDN para estilos rápidos, responsivos y modernos (estilo glassmorphism, gradientes verdes).
- **SweetAlert2**: Notificaciones modales elegantes.
- **Chart.js**: Renderizado de gráficos en tiempo real en los dashboards.
- **FontAwesome**: Iconografía de la interfaz.

## Estructura de Vistas
- **`views/dashboard/`**: Paneles de control protegidos por sesión. Incluye paneles específicos como `admin.php` (con gráficas y resumen global) y `trabajador.php` (enfocado en tareas asignadas).
- **`views/usuarios/`**: Pantallas de autenticación (`login.php`, `registre.php`).
- **`views/layouts/`**: Componentes reutilizables como la barra lateral (`sidebar.php`) y el widget flotante del asistente virtual (`assistant_widget.php`).
