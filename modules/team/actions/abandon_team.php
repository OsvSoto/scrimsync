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

$stmt_perm = $conn->prepare("SELECT per_elim_miembro as p FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?");
$stmt_perm->bind_param("ii", $usu_id, $equ_id);
$stmt_perm->execute();
$soy_capitan = $stmt_perm->get_result()->fetch_assoc()["p"];

$stmt_count = $conn->prepare("SELECT COUNT(*) as c FROM permiso_equipo WHERE equ_id = ?");
$stmt_count->bind_param("i", $equ_id);
$stmt_count->execute();
$count_members = $stmt_count->get_result()->fetch_assoc()["c"];

if ($soy_capitan && $count_members > 1) {

  // TODO: asignar miembro como capitan antes de salir?
  $_SESSION['flash_error'] = 'cant_quit';
  header("Location: ../profile/view.php");
} else if ($soy_capitan && $count_members == 1) {

  // Borramos el equipo cuando queda un solo miembro (capitan)
  $stmt_delete = $conn->prepare("DELETE FROM equipo WHERE equ_id = ?");
  $stmt_delete->bind_param("i", $equ_id);
  if ($stmt_delete->execute()) {
    $_SESSION['flash_msg'] = 'quit_delete';
    header("Location: ../profile/index.php");
  } else {
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
  }
} else if (!$soy_capitan) {


  $stmt_delete = $conn->prepare("DELETE FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?");
  $stmt_delete->bind_param("ii", $usu_id, $equ_id);
  if ($stmt_delete->execute()) {
    $_SESSION['flash_msg'] = 'quit';
    header("Location: ../profile/index.php");
  } else {
    $_SESSION['flash_error'] = 'db_error';
    header("Location: ../profile/view.php?id=$equ_id");
  }
}

?>
