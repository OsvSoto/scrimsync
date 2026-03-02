<?php
session_start();
require_once '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../profile/index.php");
  exit;
}

$equ_id = isset($_POST['equ_id']) ? (int)$_POST['equ_id'] : 0;
$usu_id = $_SESSION['usu_id'] ?? 0;
$action = $_POST['action'] ?? '';

if ($equ_id <= 0 || $usu_id <= 0) {
  $_SESSION['flash_error'] = 'db_error';
  header("Location: ../profile/index.php");
  exit;
}

$sql_perm = "SELECT per_modif_horario FROM permiso_equipo
             WHERE usu_id = ? AND equ_id = ? LIMIT 1";
$res_perm = $conn->execute_query($sql_perm, [$usu_id, $equ_id]);
$perm = $res_perm->fetch_assoc();

if (!$perm || $perm['per_modif_horario'] != 1) {
  $_SESSION['flash_error'] = 'no_permission';
  header("Location: ../profile/view.php?id=$equ_id");
  exit;
}

if ($action === 'add') {
  $dia = (int)$_POST['day']; // 1=Lunes, 7=Domingo
  $hora_inicio = $_POST['start_time'];
  $hora_fin = $_POST['end_time'];

  if ($dia < 1 || $dia > 7 || empty($hora_inicio) || empty($hora_fin)) {
    $_SESSION['flash_error'] = 'invalid_input';
    header("Location: ../profile/view.php?id=$equ_id");
    exit;
  }

  if ($hora_inicio >= $hora_fin) {
    $_SESSION['flash_error'] = 'invalid_time_range';
    header("Location: ../profile/view.php?id=$equ_id");
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO disponibilidad (equ_id, dis_dia_semana, dis_hora_inicio, dis_hora_fin) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iiss", $equ_id, $dia, $hora_inicio, $hora_fin);

  if ($stmt->execute()) {
    $_SESSION['flash_msg'] = 'availability_added';
  } else {
    $_SESSION['flash_error'] = 'db_error';
  }
  $stmt->close();
} elseif ($action === 'delete') {
  $dis_id = (int)$_POST['dis_id'];

  $stmt = $conn->prepare("DELETE FROM disponibilidad WHERE dis_id = ? AND equ_id = ?");
  $stmt->bind_param("ii", $dis_id, $equ_id);

  if ($stmt->execute()) {
    $_SESSION['flash_msg'] = 'availability_deleted';
  } else {
    $_SESSION['flash_error'] = 'db_error';
  }
  $stmt->close();
}

header("Location: ../profile/view.php?id=$equ_id");
exit;
