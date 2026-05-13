# Base de Datos

## Sistema Gestor
MySQL / MariaDB (usando PDO en PHP para conexiones seguras).

## Tablas Principales
- **usuarios**: Almacena credenciales y roles (`administrador`, `trabajador`).
- **lotes**: Representa los lotes de cultivo de la finca.
- **tareas**: Asignación de tareas a trabajadores en lotes específicos.
- **insumo / herramienta / epp**: Tablas dedicadas al control del inventario.
- **movimiento_inventario**: Historial de entradas y salidas de inventario.
- **cosechas**: Registro de la producción recolectada.

## Relaciones Clave
- Un `trabajador` (usuario) tiene asignadas múltiples `tareas`.
- Una `tarea` se ejecuta en un `lote` específico.
- Los `movimientos de inventario` están registrados y pueden estar asociados a un `usuario` que los retira o registra.
