<?php

class Lote
{
    private $conn;
    private $tabla = "lote";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT 
                    l.*,
                    COALESCE((SELECT COUNT(*) FROM actividad_lote a WHERE a.id_lote = l.id_lote), 0) AS total_actividades,
                    COALESCE((SELECT SUM(c.cantidad_kg) FROM cosecha c WHERE c.id_lote = l.id_lote), 0) AS total_cosecha
                FROM lote l
                ORDER BY l.fecha_registro DESC, l.id_lote DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_lote)
    {
        $sql = "SELECT 
                    l.*,
                    COALESCE((SELECT COUNT(*) FROM actividad_lote a WHERE a.id_lote = l.id_lote), 0) AS total_actividades,
                    COALESCE((SELECT SUM(c.cantidad_kg) FROM cosecha c WHERE c.id_lote = l.id_lote), 0) AS total_cosecha
                FROM lote l
                WHERE l.id_lote = :id_lote
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_lote' => $id_lote
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($datos)
    {
        try {
            $sql = "INSERT INTO lote
                    (nombre, ubicacion, tipo_plantacion, area_hectareas, estado, fecha_registro)
                    VALUES
                    (:nombre, :ubicacion, :tipo_plantacion, :area_hectareas, 'activo', :fecha_registro)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nombre'           => trim($datos['nombre']),
                ':ubicacion'        => trim($datos['ubicacion']),
                ':tipo_plantacion'  => trim($datos['tipo_plantacion']),
                ':area_hectareas'   => $datos['area_hectareas'] !== '' ? $datos['area_hectareas'] : null,
                ':fecha_registro'   => $datos['fecha_registro']
            ]);

        } catch (Exception $e) {
            return "Error al registrar lote: " . $e->getMessage();
        }
    }

    public function actualizar($id_lote, $datos)
    {
        try {
            $sql = "UPDATE lote
                    SET nombre = :nombre,
                        ubicacion = :ubicacion,
                        tipo_plantacion = :tipo_plantacion,
                        area_hectareas = :area_hectareas,
                        estado = :estado
                    WHERE id_lote = :id_lote";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id_lote'          => $id_lote,
                ':nombre'           => trim($datos['nombre']),
                ':ubicacion'        => trim($datos['ubicacion']),
                ':tipo_plantacion'  => trim($datos['tipo_plantacion']),
                ':area_hectareas'   => $datos['area_hectareas'] !== '' ? $datos['area_hectareas'] : null,
                ':estado'           => trim($datos['estado'])
            ]);

        } catch (Exception $e) {
            return "Error al actualizar lote: " . $e->getMessage();
        }
    }

    public function cambiarEstado($id_lote, $estado)
    {
        try {
            $sql = "UPDATE lote SET estado = :estado WHERE id_lote = :id_lote";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':estado' => $estado,
                ':id_lote' => $id_lote
            ]);

        } catch (Exception $e) {
            return "Error al cambiar estado: " . $e->getMessage();
        }
    }

    public function registrarActividad($datos)
    {
        try {
            $sql = "INSERT INTO actividad_lote
                    (id_lote, id_responsable, tipo, fecha, descripcion, costo, proxima_fecha)
                    VALUES
                    (:id_lote, :id_responsable, :tipo, :fecha, :descripcion, :costo, :proxima_fecha)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id_lote'        => $datos['id_lote'],
                ':id_responsable' => $datos['id_responsable'],
                ':tipo'           => trim($datos['tipo']),
                ':fecha'          => $datos['fecha'],
                ':descripcion'    => trim($datos['descripcion']),
                ':costo'          => $datos['costo'] !== '' ? $datos['costo'] : 0,
                ':proxima_fecha'  => $datos['proxima_fecha'] !== '' ? $datos['proxima_fecha'] : null
            ]);

        } catch (Exception $e) {
            return "Error al registrar actividad: " . $e->getMessage();
        }
    }

    public function obtenerActividades($id_lote)
    {
        $sql = "SELECT 
                    a.*,
                    u.nombre AS responsable
                FROM actividad_lote a
                LEFT JOIN usuario u ON a.id_responsable = u.id_usuario
                WHERE a.id_lote = :id_lote
                ORDER BY a.fecha DESC, a.id_actividad DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_lote' => $id_lote
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTrabajadores()
    {
        $sql = "SELECT id_usuario, nombre
                FROM usuario
                WHERE estado = 'activo'
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function total()
    {
        return (int)$this->conn->query("SELECT COUNT(*) FROM lote")->fetchColumn();
    }

    public function totalActivos()
    {
        return (int)$this->conn->query("SELECT COUNT(*) FROM lote WHERE estado = 'activo'")->fetchColumn();
    }

    public function totalArea()
    {
        return (float)$this->conn->query("SELECT COALESCE(SUM(area_hectareas), 0) FROM lote")->fetchColumn();
    }
}