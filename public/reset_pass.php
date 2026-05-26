<?php
// Script para resetear la contraseña del admin
require_once __DIR__ . '/../app/config/Database.php';

echo "<h1>Reparación de Usuario Admin</h1>";

try {
    $database = new Database();
    $db = $database->connect();

    // 1. Definir la contraseña y el email
    $email = 'admin@medico.com';
    $password_texto_plano = 'admin123';
    
    // 2. Generar el Hash con EL PHP DE TU SERVIDOR (Esto garantiza compatibilidad)
    $password_hash = password_hash($password_texto_plano, PASSWORD_BCRYPT);

    echo "<p>Intentando actualizar usuario: <strong>$email</strong></p>";
    echo "<p>Nueva contraseña será: <strong>$password_texto_plano</strong></p>";
    echo "<p>Hash generado: <small>$password_hash</small></p>";

    // 3. Actualizar en la BD
    $query = "UPDATE usuarios SET password = :pass, id_rol = 1 WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':pass', $password_hash);
    $stmt->bindParam(':email', $email);

    if($stmt->execute()) {
        if($stmt->rowCount() > 0) {
            echo "<h2 style='color:green'>¡ÉXITO! Contraseña actualizada.</h2>";
            echo "<p>Ahora borra este archivo y ve al Login.</p>";
            echo "<a href='index.php'>Ir al Login</a>";
        } else {
            // Si no actualizó nada, es porque el usuario no existe. Lo creamos.
            echo "<p style='color:orange'>El usuario no existía. Creándolo...</p>";
            
            $queryInsert = "INSERT INTO usuarios (nombre, email, password, id_rol) VALUES ('Admin', :email, :pass, 1)";
            $stmtInsert = $db->prepare($queryInsert);
            $stmtInsert->bindParam(':pass', $password_hash);
            $stmtInsert->bindParam(':email', $email);
            
            if($stmtInsert->execute()) {
                echo "<h2 style='color:green'>¡ÉXITO! Usuario creado y contraseña configurada.</h2>";
                echo "<a href='index.php'>Ir al Login</a>";
            }
        }
    } else {
        echo "<h2 style='color:red'>ERROR SQL</h2>";
        print_r($stmt->errorInfo());
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}