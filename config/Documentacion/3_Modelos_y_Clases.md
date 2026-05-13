# Modelos y Clases

Los modelos se conectan a la base de datos mediante la clase `Database` ubicada en `config/database.php`.

## Modelos Principales
- **Inventario (`models/Inventario.php`)**: Gestiona la creación, lectura, actualización y movimiento de Insumos, Herramientas y EPP (Equipos de Protección Personal). Contiene lógica para calcular totales y detectar bajo stock.
- **Usuario (`models/Usuario.php`)**: Maneja el CRUD de usuarios, autenticación (verificación de contraseñas) y validación de correos.
- **Lote (`models/Lote.php`)**: Control de las tierras de la finca, su estado y tamaño.
- **Tarea (`models/Tarea.php`)**: Asignación y seguimiento del estado de tareas agrícolas.

## Patrones
- **Inyección de Dependencias**: Los modelos reciben la conexión `$db` (PDO) a través de su constructor.
- **Consultas Preparadas**: Para evitar inyecciones SQL, todos los modelos usan `prepare()` y `execute()`.
