<?php
// Archivo: public/reset.php
require_once '../app/config/Database.php';

$db = new Database();
$conn = $db->connect();

// Contraseña: admin123
$pass = password_hash('admin123', PASSWORD_BCRYPT);
$email = 'admin@medico.com';

// 1. Intentar actualizar
$sql = "UPDATE usuarios SET password = :p, id_rol = 1 WHERE email = :e";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':p', $pass);
$stmt->bindParam(':e', $email);
$stmt->execute();

if($stmt->rowCount() > 0) {
    echo "<h1>Contraseña Actualizada</h1><p>Usuario: admin@medico.com<br>Pass: admin123</p>";
} else {
    // 2. Si no existe, crearlo
    $sql = "INSERT INTO usuarios (nombre, email, password, id_rol) VALUES ('Admin', :e, :p, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':p', $pass);
    $stmt->bindParam(':e', $email);
    $stmt->execute();
    echo "<h1>Usuario Creado</h1><p>Usuario: admin@medico.com<br>Pass: admin123</p>";
}
echo "<br><a href='index.php'>Ir al Login</a>";