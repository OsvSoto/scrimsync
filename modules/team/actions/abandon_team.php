<?php
//modules/team/actions/abandon_team.php
session_start();
require_once '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../profile/view.php");
  exit;
}

$usu_id = isset($_POST['usu_id']) ? (int) $_POST['usu_id'] : 0;
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

  // TODO: asignar miembro como capitan antes de salir?
  $_SESSION['flash_error'] = 'cant_quit';
  header("Location: ../profile/view.php");
} else if ($soy_capitan && $count_members == 1) {

  // Borramos el equipo cuando queda un solo miembro (capitan)
  $sql_delete = "DELETE FROM equipo WHERE equ_id = ?";
  if ($conn->execute_query($sql_delete, [$equ_id])) {
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
