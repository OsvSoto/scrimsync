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
      if ($conn->execute_query($sql, [$username, $alias, $email, $descripcion, $usu_id])) {
          $_SESSION['username'] = $username;
          $_SESSION['alias'] = $alias;

          if (isset($_FILES['usu_foto']) && $_FILES['usu_foto']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['usu_foto'], 'profiles', 'profile', $usu_id);

            if ($uploadResult['success']) {
              // Obtener foto anterior para eliminarla
              $sql_get_old = "SELECT usu_foto FROM usuario WHERE usu_id = ?";
              $old_user = $conn->execute_query($sql_get_old, [$usu_id])->fetch_assoc();
              $old_photo_url = $old_user['usu_foto'] ?? '';

              $webPath = '../../../' . $uploadResult['path'];

              $sql_photo = "UPDATE usuario SET usu_foto = ? WHERE usu_id = ?";
              if ($conn->execute_query($sql_photo, [$webPath, $usu_id])) {
                // Eliminar foto anterior si existe
                if (!empty($old_photo_url)) {
                  $oldFileName = basename($old_photo_url);
                  $oldFilePath = __DIR__ . '/../../../uploads/profiles/' . $oldFileName;
                  if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    unlink($oldFilePath);
                  }
                }
              }
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
      $user = $conn->execute_query($sql, [$usu_id])->fetch_assoc();

      if ($user && password_verify($current_password, $user['usu_password'])) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $sql_upd = "UPDATE usuario SET usu_password = ? WHERE usu_id = ?";
        if ($conn->execute_query($sql_upd, [$hashed_password, $usu_id])) {
          $_SESSION['flash_msg'] = 'password_updated';
          header("Location: index.php");
        } else {
          $_SESSION['flash_error'] = 'db_error';
          header("Location: index.php");
        }
      } else {
        $_SESSION['flash_error'] = 'invalid_current_password';
        header("Location: index.php");
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
