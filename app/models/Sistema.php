<?php
class Sistema {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function verificarPasswordAdmin($id_usuario, $password) {
        // Consultamos la contraseña
        $query = "SELECT password FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_usuario);
        $stmt->execute();
        
        // --- SOLUCIÓN AL ERROR 2014 ---
        // Usamos fetchAll() para vaciar completamente el buffer de la conexión.
        // Si usamos fetch(), la conexión queda esperando más datos y bloquea el TRUNCATE.
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cerramos el cursor y destruimos el objeto statement para liberar recursos
        $stmt->closeCursor();
        $stmt = null;

        // Verificamos si encontramos el usuario
        if (count($resultados) > 0) {
            $hashGuardado = $resultados[0]['password'];
            if (password_verify($password, $hashGuardado)) {
                return true;
            }
        }
        return false;
    }

    public function restablecerSistema() {
        try {
            // 1. Desactivar validación de llaves foráneas
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");

            // 2. Limpiar tablas transaccionales
            // Usamos TRUNCATE para reiniciar los contadores de ID (AUTO_INCREMENT)
            $this->conn->exec("TRUNCATE TABLE pagos");
            $this->conn->exec("TRUNCATE TABLE gastos");
            $this->conn->exec("TRUNCATE TABLE sesiones_caja");
            $this->conn->exec("TRUNCATE TABLE archivos_paciente");
            $this->conn->exec("TRUNCATE TABLE auditoria"); // Agregado según tu BD
            $this->conn->exec("TRUNCATE TABLE citas");
            
            // 3. Limpiar personal médico
            $this->conn->exec("TRUNCATE TABLE horarios_medicos");
            $this->conn->exec("TRUNCATE TABLE medicos");
            
            // 4. Limpiar Catálogos
            $this->conn->exec("TRUNCATE TABLE servicios");
            $this->conn->exec("TRUNCATE TABLE especialidades");
            $this->conn->exec("TRUNCATE TABLE medicamentos");

            // 5. Limpiar Usuarios (Excepto Administradores)
            // Borramos todos los que NO sean Rol 1 (Admin)
            $this->conn->exec("DELETE FROM usuarios WHERE id_rol != 1");
            
            // Optimizamos la tabla para recuperar espacio y reorganizar índices
            $this->conn->exec("OPTIMIZE TABLE usuarios");

            // 6. Reactivar validación de llaves foráneas
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");

            return true;
        } catch (Exception $e) {
            // En caso de error, intentamos reactivar los checks
            try { $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1"); } catch(Exception $x) {}
            return false;
        }
    }
}