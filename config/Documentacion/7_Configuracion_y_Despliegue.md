# Configuración y Despliegue

## Requisitos
- Servidor web local: Laragon o XAMPP.
- PHP >= 8.0 (Recomendado).
- MySQL / MariaDB.
- Conexión a Internet para la carga de CDNs (Tailwind, Chart.js, SweetAlert).

## Instalación
1. **Clonar/Copiar el repositorio** en la carpeta raíz del servidor (`C:/laragon/www/system-coff-360`).
2. **Importar la Base de Datos**: Ejecutar el script SQL que se encuentra en la carpeta `sql/` (ej. `systemcoff360.sql`) en phpMyAdmin o HeidiSQL.
3. **Configurar Conexión**: Modificar el archivo `config/database.php` con las credenciales del servidor (por defecto, usuario `root`, sin contraseña en Laragon).
4. **Acceso Inicial**: Abrir el navegador en `http://localhost/system-coff-360/public/index.php`. Crear un usuario o iniciar sesión con una cuenta de administrador existente.
