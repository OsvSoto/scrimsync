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
                header("Location: login.php?error=invalid_credentials");
            }
            break;

        case 'Registro':
            $p_usuario = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'email'    => trim($_POST['email']),
                'alias'    => trim($_POST['alias'])
            ];

            if (Registro_Usuario($conn, $p_usuario)) {
                header("Location: login.php?msg=registered");
            } else {
                header("Location: register.php?error=db_error");
            }
            break;

        case 'Recuperar':
            $p_correo_recup = trim($_POST['email']);
            Recuperacion_Credenciales($conn, $p_correo_recup);
            header("Location: recover.php?msg=email_sent");
            break;

        default:
            header("Location: login.php?error=unknown_op");
            break;
    }
    mysqli_close($conn);

} else {
    header("Location: login.php");
}
?>
