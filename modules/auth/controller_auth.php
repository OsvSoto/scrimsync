<?php
// modules/auth/controller_auth.php
session_start();
require_once 'auth_logic.php'; // Importamos las funciones (Módulos inferiores)

// Validación básica de entrada
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ENTRADA: p_op (La operación a realizar)
    $p_op = isset($_POST['p_op']) ? $_POST['p_op'] : '';

    // MÓDULO: Control_Acceso
    switch ($p_op) {
        
        // Case: Login
        case 'Login':
            // Preparar p_credencial
            $p_credencial = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password'])
            ];

            // Llamada al módulo inferior: Autenticar_Usuario
            $usuario_data = Autenticar_Usuario($conn, $p_credencial);

            if ($usuario_data) {
                // Éxito: Configuramos sesión
                $_SESSION['loggedin'] = true;
                $_SESSION['usu_id'] = $usuario_data['usu_id'];
                $_SESSION['username'] = $usuario_data['usu_username'];
                $_SESSION['alias'] = $usuario_data['usu_alias'];
                $_SESSION['rol'] = (int)$usuario_data['usu_tipo'];

                // Redirección según rol (Salida del módulo)
                if ($_SESSION['rol'] == 0) {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../../index.php"); 
                }
                exit;
            } else {
                // Fallo
                header("Location: login.php?error=invalid_credentials");
            }
            break;

        // Case: Registro
        case 'Registro':
            // Preparar p_usuario
            $p_usuario = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'email'    => trim($_POST['email']),
                'alias'    => trim($_POST['alias'])
            ];

            // Llamada al módulo inferior: Registro_Usuario
            if (Registro_Usuario($conn, $p_usuario)) {
                header("Location: login.php?msg=registered");
            } else {
                header("Location: register.php?error=db_error");
            }
            break;

        // Case: Recuperar 
        // case 'Recuperar':
        //     Recuperacion_Credenciales(...);
        //     break;

        default:
            header("Location: login.php?error=unknown_op");
            break;
    }
    
    mysqli_close($conn);

} else {
    // Si intentan entrar directo sin POST
    header("Location: login.php");
}
?>