<?php

class Usuario
{
    private $conn;
    private $tabla = "usuario";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function obtenerIdRol($rol)
    {
        $rol = strtolower(trim($rol));

        if ($rol === 'administrador' || $rol === 'admin') {
            return 1;
        }

        return 2; // trabajador
    }

    public function existeCorreo($email)
    {
        $sql = "SELECT COUNT(*) FROM {$this->tabla} WHERE correo = :correo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':correo' => strtolower(trim($email))
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function emailExiste($email)
    {
        return $this->existeCorreo($email);
    }

    public function buscarPorEmail($email)
    {
        return $this->obtenerPorEmail($email);
    }

    public function obtenerPorEmail($email)
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.DNI,
                    u.telefono,
                    u.correo,
                    u.contrasena,
                    u.estado,
                    u.fecha_registro,
                    u.intentos_fallidos,
                    r.nombre AS rol
                FROM {$this->tabla} u
                LEFT JOIN rol r ON u.id_rol = r.id_rol
                WHERE u.correo = :correo
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':correo' => strtolower(trim($email))
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($datos)
    {
        try {
            $nombres = trim($datos['nombres'] ?? '');
            $apellidos = trim($datos['apellidos'] ?? '');
            $nombre = trim($nombres . ' ' . $apellidos);

            $correo = strtolower(trim($datos['email'] ?? $datos['correo'] ?? ''));
            $telefono = trim($datos['telefono'] ?? '');
            $password = $datos['password'] ?? $datos['contrasena'] ?? '';
            $rol = strtolower(trim($datos['rol'] ?? 'trabajador'));

            if ($nombre === '') {
                return "El nombre no puede estar vacío.";
            }

            if ($correo === '') {
                return "El correo no puede estar vacío.";
            }

            if ($password === '') {
                return "La contraseña no puede estar vacía.";
            }

            if ($this->existeCorreo($correo)) {
                return "El correo ya está registrado.";
            }

            $idRol = $this->obtenerIdRol($rol);

            // Tu formulario no envía DNI, pero tu tabla usuario sí tiene esa columna.
            $dni = trim($datos['DNI'] ?? $datos['dni'] ?? '');
            if ($dni === '') {
                $dni = 'TEMP' . time() . rand(100, 999);
            }

            $sql = "INSERT INTO {$this->tabla}
                    (id_rol, nombre, DNI, telefono, correo, contrasena, estado, fecha_registro, intentos_fallidos)
                    VALUES
                    (:id_rol, :nombre, :dni, :telefono, :correo, :contrasena, 'activo', NOW(), 0)";

            $stmt = $this->conn->prepare($sql);

            $ok = $stmt->execute([
                ':id_rol'     => $idRol,
                ':nombre'     => $nombre,
                ':dni'        => $dni,
                ':telefono'   => $telefono,
                ':correo'     => $correo,
                ':contrasena' => password_hash($password, PASSWORD_BCRYPT)
            ]);

            return $ok;

        } catch (Exception $e) {
            return "Error al registrar: " . $e->getMessage();
        }
    }

    public function crear($datos)
    {
        return $this->registrar($datos);
    }

    public function obtenerTodos()
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.DNI,
                    u.telefono,
                    u.correo,
                    u.estado,
                    u.fecha_registro,
                    u.intentos_fallidos,
                    r.nombre AS rol
                FROM {$this->tabla} u
                LEFT JOIN rol r ON u.id_rol = r.id_rol
                ORDER BY u.fecha_registro DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodos()
    {
        return $this->obtenerTodos();
    }

    public function obtenerPorId($id_usuario)
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.DNI,
                    u.telefono,
                    u.correo,
                    u.estado,
                    u.fecha_registro,
                    u.intentos_fallidos,
                    r.nombre AS rol
                FROM {$this->tabla} u
                LEFT JOIN rol r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = :id_usuario
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id_usuario, $datos)
    {
        try {
            $nombres = trim($datos['nombres'] ?? '');
            $apellidos = trim($datos['apellidos'] ?? '');
            $nombre = trim($datos['nombre'] ?? ($nombres . ' ' . $apellidos));

            $correo = strtolower(trim($datos['email'] ?? $datos['correo'] ?? ''));
            $telefono = trim($datos['telefono'] ?? '');
            $rol = strtolower(trim($datos['rol'] ?? 'trabajador'));
            $dni = trim($datos['DNI'] ?? $datos['dni'] ?? '');

            if ($nombre === '') {
                return "El nombre no puede estar vacío.";
            }

            if ($correo === '') {
                return "El correo no puede estar vacío.";
            }

            $idRol = $this->obtenerIdRol($rol);

            $sql = "UPDATE {$this->tabla}
                    SET id_rol = :id_rol,
                        nombre = :nombre,
                        DNI = :dni,
                        telefono = :telefono,
                        correo = :correo";

            $params = [
                ':id_usuario' => $id_usuario,
                ':id_rol'     => $idRol,
                ':nombre'     => $nombre,
                ':dni'        => $dni,
                ':telefono'   => $telefono,
                ':correo'     => $correo
            ];

            if (!empty($datos['password']) || !empty($datos['contrasena'])) {
                $password = $datos['password'] ?? $datos['contrasena'];
                $sql .= ", contrasena = :contrasena";
                $params[':contrasena'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $sql .= " WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute($params);

        } catch (Exception $e) {
            return "Error al actualizar: " . $e->getMessage();
        }
    }

    public function cambiarEstado($id_usuario, $activo)
    {
        try {
            if (is_numeric($activo)) {
                $estado = ((int)$activo === 1) ? 'activo' : 'inactivo';
            } else {
                $estado = strtolower(trim($activo));
            }

            $sql = "UPDATE {$this->tabla}
                    SET estado = :estado
                    WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':estado'     => $estado,
                ':id_usuario' => $id_usuario
            ]);

        } catch (Exception $e) {
            return "Error al cambiar estado: " . $e->getMessage();
        }
    }

    public function eliminar($id_usuario)
    {
        try {
            $sql = "DELETE FROM {$this->tabla} WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':id_usuario' => $id_usuario
            ]);

        } catch (Exception $e) {
            return "Error al eliminar: " . $e->getMessage();
        }
    }

    public function total()
    {
        return (int)$this->conn
            ->query("SELECT COUNT(*) FROM {$this->tabla}")
            ->fetchColumn();
    }

    public function totalPorRol($rol)
    {
        $idRol = $this->obtenerIdRol($rol);

        $sql = "SELECT COUNT(*)
                FROM {$this->tabla}
                WHERE id_rol = :id_rol
                AND estado = 'activo'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_rol' => $idRol
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function actualizarUltimoAcceso($id_usuario)
    {
        // Tu tabla no tiene columna ultimo_acceso.
        // Se deja vacío para que el login no falle.
        return true;
    }
}