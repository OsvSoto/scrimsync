<?php
session_start();
require_once '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../profile/index.php");
    exit;
}

if (!isset($_SESSION['loggedin'])) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];
$equ_id = isset($_POST['equ_id']) ? (int)$_POST['equ_id'] : 0;
$equ_nombre = $_POST['equ_nombre'];
$target_usu_id = isset($_POST['target_usu_id']) ? (int)$_POST['target_usu_id'] : 0;

if ($equ_id <= 0 || $target_usu_id <= 0) {
    die("Datos inválidos.");
}

// Verificar permisos (per_enviar_scrim=1 como solicitado o per_elim_miembro)
$sql_perm = "SELECT per_enviar_scrim, per_elim_miembro FROM permiso_equipo WHERE usu_id = '$usu_id' AND equ_id = '$equ_id'";
$res_perm = mysqli_query($conn, $sql_perm);
$perm = mysqli_fetch_assoc($res_perm);

if (!$perm || ($perm['per_enviar_scrim'] != 1 && $perm['per_elim_miembro'] != 1)) {
    die("No tienes permisos para eliminar miembros.");
}

// Evitar auto-eliminación
if ($usu_id == $target_usu_id) {
    die("No puedes eliminarte a ti mismo.");
}

// Eliminar
$sql_delete = "DELETE FROM permiso_equipo WHERE usu_id = '$target_usu_id' AND equ_id = '$equ_id'";
if (mysqli_query($conn, $sql_delete)) {

    $stmt_notif = "INSERT INTO notificacion (usu_id, not_tipo, not_asunto, not_mensaje) VALUES ($target_usu_id, 'SISTEMA', 'Has sido kickeado', '$equ_nombre: kgaste prro')";
    if ($conn->query($stmt_notif) === TRUE) {
        $_SESSION['flash_msg'] = 'kicked';
    }
    header("Location: ../profile/view.php?id=$equ_id");
} else {
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
}
?>
