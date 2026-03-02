<?php
// modules/team/actions/invite_member.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['usu_id'])) {
  header("Location: ../../../modules/auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../profile/index.php");
  exit;
}

$usu_id = $_SESSION['usu_id'];

if (isset($_POST['action']) && $_POST['action'] === 'accept_invite') {
  $not_id = isset($_POST['not_id']) ? (int)$_POST['not_id'] : 0;
  $equ_id = isset($_POST['equ_id']) ? (int)$_POST['equ_id'] : 0;

  if ($not_id <= 0 || $equ_id <= 0) {
    $_SESSION['flash_error'] = "Datos de invitación inválidos.";
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  }

  // Verificar que la notificación sea válida y pertenezca al usuario
  $sql_notif_check = "SELECT not_id FROM notificacion WHERE not_id = ? AND usu_id = ? AND equ_id = ? AND not_tipo = 'INVITACION'";
  $res_notif = $conn->execute_query($sql_notif_check, [$not_id, $usu_id, $equ_id]);

  if ($res_notif->num_rows == 0) {
    $_SESSION['flash_error'] = "Invitación no válida o expirada.";
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  }

  // Verificar si ya es miembro
  $sql_check = "SELECT per_id FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
  $res_check = $conn->execute_query($sql_check, [$usu_id, $equ_id]);
  if ($res_check->fetch_assoc()) {
    $conn->execute_query("DELETE FROM notificacion WHERE not_id = ?", [$not_id]);
    $_SESSION['flash_msg'] = "Ya eres miembro de este equipo.";
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  }

  // Añadir al equipo
  mysqli_begin_transaction($conn);
  try {
    $sql_jue = "SELECT jue_id FROM equipo WHERE equ_id = ?";
    $res_jue = $conn->execute_query($sql_jue, [$equ_id]);
    $equipo_info = $res_jue->fetch_assoc();
    if (!$equipo_info) throw new Exception("Equipo no encontrado.");
    $jue_id = $equipo_info['jue_id'];

    $rol_id = 0;
    if ($jue_id) {
      $sql_rol = "SELECT rol_id FROM rol_predefinido WHERE jue_id = ? LIMIT 1";
      $res_rol = $conn->execute_query($sql_rol, [$jue_id]);
      $rol_info = $res_rol->fetch_assoc();
      if ($rol_info) $rol_id = $rol_info['rol_id'];
    }

    if ($rol_id <= 0) throw new Exception("No hay roles definidos para el juego.");

    $sql_ins = "INSERT INTO permiso_equipo (usu_id, equ_id, rol_id, per_modif_horario, per_enviar_scrim, per_elim_miembro) VALUES (?, ?, ?, 0, 0, 0)";
    $conn->execute_query($sql_ins, [$usu_id, $equ_id, $rol_id]);

    $sql_del = "DELETE FROM notificacion WHERE not_id = ?";
    $conn->execute_query($sql_del, [$not_id]);

    mysqli_commit($conn);

    $_SESSION['flash_msg'] = "Te has unido al equipo correctamente.";
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  } catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['flash_error'] = "Error al unirse: " . $e->getMessage();
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  }
} elseif (isset($_POST['target_usu_id'])) {
  $equ_id = isset($_POST['equ_id']) ? (int)$_POST['equ_id'] : 0;
  $target_usu_id = (int)$_POST['target_usu_id'];

  if ($equ_id <= 0 || $target_usu_id <= 0) {
    header("Location: ../profile/index.php");
    exit;
  }

  $sql_perm = "SELECT per_elim_miembro FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
  $res_perm = $conn->execute_query($sql_perm, [$usu_id, $equ_id]);
  $perm_info = $res_perm->fetch_assoc();

  if (!$perm_info) {
    header("Location: ../profile/manage.php?id=$equ_id&error=no_permission");
    exit;
  }

  try {
    $sql_check = "SELECT per_id FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
    $res_check = $conn->execute_query($sql_check, [$target_usu_id, $equ_id]);
    if ($res_check->fetch_assoc()) {
      $_SESSION['flash_error'] = "El usuario ya es miembro.";
      header("Location: ../profile/manage.php?id=$equ_id");
      exit;
    }

    $sql_info = "SELECT equ_nombre FROM equipo WHERE equ_id = ?";
    $res_info = $conn->execute_query($sql_info, [$equ_id]);
    $equ_info = $res_info->fetch_assoc();
    $equ_nombre = $equ_info['equ_nombre'];

    $not_tipo = 'INVITACION';
    $not_asunto = 'Invitación de Equipo';
    $not_mensaje = "Has sido invitado a unirte a $equ_nombre.";

    $sql_notif = "INSERT INTO notificacion (usu_id, equ_id, not_tipo, not_asunto, not_mensaje) VALUES (?, ?, ?, ?, ?)";
    if ($conn->execute_query($sql_notif, [$target_usu_id, $equ_id, $not_tipo, $not_asunto, $not_mensaje])) {
      $_SESSION['flash_msg'] = 'invited';
    } else {
      $_SESSION['flash_error'] = "Error al enviar invitación.";
    }

    header("Location: ../profile/manage.php?id=$equ_id");
    exit;
  } catch (Exception $e) {
    $_SESSION['flash_error'] = "Error db: " . $e->getMessage();
    header("Location: ../profile/manage.php?id=$equ_id");
    exit;
  }
} else {
  header("Location: ../profile/index.php");
  exit;
}
