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
$sql_perm = "SELECT per_enviar_scrim, per_elim_miembro FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
$res_perm = $conn->execute_query($sql_perm, [$usu_id, $equ_id]);
$perm = $res_perm->fetch_assoc();

if (!$perm || ($perm['per_enviar_scrim'] != 1 && $perm['per_elim_miembro'] != 1)) {
    die("No tienes permisos para eliminar miembros.");
}

// Evitar auto-eliminación
if ($usu_id == $target_usu_id) {
    die("No puedes eliminarte a ti mismo.");
}

// Eliminar usuario del equipo + permisos
$sql_delete = "DELETE FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
if ($conn->execute_query($sql_delete, [$target_usu_id, $equ_id])) {
    $asunto = "$equ_nombre: Has sido expulsado";
    $mensaje = "Te han expulsado del equipo";
    $stmt_notif = "
        INSERT INTO notificacion (usu_id, not_tipo, not_asunto, not_mensaje)
        VALUES (?, 'SISTEMA', ?, ?)";
    if ($conn->execute_query($stmt_notif, [$target_usu_id, $asunto, $mensaje]) === TRUE) {
        $_SESSION['flash_msg'] = 'kicked';
    }
    header("Location: ../profile/view.php?id=$equ_id");
} else {
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
}
