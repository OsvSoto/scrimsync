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
$target_usu_id = isset($_POST['target_usu_id']) ? (int)$_POST['target_usu_id'] : 0;
$rol_id = isset($_POST['rol_id']) ? (int)$_POST['rol_id'] : 0;

if ($equ_id <= 0 || $target_usu_id <= 0 || $rol_id <= 0) {
  die("Datos inválidos.");
}

// Verificar permisos (per_enviar_scrim=1 como solicitado)
$sql_perm = "SELECT per_enviar_scrim FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
$res_perm = $conn->execute_query($sql_perm, [$usu_id, $equ_id]);
$perm = $res_perm->fetch_assoc();

if (!$perm || $perm['per_enviar_scrim'] != 1) {
  die("No tienes permisos para asignar roles.");
}

// Actualizar rol
$sql_update = "UPDATE permiso_equipo SET rol_id = ? WHERE usu_id = ? AND equ_id = ?";
if ($conn->execute_query($sql_update, [$rol_id, $target_usu_id, $equ_id])) {
  $_SESSION['flash_msg'] = 'role_assigned';
  header("Location: ../profile/view.php?id=$equ_id");
} else {
  $_SESSION['flash_error'] = 'db_error';
  header("Location: ../profile/view.php?id=$equ_id");
}
