<?php
// setup_admin.php
require_once 'config/db.php';

// Datos del Admin
$user = "Admin";
$email = "admin@scrimsync.com";
$pass_plana = "scrimsync"; // <-- TU CONTRASEÑA ELEGIDA
$pass_hash = password_hash($pass_plana, PASSWORD_DEFAULT); // Encriptación segura

// SQL de inserción
$sql = "INSERT INTO usuario (usu_tipo, usu_username, usu_email, usu_password, usu_alias, usu_descripcion)
        VALUES (0, '$user', '$email', '$pass_hash', 'SystemAdmin', 'Cuenta principal del sistema')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>Usuario Admin creado correctamente</h1>";
    echo "<p>Email: $email</p>";
    echo "<p>Password: $pass_plana</p>";
    echo "<br><a href='login.php'>Ir al Login</a>";
} else {
    echo "Error: " . $conn->error;
}
?>