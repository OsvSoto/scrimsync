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

if ($equ_id <= 0) {
  header("Location: ../profile/index.php");
  exit;
}

$sql_perm = "SELECT per_elim_miembro, per_modif_horario FROM permiso_equipo
             WHERE usu_id = ? AND equ_id = ?";
$stmt_perm = $conn->execute_query($sql_perm, [$usu_id, $equ_id]);
$perm = $stmt_perm->fetch_assoc();

if (!$perm || ($perm['per_elim_miembro'] != 1 && $perm['per_modif_horario'] != 1)) {
  $_SESSION['flash_error'] = 'no_permission';
  header("Location: ../profile/view.php?id=$equ_id");
  exit;
}

$sql_img = "SELECT equ_logo FROM equipo WHERE equ_id = ?";
$stmt_img = $conn->execute_query($sql_img, [$equ_id]);
$equipo = $stmt_img->fetch_assoc();

$sql_delete = "DELETE FROM equipo WHERE equ_id = ?";
$stmt_delete = $conn->execute_query($sql_delete, [$equ_id]);

if ($stmt_delete) {
  if ($equipo['equ_logo'] && file_exists(__DIR__ . '/../../../' . $equipo['equ_logo'])) {

    unlink(__DIR__ . '/../../../' . $equipo['equ_logo']);
  }
  $_SESSION['flash_msg'] = 'team_deleted';
  header("Location: ../profile/index.php");
} else {

  $_SESSION['flash_error'] = 'db_error';
  header("Location: ../profile/view.php?id=$equ_id");
}
