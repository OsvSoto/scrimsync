<?php
// modules/auth/controller_auth.php
session_start();
require_once 'auth_logic.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $p_op = isset($_POST['p_op']) ? $_POST['p_op'] : '';
    switch ($p_op) {
        case 'Login':
            $p_credencial = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password'])
            ];

            $usuario_data = Autenticar_Usuario($conn, $p_credencial);

            if ($usuario_data) {
                $_SESSION['loggedin'] = true;
                $_SESSION['usu_id'] = $usuario_data['usu_id'];
                $_SESSION['username'] = $usuario_data['usu_username'];
                $_SESSION['alias'] = $usuario_data['usu_alias'];
                $_SESSION['tipo'] = (int)$usuario_data['usu_tipo'];

                if ($_SESSION['tipo'] == 0) {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../../index.php");
                }
                exit;
            } else {
                $_SESSION['flash_error'] = "Usuario o contraseña incorrectos.";
                header("Location: login.php");
            }
            break;

        case 'Registro':
            $p_usuario = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'email'    => trim($_POST['email']),
                'alias'    => trim($_POST['alias'])
            ];

            $registro_result = Registro_Usuario($conn, $p_usuario);

            if ($registro_result === true) {
                $_SESSION['flash_msg'] = "Registro exitoso. Ahora puedes ingresar.";
                header("Location: login.php");
            } elseif ($registro_result === false) {
                $_SESSION['flash_error'] = "Error: El email ingresado no es válido.";
                header("Location: register.php");
            } else {
                $_SESSION['flash_error'] = "Error: El usuario o email ya existen.";
                header("Location: register.php");
            }
            break;

        case 'Recuperar':
            $p_correo_recup = trim($_POST['email']);
            if (Recuperacion_Credenciales($conn, $p_correo_recup)) {
                $_SESSION['flash_msg'] = "Si el correo existe, se ha enviado una nueva contraseña.";
            } else {
                $_SESSION['flash_error'] = "No se pudo procesar la solicitud.";
            }
            header("Location: recover.php");
            break;

        default:
            $_SESSION['flash_error'] = "Operación no válida.";
            header("Location: login.php");
            break;
    }
    mysqli_close($conn);

} else {
    header("Location: login.php");
}
?>
