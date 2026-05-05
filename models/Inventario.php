<?php

class Inventario
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerInsumos()
    {
        $sql = "SELECT *,
                CASE 
                    WHEN stock_actual <= stock_minimo THEN 'bajo'
                    ELSE 'normal'
                END AS alerta_stock
                FROM insumo
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearInsumo($datos)
    {
        try {
            $sql = "INSERT INTO insumo
                    (nombre, tipo, unidad, stock_actual, stock_minimo, precio_unidad)
                    VALUES
                    (:nombre, :tipo, :unidad, :stock_actual, :stock_minimo, :precio_unidad)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nombre' => trim($datos['nombre']),
                ':tipo' => trim($datos['tipo']),
                ':unidad' => trim($datos['unidad']),
                ':stock_actual' => $datos['stock_actual'],
                ':stock_minimo' => $datos['stock_minimo'],
                ':precio_unidad' => $datos['precio_unidad']
            ]);

        } catch (Exception $e) {
            return "Error al crear insumo: " . $e->getMessage();
        }
    }

    public function actualizarStockInsumo($id_insumo, $tipo_movimiento, $cantidad)
    {
        try {
            $insumo = $this->obtenerInsumoPorId($id_insumo);

            if (!$insumo) {
                return "No se encontró el insumo.";
            }

            $stockActual = (float)$insumo['stock_actual'];
            $cantidad = (float)$cantidad;

            if ($cantidad <= 0) {
                return "La cantidad debe ser mayor a cero.";
            }

            if ($tipo_movimiento === 'entrada') {
                $nuevoStock = $stockActual + $cantidad;
            } else {
                if ($cantidad > $stockActual) {
                    return "No hay suficiente stock disponible.";
                }

                $nuevoStock = $stockActual - $cantidad;
            }

            $sql = "UPDATE insumo SET stock_actual = :stock_actual WHERE id_insumo = :id_insumo";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':stock_actual' => $nuevoStock,
                ':id_insumo' => $id_insumo
            ]);

        } catch (Exception $e) {
            return "Error al actualizar stock: " . $e->getMessage();
        }
    }

    public function obtenerInsumoPorId($id_insumo)
    {
        $sql = "SELECT * FROM insumo WHERE id_insumo = :id_insumo LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_insumo' => $id_insumo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerHerramientas()
    {
        $sql = "SELECT h.*,
                (
                    SELECT u.nombre
                    FROM entrega_herramienta eh
                    INNER JOIN usuario u ON eh.id_usuario = u.id_usuario
                    WHERE eh.id_herramienta = h.id_herramienta
                    AND eh.fecha_devolucion IS NULL
                    ORDER BY eh.id_entrega DESC
                    LIMIT 1
                ) AS responsable_actual
                FROM herramienta h
                ORDER BY h.nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearHerramienta($datos)
    {
        try {
            $sql = "INSERT INTO herramienta
                    (nombre, descripcion, estado, fecha_registro)
                    VALUES
                    (:nombre, :descripcion, 'disponible', :fecha_registro)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nombre' => trim($datos['nombre']),
                ':descripcion' => trim($datos['descripcion']),
                ':fecha_registro' => $datos['fecha_registro']
            ]);

        } catch (Exception $e) {
            return "Error al crear herramienta: " . $e->getMessage();
        }
    }

    public function entregarHerramienta($datos)
    {
        try {
            $this->conn->beginTransaction();

            $herramienta = $this->obtenerHerramientaPorId($datos['id_herramienta']);

            if (!$herramienta) {
                $this->conn->rollBack();
                return "No se encontró la herramienta.";
            }

            if ($herramienta['estado'] !== 'disponible') {
                $this->conn->rollBack();
                return "La herramienta no está disponible.";
            }

            $sql = "INSERT INTO entrega_herramienta
                    (id_herramienta, id_usuario, fecha_entrega, estado_herramienta, fecha_devolucion, observaciones)
                    VALUES
                    (:id_herramienta, :id_usuario, :fecha_entrega, :estado_herramienta, NULL, :observaciones)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_herramienta' => $datos['id_herramienta'],
                ':id_usuario' => $datos['id_usuario'],
                ':fecha_entrega' => $datos['fecha_entrega'],
                ':estado_herramienta' => $datos['estado_herramienta'],
                ':observaciones' => trim($datos['observaciones'])
            ]);

            $sqlEstado = "UPDATE herramienta SET estado = 'en_uso' WHERE id_herramienta = :id_herramienta";
            $stmtEstado = $this->conn->prepare($sqlEstado);
            $stmtEstado->execute([':id_herramienta' => $datos['id_herramienta']]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return "Error al entregar herramienta: " . $e->getMessage();
        }
    }

    public function devolverHerramienta($id_entrega)
    {
        try {
            $this->conn->beginTransaction();

            $sqlEntrega = "SELECT * FROM entrega_herramienta WHERE id_entrega = :id_entrega LIMIT 1";
            $stmtEntrega = $this->conn->prepare($sqlEntrega);
            $stmtEntrega->execute([':id_entrega' => $id_entrega]);
            $entrega = $stmtEntrega->fetch(PDO::FETCH_ASSOC);

            if (!$entrega) {
                $this->conn->rollBack();
                return "No se encontró la entrega.";
            }

            $sql = "UPDATE entrega_herramienta
                    SET fecha_devolucion = CURDATE()
                    WHERE id_entrega = :id_entrega";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_entrega' => $id_entrega]);

            $sqlHerramienta = "UPDATE herramienta
                               SET estado = 'disponible'
                               WHERE id_herramienta = :id_herramienta";

            $stmtHerramienta = $this->conn->prepare($sqlHerramienta);
            $stmtHerramienta->execute([':id_herramienta' => $entrega['id_herramienta']]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return "Error al devolver herramienta: " . $e->getMessage();
        }
    }

    public function obtenerHerramientaPorId($id_herramienta)
    {
        $sql = "SELECT * FROM herramienta WHERE id_herramienta = :id_herramienta LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_herramienta' => $id_herramienta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEpp()
    {
        $sql = "SELECT *,
                CASE 
                    WHEN stock_disponible <= 2 THEN 'bajo'
                    ELSE 'normal'
                END AS alerta_stock
                FROM epp
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearEpp($datos)
    {
        try {
            $sql = "INSERT INTO epp
                    (nombre, descripcion, cantidad_total, stock_disponible, talla)
                    VALUES
                    (:nombre, :descripcion, :cantidad_total, :stock_disponible, :talla)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':nombre' => trim($datos['nombre']),
                ':descripcion' => trim($datos['descripcion']),
                ':cantidad_total' => $datos['cantidad_total'],
                ':stock_disponible' => $datos['stock_disponible'],
                ':talla' => trim($datos['talla'])
            ]);

        } catch (Exception $e) {
            return "Error al crear EPP: " . $e->getMessage();
        }
    }

    public function entregarEpp($datos)
    {
        try {
            $this->conn->beginTransaction();

            $epp = $this->obtenerEppPorId($datos['id_epp']);

            if (!$epp) {
                $this->conn->rollBack();
                return "No se encontró el EPP.";
            }

            if ((int)$epp['stock_disponible'] <= 0) {
                $this->conn->rollBack();
                return "No hay stock disponible de este EPP.";
            }

            $sql = "INSERT INTO entrega_epp
                    (id_epp, id_usuario, fecha_entrega, estado_elemento, fecha_devolucion, observaciones)
                    VALUES
                    (:id_epp, :id_usuario, :fecha_entrega, :estado_elemento, NULL, :observaciones)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_epp' => $datos['id_epp'],
                ':id_usuario' => $datos['id_usuario'],
                ':fecha_entrega' => $datos['fecha_entrega'],
                ':estado_elemento' => $datos['estado_elemento'],
                ':observaciones' => trim($datos['observaciones'])
            ]);

            $sqlStock = "UPDATE epp
                         SET stock_disponible = stock_disponible - 1
                         WHERE id_epp = :id_epp";

            $stmtStock = $this->conn->prepare($sqlStock);
            $stmtStock->execute([':id_epp' => $datos['id_epp']]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return "Error al entregar EPP: " . $e->getMessage();
        }
    }

    public function devolverEpp($id_entrega)
    {
        try {
            $this->conn->beginTransaction();

            $sqlEntrega = "SELECT * FROM entrega_epp WHERE id_entrega = :id_entrega LIMIT 1";
            $stmtEntrega = $this->conn->prepare($sqlEntrega);
            $stmtEntrega->execute([':id_entrega' => $id_entrega]);
            $entrega = $stmtEntrega->fetch(PDO::FETCH_ASSOC);

            if (!$entrega) {
                $this->conn->rollBack();
                return "No se encontró la entrega.";
            }

            $sql = "UPDATE entrega_epp
                    SET fecha_devolucion = CURDATE()
                    WHERE id_entrega = :id_entrega";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_entrega' => $id_entrega]);

            $sqlStock = "UPDATE epp
                         SET stock_disponible = stock_disponible + 1
                         WHERE id_epp = :id_epp";

            $stmtStock = $this->conn->prepare($sqlStock);
            $stmtStock->execute([':id_epp' => $entrega['id_epp']]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            return "Error al devolver EPP: " . $e->getMessage();
        }
    }

    public function obtenerEppPorId($id_epp)
    {
        $sql = "SELECT * FROM epp WHERE id_epp = :id_epp LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_epp' => $id_epp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEntregasHerramientas()
    {
        $sql = "SELECT eh.*, h.nombre AS herramienta, u.nombre AS trabajador
                FROM entrega_herramienta eh
                INNER JOIN herramienta h ON eh.id_herramienta = h.id_herramienta
                INNER JOIN usuario u ON eh.id_usuario = u.id_usuario
                ORDER BY eh.fecha_entrega DESC, eh.id_entrega DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEntregasEpp()
    {
        $sql = "SELECT ee.*, e.nombre AS epp, u.nombre AS trabajador
                FROM entrega_epp ee
                INNER JOIN epp e ON ee.id_epp = e.id_epp
                INNER JOIN usuario u ON ee.id_usuario = u.id_usuario
                ORDER BY ee.fecha_entrega DESC, ee.id_entrega DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
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

    public function totales()
    {
        return [
            'insumos' => (int)$this->conn->query("SELECT COUNT(*) FROM insumo")->fetchColumn(),
            'insumos_bajos' => (int)$this->conn->query("SELECT COUNT(*) FROM insumo WHERE stock_actual <= stock_minimo")->fetchColumn(),
            'herramientas' => (int)$this->conn->query("SELECT COUNT(*) FROM herramienta")->fetchColumn(),
            'herramientas_disponibles' => (int)$this->conn->query("SELECT COUNT(*) FROM herramienta WHERE estado = 'disponible'")->fetchColumn(),
            'epp' => (int)$this->conn->query("SELECT COUNT(*) FROM epp")->fetchColumn(),
            'epp_bajo' => (int)$this->conn->query("SELECT COUNT(*) FROM epp WHERE stock_disponible <= 2")->fetchColumn()
        ];
    }
}
