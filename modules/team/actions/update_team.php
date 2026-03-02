<?php
// modules/team/actions/update_team.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
  header("Location: " . BASE_URL . "modules/auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../profile/index.php");
  exit;
}

$equ_id = isset($_POST['equ_id']) ? (int)$_POST['equ_id'] : 0;
$nombre = trim($_POST['nombre']);
$jue_id = isset($_POST['jue_id']) ? (int)$_POST['jue_id'] : 0;
$usu_id = $_SESSION['usu_id'];

if ($equ_id <= 0) {
  $_SESSION['flash_error'] = 'invalid_request';
  header("Location: ../profile/index.php");
  exit;
}

// Verificar Permisos (Capitán)
$check_sql = "SELECT * FROM permiso_equipo
              WHERE usu_id = ? AND equ_id = ?
              AND (per_modif_horario = 1 OR per_elim_miembro = 1)";
$res_check = $conn->execute_query($check_sql, [$usu_id, $equ_id]);

if ($res_check->num_rows == 0) {
  $_SESSION['flash_error'] = 'no_permission';
  header("Location: ../profile/view.php?id=$equ_id");
  exit;
}

// Manejo de la Imagen (Logo)
require_once '../../functions/process_image.php';

$campo_logo = "";
$params = [$nombre, $jue_id];

if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
  $uploadResult = uploadImage($_FILES['logo'], 'teams', 'team', $usu_id);

  if ($uploadResult['success']) {
    $ruta_bd = $uploadResult['path'];
    $campo_logo = ", equ_logo = ?";
    $params[] = $ruta_bd;

    $sql_old = "SELECT equ_logo FROM equipo WHERE equ_id = ?";
    $res_old = $conn->execute_query($sql_old, [$equ_id]);
    if ($old_team = $res_old->fetch_assoc()) {
      $old_logo = $old_team['equ_logo'];
      if ($old_logo && file_exists(__DIR__ . '/../../../' . $old_logo)) {
        unlink(__DIR__ . '/../../../' . $old_logo);
      }
    }

  } else {
    $_SESSION['flash_error'] = $uploadResult['error'];
    header("Location: ../profile/manage.php?id=$equ_id");
    exit;
  }
}

$params[] = $equ_id;

// Actualizar BD
$sql_update = "UPDATE equipo SET
               equ_nombre = ?,
               jue_id = ?
               $campo_logo
               WHERE equ_id = ?";

if ($conn->execute_query($sql_update, $params)) {
  $_SESSION['flash_msg'] = 'updated';
  header("Location: ../profile/manage.php?id=$equ_id");
} else {
  $_SESSION['flash_error'] = 'db_error';
  $_SESSION['debug_error'] = $conn->error;
  header("Location: ../profile/manage.php?id=$equ_id");
}

