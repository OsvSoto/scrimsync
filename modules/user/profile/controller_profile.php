<?php
// modules/user/profile/controller_profile.php
session_start();
require_once '../../../config/db.php';
require_once '../../functions/process_image.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['usu_id'])) {
  $usu_id = $_SESSION['usu_id'];
  $action = $_POST['action'];

  switch ($action) {
    case 'update_profile':
      $username = trim($_POST['usu_username']);
      $alias = trim($_POST['usu_alias']);
      $email = trim($_POST['usu_email']);
      $descripcion = trim($_POST['usu_descripcion']);

      $sql = "UPDATE usuario SET usu_username = ?, usu_alias = ?, usu_email = ?, usu_descripcion = ? WHERE usu_id = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $alias, $email, $descripcion, $usu_id);
        if (mysqli_stmt_execute($stmt)) {
          $_SESSION['username'] = $username;
          $_SESSION['alias'] = $alias;

          if (isset($_FILES['usu_foto']) && $_FILES['usu_foto']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['usu_foto'], 'profiles', 'profile', $usu_id);

            if ($uploadResult['success']) {
              // Obtener foto anterior para eliminarla si es necesario
              $sql_get_old = "SELECT usu_foto FROM usuario WHERE usu_id = ?";
              $stmt_old = mysqli_prepare($conn, $sql_get_old);
              mysqli_stmt_bind_param($stmt_old, "i", $usu_id);
              mysqli_stmt_execute($stmt_old);
              mysqli_stmt_bind_result($stmt_old, $old_photo_url);
              mysqli_stmt_fetch($stmt_old);
              mysqli_stmt_close($stmt_old);

              $webPath = '../../../' . $uploadResult['path'];

              $sql_photo = "UPDATE usuario SET usu_foto = ? WHERE usu_id = ?";
              $stmt_photo = mysqli_prepare($conn, $sql_photo);
              mysqli_stmt_bind_param($stmt_photo, "si", $webPath, $usu_id);

              if (mysqli_stmt_execute($stmt_photo)) {
                // Eliminar foto anterior si existe
                if (!empty($old_photo_url)) {
                  $oldFileName = basename($old_photo_url);
                  $oldFilePath = __DIR__ . '/../../../uploads/profiles/' . $oldFileName;
                  if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    unlink($oldFilePath);
                  }
                }
              }
              mysqli_stmt_close($stmt_photo);
            } else {
              $_SESSION['flash_error'] = $uploadResult['error'];
              header("Location: index.php");
              exit;
            }
          }
          $_SESSION['flash_msg'] = 'profile_updated';
          header("Location: index.php");
        } else {
          $_SESSION['flash_error'] = 'db_error';
          header("Location: index.php");
        }
        mysqli_stmt_close($stmt);
      }
      break;

    case 'update_password':
      $current_password = $_POST['current_password'];
      $new_password = $_POST['new_password'];
      $confirm_password = $_POST['confirm_password'];

      if ($new_password !== $confirm_password) {
        $_SESSION['flash_error'] = 'password_mismatch';
        header("Location: index.php");
        exit;
      }

      // Verificar contraseña actual
      $sql = "SELECT usu_password FROM usuario WHERE usu_id = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $usu_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (password_verify($current_password, $user['usu_password'])) {
          $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
          $sql_upd = "UPDATE usuario SET usu_password = ? WHERE usu_id = ?";
          if ($stmt_upd = mysqli_prepare($conn, $sql_upd)) {
            mysqli_stmt_bind_param($stmt_upd, "si", $hashed_password, $usu_id);
            if (mysqli_stmt_execute($stmt_upd)) {
              $_SESSION['flash_msg'] = 'password_updated';
              header("Location: index.php");
            } else {
              $_SESSION['flash_error'] = 'db_error';
              header("Location: index.php");
            }
            mysqli_stmt_close($stmt_upd);
          }
        } else {
          $_SESSION['flash_error'] = 'invalid_current_password';
          header("Location: index.php");
        }
      }
      break;

    default:
      header("Location: index.php");
      break;
  }
  mysqli_close($conn);
} else {
  header("Location: index.php");
}
