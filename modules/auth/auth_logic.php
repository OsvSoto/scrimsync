<?php
// modules/auth/auth_logic.php
require_once '../../config/db.php';

function Autenticar_Usuario($conn, $p_credencial) {
    $username = $p_credencial['username'];
    $password_input = $p_credencial['password'];

    // SQL: Seleccionar password y tipo (y otros datos necesarios para la sesión)
    $sql = "SELECT usu_id, usu_username, usu_password, usu_tipo, usu_alias FROM usuario WHERE usu_username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $hash_bd = $row['usu_password'];
            
            // Verificación del hash
            if (password_verify($password_input, $hash_bd)) {
                // Retorna los datos del usuario (equivalente a retornar tipo_usuario)
                return $row; 
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false; // Credenciales inválidas
}

function Registro_Usuario($conn, $p_usuario) {
    $username = $p_usuario['username'];
    $email    = $p_usuario['email'];
    $alias    = $p_usuario['alias']; // Asumo que también pides alias
    $password = password_hash($p_usuario['password'], PASSWORD_BCRYPT);
    $tipo      = 1; // 1 = Jugador por defecto

    $sql = "INSERT INTO usuario (usu_username, usu_password, usu_email, usu_alias, usu_tipo) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $password, $email, $alias, $tipo);
        $resultado = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $resultado;
    }
    return false;
}
?>