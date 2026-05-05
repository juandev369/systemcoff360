<?php

class Tarea
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT 
                    t.id_tarea,
                    t.descripcion,
                    t.prioridad,
                    t.estado,
                    t.fecha_creacion,
                    t.fecha_limite,
                    GROUP_CONCAT(u.nombre SEPARATOR ', ') AS trabajadores,
                    COUNT(a.id_asignacion) AS total_asignados
                FROM tarea t
                LEFT JOIN asignacion_tarea a ON t.id_tarea = a.id_tarea
                LEFT JOIN usuario u ON a.id_usuario = u.id_usuario
                GROUP BY t.id_tarea
                ORDER BY t.fecha_creacion DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_tarea)
    {
        $sql = "SELECT * FROM tarea WHERE id_tarea = :id_tarea LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_tarea' => $id_tarea
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerAsignaciones($id_tarea)
    {
        $sql = "SELECT 
                    a.id_asignacion,
                    a.id_tarea,
                    a.id_usuario,
                    a.fecha_asignacion,
                    a.estado,
                    u.nombre,
                    u.correo,
                    u.telefono
                FROM asignacion_tarea a
                INNER JOIN usuario u ON a.id_usuario = u.id_usuario
                WHERE a.id_tarea = :id_tarea
                ORDER BY a.fecha_asignacion DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_tarea' => $id_tarea
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTrabajadores()
    {
        $sql = "SELECT id_usuario, nombre, correo
                FROM usuario
                WHERE id_rol = 2
                AND estado = 'activo'
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO tarea
                    (descripcion, prioridad, estado, fecha_creacion, fecha_limite)
                    VALUES
                    (:descripcion, :prioridad, 'pendiente', NOW(), :fecha_limite)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':descripcion'  => trim($datos['descripcion']),
                ':prioridad'    => trim($datos['prioridad']),
                ':fecha_limite' => !empty($datos['fecha_limite']) ? $datos['fecha_limite'] : null
            ]);

            $id_tarea = $this->conn->lastInsertId();

            if (!empty($datos['trabajadores']) && is_array($datos['trabajadores'])) {
                $sqlAsignacion = "INSERT INTO asignacion_tarea
                                  (id_tarea, id_usuario, fecha_asignacion, estado)
                                  VALUES
                                  (:id_tarea, :id_usuario, NOW(), 'pendiente')";

                $stmtAsignacion = $this->conn->prepare($sqlAsignacion);

                foreach ($datos['trabajadores'] as $id_usuario) {
                    $stmtAsignacion->execute([
                        ':id_tarea'   => $id_tarea,
                        ':id_usuario' => (int)$id_usuario
                    ]);

                    $this->crearNotificacion(
                        (int)$id_usuario,
                        'Se te asignó una nueva tarea: ' . trim($datos['descripcion']),
                        'tarea'
                    );
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return "Error al crear tarea: " . $e->getMessage();
        }
    }

    private function crearNotificacion($id_usuario, $mensaje, $tipo = 'general')
    {
        try {
            $sql = "INSERT INTO notificacion
                    (id_usuario, mensaje, tipo, estado, fecha)
                    VALUES
                    (:id_usuario, :mensaje, :tipo, 'no_leida', NOW())";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':mensaje'    => $mensaje,
                ':tipo'       => $tipo
            ]);

        } catch (Exception $e) {
            return false;
        }
    }

    public function cambiarEstadoTarea($id_tarea, $estado)
    {
        try {
            $sql = "UPDATE tarea 
                    SET estado = :estado 
                    WHERE id_tarea = :id_tarea";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':estado'   => $estado,
                ':id_tarea' => $id_tarea
            ]);

        } catch (Exception $e) {
            return "Error al cambiar estado: " . $e->getMessage();
        }
    }

    public function cambiarEstadoAsignacion($id_asignacion, $estado)
    {
        try {
            $sql = "UPDATE asignacion_tarea 
                    SET estado = :estado 
                    WHERE id_asignacion = :id_asignacion";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':estado'        => $estado,
                ':id_asignacion' => $id_asignacion
            ]);

        } catch (Exception $e) {
            return "Error al cambiar estado de asignación: " . $e->getMessage();
        }
    }

    public function eliminar($id_tarea)
    {
        try {
            $sql = "DELETE FROM tarea WHERE id_tarea = :id_tarea";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id_tarea' => $id_tarea
            ]);

        } catch (Exception $e) {
            return "Error al eliminar tarea: " . $e->getMessage();
        }
    }

    public function total()
    {
        return (int)$this->conn
            ->query("SELECT COUNT(*) FROM tarea")
            ->fetchColumn();
    }

    public function totalPorEstado($estado)
    {
        $sql = "SELECT COUNT(*) FROM tarea WHERE estado = :estado";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':estado' => $estado
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function totalVencidas()
    {
        $sql = "SELECT COUNT(*) 
                FROM tarea 
                WHERE fecha_limite IS NOT NULL
                AND fecha_limite < CURDATE()
                AND estado <> 'completada'";

        return (int)$this->conn->query($sql)->fetchColumn();
    }
}