<?php
// modules/team/actions/create_teams.php
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

$usu_id = $_SESSION['usu_id'];
$nombre = trim($_POST['nombre']);
$jue_id = isset($_POST['jue_id']) ? (int)$_POST['jue_id'] : 0;

if (empty($nombre)) {
    $_SESSION['flash_error'] = 'name_required';
    header("Location: ../profile/index.php?view=create");
    exit;
}
if ($jue_id <= 0) {
    $_SESSION['flash_error'] = 'game_required';
    header("Location: ../profile/index.php?view=create");
    exit;
}

require_once '../../functions/process_image.php';

$ruta_logo = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $uploadResult = uploadImage($_FILES['logo'], 'teams', 'team', $usu_id);

    if ($uploadResult['success']) {
        $ruta_logo = $uploadResult['path'];
    } else {
        $_SESSION['flash_error'] = $uploadResult['error'];
        header("Location: ../profile/index.php?view=create");
        exit;
    }
}

mysqli_begin_transaction($conn);

try {
  // Insertar el Equipo
  $sql_equipo = "INSERT INTO equipo (usu_id, jue_id, equ_nombre, equ_logo) VALUES (?, ?, ?, ?)";
  if (!$conn->execute_query($sql_equipo, [$usu_id, $jue_id, $nombre, $ruta_logo])) {
    throw new Exception("Error al crear el equipo: " . $conn->error);
  }

  $nuevo_equ_id = $conn->insert_id;

  $sql_rol = "SELECT rol_id FROM rol_predefinido WHERE jue_id = ? LIMIT 1";
  $res_rol = $conn->execute_query($sql_rol, [$jue_id]);
  $rol_data = $res_rol->fetch_assoc();
  $rol_id = $rol_data['rol_id'];

  $sql_permisos = "INSERT INTO permiso_equipo
                    (usu_id, equ_id, rol_id, per_modif_horario, per_enviar_scrim, per_elim_miembro)
                    VALUES (?, ?, ?, ?, ?, ?)";
  $permiso_si = 1; // 1 = Verdadero

  if (!$conn->execute_query($sql_permisos, [$usu_id, $nuevo_equ_id, $rol_id, $permiso_si, $permiso_si, $permiso_si])) {
    throw new Exception("Error al asignar permisos de capitán.");
  }
  mysqli_commit($conn);

  // Redirigimos al dashboard
  $_SESSION['flash_msg'] = 'team_created';
  header("Location: ../profile/index.php");
  exit;
} catch (Exception $e) {
  // SI ALGO FALLA: Deshacemos todo
  mysqli_rollback($conn);

  // Borramos la imagen si se subió
  if ($ruta_logo && file_exists(__DIR__ . '/../../../' . $ruta_logo)) {
    unlink(__DIR__ . '/../../../' . $ruta_logo);
  }

  $_SESSION['flash_error'] = 'db_error';
  $_SESSION['debug_error'] = $e->getMessage();
  header("Location: ../profile/index.php?view=create");
  exit;
}

