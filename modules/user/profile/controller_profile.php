<?php
// modules/user/profile/controller_profile.php
session_start();
require_once '../../../config/db.php';

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
            $fileTmpPath = $_FILES['usu_foto']['tmp_name'];
            $fileSize    = $_FILES['usu_foto']['size'];

            $maxFileSize = 5 * 1024 * 1024;
            if ($fileSize > $maxFileSize) {
              header("Location: index.php?error=file_too_large");
              exit;
            }

            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detectedMimeType = $finfo->file($fileTmpPath);

            if (!in_array($detectedMimeType, $allowedMimeTypes)) {
              header("Location: index.php?error=invalid_file_type");
              exit;
            }

            try {
              // Obtener foto anterior para eliminarla si es necesario
              $sql_get_old = "SELECT usu_foto FROM usuario WHERE usu_id = ?";
              $stmt_old = mysqli_prepare($conn, $sql_get_old);
              mysqli_stmt_bind_param($stmt_old, "i", $usu_id);
              mysqli_stmt_execute($stmt_old);
              mysqli_stmt_bind_result($stmt_old, $old_photo_url);
              mysqli_stmt_fetch($stmt_old);
              mysqli_stmt_close($stmt_old);

              $baseDir = $_SERVER['DOCUMENT_ROOT'];
              $uploadDir = $baseDir . '/uploads/profiles/';
              if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
              }

              $newFileName = 'profile_' . $usu_id . '_' . bin2hex(random_bytes(8)) . '.jpg';
              $destPath = $uploadDir . $newFileName;

              // Procesamiento Imagick
              $image = new Imagick($fileTmpPath);
              $image->setImageFormat('jpeg');
              $image->setImageCompressionQuality(85);
              $image->cropThumbnailImage(300, 300);
              $image->writeImage($destPath);
              $image->clear();
              $image->destroy();

              $webPath = BASE_URL . 'uploads/profiles/' . $newFileName;
              $sql_photo = "UPDATE usuario SET usu_foto = ? WHERE usu_id = ?";
              $stmt_photo = mysqli_prepare($conn, $sql_photo);
              mysqli_stmt_bind_param($stmt_photo, "si", $webPath, $usu_id);

              if (mysqli_stmt_execute($stmt_photo)) {
                // Eliminar foto anterior si existe y es local
                if (!empty($old_photo_url) && strpos($old_photo_url, BASE_URL) === 0) {
                  $oldFileName = basename($old_photo_url);
                  $oldFilePath = $uploadDir . $oldFileName;
                  if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    unlink($oldFilePath);
                  }
                }
              }
              mysqli_stmt_close($stmt_photo);
            } catch (Exception $e) {
              error_log("Error Imagick: " . $e->getMessage());
              header("Location: index.php?error=image_processing_error");
              exit;
            }
          }

          header("Location: index.php?msg=profile_updated");
        } else {
          header("Location: index.php?error=db_error");
        }
        mysqli_stmt_close($stmt);
      }
      break;

    case 'update_password':
      $current_password = $_POST['current_password'];
      $new_password = $_POST['new_password'];
      $confirm_password = $_POST['confirm_password'];

      if ($new_password !== $confirm_password) {
        header("Location: index.php?error=password_mismatch");
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
              header("Location: index.php?msg=password_updated");
            } else {
              header("Location: index.php?error=db_error");
            }
            mysqli_stmt_close($stmt_upd);
          }
        } else {
          header("Location: index.php?error=invalid_current_password");
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
