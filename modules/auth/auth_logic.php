<?php
// modules/auth/auth_logic.php
require_once '../../config/db.php';

// Necesita PHPMailer -> instalar con composer
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function Autenticar_Usuario($conn, $p_credencial) {
    $username = $p_credencial['username'];
    $password_input = $p_credencial['password'];

    $sql = "SELECT usu_id, usu_username, usu_password, usu_tipo, usu_alias FROM usuario WHERE usu_username = ?";
    $result = $conn->execute_query($sql, [$username]);
    if ($row = $result->fetch_assoc()) {
        $hash_bd = $row['usu_password'];
        if (password_verify($password_input, $hash_bd)) {
            return $row;
        }
    }
    return false;
}

function Registro_Usuario($conn, $p_usuario) {
    $username = $p_usuario['username'];
    $email    = $p_usuario['email'];
    $alias    = $p_usuario['alias'];
    $password = password_hash($p_usuario['password'], PASSWORD_BCRYPT);
    $tipo      = 1;

    $sql = "INSERT INTO usuario (usu_username, usu_password, usu_email, usu_alias, usu_tipo) VALUES (?, ?, ?, ?, ?)";
    return $conn->execute_query($sql, [$username, $password, $email, $alias, $tipo]);
}

function Generar_String_Aleatorio($length = 8) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function Enviar_Email_SMTP($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = getenv('SMTP_HOST');
      $mail->Port = getenv('SMTP_PORT');
      $mail->Username = getenv('SMTP_USER');
      $mail->Password = getenv('SMTP_PASS');

      if(!empty($mail->Password)){
        $mail->SMTPAuth = true;

        if ($mail->Port==587){
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        elseif($mail->Port==465){
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }
      } else {
        $mail->SMTPAuth = false;
        $mail->SMTPSecure = false;
      }

      $mail->setFrom(getenv('SMTP_FROM'), 'ScrimSync');
      $mail->addAddress($to);

      $mail->isHTML(true);
      $mail->CharSet = 'UTF-8';
      $mail->Subject = $subject;
      $mail->Body = $message;

      $mail->AltBody = strip_tags($message);
      $mail->send();
      return true;
  } catch (Exception $e) {
    error_log("Error enviando correo: {$mail->ErrorInfo}");
    return false;
  }
}

function Recuperacion_Credenciales($conn, $p_correo_recup) {
    $sql = "SELECT usu_id, usu_username FROM usuario WHERE usu_email = ?";
    $result = $conn->execute_query($sql, [$p_correo_recup]);
    if ($row = $result->fetch_assoc()) {
        $id_usuario = $row['usu_id'];
        $username = $row['usu_username'];

        $pass_temporal = Generar_String_Aleatorio(8);
        $hash_temporal = password_hash($pass_temporal, PASSWORD_BCRYPT);

        $update_sql = "UPDATE usuario SET usu_password = ? WHERE usu_id = ?";
        if ($conn->execute_query($update_sql, [$hash_temporal, $id_usuario])) {
            $asunto = "Recuperación de Acceso";
            $mensaje = "
            <div style='font-family: sans-serif; padding: 20px; border: 1px solid #ddd;'>
                <h2>Hola $username,</h2>
                <p>Has solicitado recuperar tu contraseña en <strong>ScrimSync</strong>.</p>
                <p>Tu nueva contraseña temporal es:</p>
                <h3 style='background-color: #f4f4f5; padding: 10px; display: inline-block;'>$pass_temporal</h3>
                <p>Por favor, inicia sesión y cámbiala lo antes posible.</p>
            </div>";
            return Enviar_Email_SMTP($p_correo_recup, $asunto, $mensaje);
        }
    }
    return false;
}
?>
