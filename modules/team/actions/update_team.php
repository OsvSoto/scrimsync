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
$stmt_check = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt_check, "ii", $usu_id, $equ_id);
mysqli_stmt_execute($stmt_check);
$res_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($res_check) == 0) {
  $_SESSION['flash_error'] = 'no_permission';
  header("Location: ../profile/view.php?id=$equ_id");
  exit;
}

// Manejo de la Imagen (Logo)
require_once '../../functions/process_image.php';

$campo_logo = "";
$params_types = "ssi"; // nombre, jue_id, equ_id (default)
$params_values = [$nombre, $jue_id, $equ_id];

if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
  $uploadResult = uploadImage($_FILES['logo'], 'teams', 'team', $usu_id);

  if ($uploadResult['success']) {
    $ruta_bd = $uploadResult['path'];
    $campo_logo = ", equ_logo = ?";

    $params_types = "sssi";
    $params_values = [$nombre, $jue_id, $ruta_bd, $equ_id];

    $sql_old = "SELECT equ_logo FROM equipo WHERE equ_id = ?";
    $stmt_old = mysqli_prepare($conn, $sql_old);
    mysqli_stmt_bind_param($stmt_old, "i", $equ_id);
    mysqli_stmt_execute($stmt_old);
    mysqli_stmt_bind_result($stmt_old, $old_logo);
    if (mysqli_stmt_fetch($stmt_old)) {
      if ($old_logo && file_exists(__DIR__ . '/../../../' . $old_logo)) {
        unlink(__DIR__ . '/../../../' . $old_logo);
      }
    }
    mysqli_stmt_close($stmt_old);

  } else {
    $_SESSION['flash_error'] = $uploadResult['error'];
    header("Location: ../profile/manage.php?id=$equ_id");
    exit;
  }
}

// Actualizar BD
$sql_update = "UPDATE equipo SET
               equ_nombre = ?,
               jue_id = ?
               $campo_logo
               WHERE equ_id = ?";

$stmt_update = mysqli_prepare($conn, $sql_update);
mysqli_stmt_bind_param($stmt_update, $params_types, ...$params_values);

if (mysqli_stmt_execute($stmt_update)) {
  $_SESSION['flash_msg'] = 'updated';
  header("Location: ../profile/manage.php?id=$equ_id");
} else {
  $_SESSION['flash_error'] = 'db_error';
  $_SESSION['debug_error'] = mysqli_error($conn);
  header("Location: ../profile/manage.php?id=$equ_id");
}

