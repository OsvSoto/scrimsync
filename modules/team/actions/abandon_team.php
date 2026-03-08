<?php
//modules/team/actions/abandon_team.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
  header("Location: " . BASE_URL . "modules/auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../profile/view.php");
  exit;
}

$usu_id = $_SESSION['usu_id'];
$equ_id = isset($_POST['equ_id']) ? (int) $_POST['equ_id'] : 0;

$sql_perm = "SELECT per_elim_miembro as p FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
$res_perm = $conn->execute_query($sql_perm, [$usu_id, $equ_id]);
$perm_data = $res_perm->fetch_assoc();
$soy_capitan = $perm_data["p"];

$sql_count = "SELECT COUNT(*) as c FROM permiso_equipo WHERE equ_id = ?";
$res_count = $conn->execute_query($sql_count, [$equ_id]);
$count_data = $res_count->fetch_assoc();
$count_members = $count_data["c"];

if ($soy_capitan && $count_members > 1) {
  $new_captain_id = isset($_POST['new_captain_id']) ? (int)$_POST['new_captain_id'] : 0;

  if ($new_captain_id <= 0) {
    $_SESSION['flash_error'] = 'cant_quit';
    header("Location: ../profile/view.php?id=$equ_id");
    exit;
  }

  mysqli_begin_transaction($conn);
  try {
    $sql_transfer_owner = "UPDATE equipo SET usu_id = ? WHERE equ_id = ?";
    $conn->execute_query($sql_transfer_owner, [$new_captain_id, $equ_id]);

    $sql_update_perms = "UPDATE permiso_equipo SET per_modif_horario = 1, per_enviar_scrim = 1, per_elim_miembro = 1 WHERE usu_id = ? AND equ_id = ?";
    $conn->execute_query($sql_update_perms, [$new_captain_id, $equ_id]);

    $sql_delete_old = "DELETE FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
    $conn->execute_query($sql_delete_old, [$usu_id, $equ_id]);

    mysqli_commit($conn);
    $_SESSION['flash_msg'] = 'quit';
    header("Location: ../profile/index.php");
    exit;
  } catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
    exit;
  }
} else if ($soy_capitan && $count_members == 1) {

  $sql_info = "SELECT equ_logo FROM equipo WHERE equ_id = ?";
  $res_info = $conn->execute_query($sql_info, [$equ_id]);
  $equipo = $res_info->fetch_assoc();

  // borramos todo cuando se va el ultimo miembro
  $sql_delete = "DELETE FROM equipo WHERE equ_id = ?";
  if ($conn->execute_query($sql_delete, [$equ_id])) {
    if ($equipo && $equipo['equ_logo'] && file_exists(__DIR__ . '/../../../' . $equipo['equ_logo'])) {
      unlink(__DIR__ . '/../../../' . $equipo['equ_logo']);
    }
    $_SESSION['flash_msg'] = 'quit_delete';
    header("Location: ../profile/index.php");
  } else {
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
  }
} else if (!$soy_capitan) {

  $sql_delete_perm = "DELETE FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
  if ($conn->execute_query($sql_delete_perm, [$usu_id, $equ_id])) {
    $_SESSION['flash_msg'] = 'quit';
    header("Location: ../profile/index.php");
  } else {
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
  }
}

?>
