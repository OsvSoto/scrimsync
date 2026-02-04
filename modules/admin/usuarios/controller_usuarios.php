<?php
// modules/admin/usuarios/controller_usuarios.php
session_start();
require_once '../../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['loggedin']) && $_SESSION['tipo'] == 0) {

    $p_op = $_POST['p_op'];

    if ($p_op == 'Asignar_Tipo_Usu') {
        $p_usu_objetivo = isset($_POST['usu_id']) ? intval($_POST['usu_id']) : 0;
        $p_usu_admin = $_SESSION['usu_id'];

        if ($p_usu_objetivo > 0) {
            // Iniciar transacción
            mysqli_begin_transaction($conn);

            try {
                // 1. Verificar que quien ejecuta ES admin (doble seguridad)
                $sql_check = "SELECT usu_tipo FROM usuario WHERE usu_id = ?";
                $stmt_check = mysqli_prepare($conn, $sql_check);
                mysqli_stmt_bind_param($stmt_check, "i", $p_usu_admin);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $row_check = mysqli_fetch_assoc($result_check);
                mysqli_stmt_close($stmt_check);

                if ($row_check['usu_tipo'] != 0) {
                    throw new Exception("No tienes permisos de administrador.");
                }

                // 2. Actualizar tipo de usuario
                $sql_update = "UPDATE usuario SET usu_tipo = 0 WHERE usu_id = ?";
                $stmt_update = mysqli_prepare($conn, $sql_update);
                mysqli_stmt_bind_param($stmt_update, "i", $p_usu_objetivo);

                if (!mysqli_stmt_execute($stmt_update)) {
                    throw new Exception("Error al actualizar usuario.");
                }
                mysqli_stmt_close($stmt_update);

                // 3. Registrar Notificación (Según Tabla 8)
                // Registrar_Notificacion(p_usu_objetivo, "Sistema", "Cambio de Privilegios", ...)
                $not_tipo = "SISTEMA";
                $not_asunto = "Cambio de Privilegios";
                $not_mensaje = "Has sido ascendido a Administrador del sistema.";

                $sql_notif = "INSERT INTO notificacion (usu_id, not_tipo, not_asunto, not_mensaje) VALUES (?, ?, ?, ?)";
                $stmt_notif = mysqli_prepare($conn, $sql_notif);
                mysqli_stmt_bind_param($stmt_notif, "isss", $p_usu_objetivo, $not_tipo, $not_asunto, $not_mensaje);

                if (!mysqli_stmt_execute($stmt_notif)) {
                    throw new Exception("Error al registrar notificación.");
                }
                mysqli_stmt_close($stmt_notif);

                // Commit
                mysqli_commit($conn);
                $_SESSION['flash_msg'] = 'promovido';
                header("Location: index.php");
                /* header("Location: index.php?msg=promovido"); */

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
    // Si no es admin o no es POST
    header("Location: ../../../index.php");
}
?>
