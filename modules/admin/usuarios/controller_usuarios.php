<?php
// modules/admin/usuarios/controller_usuarios.php
session_start();
require_once '../../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['loggedin']) && $_SESSION['tipo'] == 0) {

    $p_op = $_POST['p_op'];
    $p_usu_admin = $_SESSION['usu_id'];

    if ($p_op == 'Asignar_Tipo_Usu') {
        $p_usu_objetivo = isset($_POST['usu_id']) ? intval($_POST['usu_id']) : 0;
        $nuevo_tipo = isset($_POST['nuevo_tipo']) ? intval($_POST['nuevo_tipo']) : 1;

        if ($p_usu_objetivo > 0 && ($p_usu_objetivo != $p_usu_admin)) {
            mysqli_begin_transaction($conn);
            try {
                // Actualizar tipo de usuario
                $sql_update = "UPDATE usuario SET usu_tipo = ? WHERE usu_id = ?";
                if (!$conn->execute_query($sql_update, [$nuevo_tipo, $p_usu_objetivo])) {
                    throw new Exception("Error al actualizar usuario.");
                }

                $not_tipo = "SISTEMA";
                $not_asunto = "Cambio de Privilegios";
                
                if ($nuevo_tipo == 0) {
                    $not_mensaje = "Has sido ascendido a Administrador del sistema.";
                    $flash = 'promovido';
                } else {
                    $not_mensaje = "Tus privilegios de Administrador han sido revocados.";
                    $flash = 'demovido';
                }

                $sql_notif = "INSERT INTO notificacion (usu_id, not_tipo, not_asunto, not_mensaje) VALUES (?, ?, ?, ?)";
                if (!$conn->execute_query($sql_notif, [$p_usu_objetivo, $not_tipo, $not_asunto, $not_mensaje])) {
                    throw new Exception("Error al registrar notificación.");
                }

                mysqli_commit($conn);
                $_SESSION['flash_msg'] = $flash;
                header("Location: index.php");
            } catch (Exception $e) {
                mysqli_rollback($conn);
                header("Location: index.php?error=" . urlencode($e->getMessage()));
            }
        } else {
            header("Location: index.php?error=invalid_id");
        }
    } else {
        header("Location: index.php?error=op_invalida");
    }

    mysqli_close($conn);

} else {
    header("Location: ../../../index.php");
}
?>
