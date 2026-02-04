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
  $stmt = $conn->prepare("SELECT not_id FROM notificacion WHERE not_id = ? AND usu_id = ? AND equ_id = ? AND not_tipo = 'INVITACION'");
  $stmt->bind_param("iii", $not_id, $usu_id, $equ_id);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows == 0) {
    $stmt->close();
    $_SESSION['flash_error'] = "Invitación no válida o expirada.";
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  }
  $stmt->close();

  // Verificar si ya es miembro
  $stmt_check = $conn->prepare("SELECT per_id FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?");
  $stmt_check->bind_param("ii", $usu_id, $equ_id);
  $stmt_check->execute();
  if ($stmt_check->fetch()) {
    $stmt_check->close();
    $conn->query("DELETE FROM notificacion WHERE not_id = $not_id");
    $_SESSION['flash_msg'] = "Ya eres miembro de este equipo.";
    header("Location: ../../../modules/user/notification/index.php");
    exit;
  }
  $stmt_check->close();

  // Añadir al equipo
  mysqli_begin_transaction($conn);
  try {
    $stmt_jue = $conn->prepare("SELECT jue_id FROM equipo WHERE equ_id = ?");
    $stmt_jue->bind_param("i", $equ_id);
    $stmt_jue->execute();
    $stmt_jue->bind_result($jue_id);
    if (!$stmt_jue->fetch()) throw new Exception("Equipo no encontrado.");
    $stmt_jue->close();

    $rol_id = 0;
    if ($jue_id) {
      $stmt_rol = $conn->prepare("SELECT rol_id FROM rol_predefinido WHERE jue_id = ? LIMIT 1");
      $stmt_rol->bind_param("i", $jue_id);
      $stmt_rol->execute();
      $stmt_rol->bind_result($rol_found);
      if ($stmt_rol->fetch()) $rol_id = $rol_found;
      $stmt_rol->close();
    }

    if ($rol_id <= 0) throw new Exception("No hay roles definidos para el juego.");

    $stmt_ins = $conn->prepare("INSERT INTO permiso_equipo (usu_id, equ_id, rol_id, per_modif_horario, per_enviar_scrim, per_elim_miembro) VALUES (?, ?, ?, 0, 0, 0)");
    $stmt_ins->bind_param("iii", $usu_id, $equ_id, $rol_id);
    $stmt_ins->execute();
    $stmt_ins->close();

    $stmt_del = $conn->prepare("DELETE FROM notificacion WHERE not_id = ?");
    $stmt_del->bind_param("i", $not_id);
    $stmt_del->execute();
    $stmt_del->close();

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
  $stmt_perm = $conn->prepare("SELECT per_elim_miembro FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?");
  $stmt_perm->bind_param("ii", $usu_id, $equ_id);
  $stmt_perm->execute();
  $stmt_perm->bind_result($puede_gestionar);
  if (!$stmt_perm->fetch()) {
    $stmt_perm->close();
    header("Location: ../profile/manage.php?id=$equ_id&error=no_permission");
    exit;
  }
  $stmt_perm->close();
  try {
    $stmt_check = $conn->prepare("SELECT per_id FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?");
    $stmt_check->bind_param("ii", $target_usu_id, $equ_id);
    $stmt_check->execute();
    if ($stmt_check->fetch()) {
      $stmt_check->close();
      $_SESSION['flash_error'] = "El usuario ya es miembro.";
      header("Location: ../profile/manage.php?id=$equ_id");
      exit;
    }
    $stmt_check->close();

    $stmt_info = $conn->prepare("SELECT equ_nombre FROM equipo WHERE equ_id = ?");
    $stmt_info->bind_param("i", $equ_id);
    $stmt_info->execute();
    $stmt_info->bind_result($equ_nombre);
    $stmt_info->fetch();
    $stmt_info->close();

    $not_tipo = 'INVITACION';
    $not_asunto = 'Invitación de Equipo';
    $not_mensaje = "Has sido invitado a unirte a $equ_nombre.";

    $stmt_notif = $conn->prepare("INSERT INTO notificacion (usu_id, equ_id, not_tipo, not_asunto, not_mensaje) VALUES (?, ?, ?, ?, ?)");
    $stmt_notif->bind_param("iisss", $target_usu_id, $equ_id, $not_tipo, $not_asunto, $not_mensaje);

    if ($stmt_notif->execute()) {
      $_SESSION['flash_msg'] = 'invited';
    } else {
      $_SESSION['flash_error'] = "Error al enviar invitación.";
    }
    $stmt_notif->close();

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
