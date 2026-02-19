<?php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['usu_id'])) {
    header('Location: ../../../modules/auth/login.php');
    exit;
}

$usu_id = $_SESSION['usu_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark_all_read') {
        $stmt = $conn->prepare('UPDATE notificacion SET not_estado_leido = 1 WHERE usu_id = ?');
        $stmt->bind_param('i', $usu_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($_POST['action'] === 'mark_as_read') {
        $not_id = isset($_POST['not_id']) ? (int) $_POST['not_id'] : 0;
        if ($not_id > 0) {
            $stmt = $conn->prepare('UPDATE notificacion SET not_estado_leido = 1 WHERE usu_id = ? AND not_id = ?');
            $stmt->bind_param('ii', $usu_id, $not_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($_POST['action'] === 'reject_invite') {
        $not_id = isset($_POST['not_id']) ? (int) $_POST['not_id'] : 0;

        if ($not_id > 0) {
            $stmt = $conn->prepare('DELETE FROM notificacion WHERE not_id = ? AND usu_id = ?');
            $stmt->bind_param('ii', $not_id, $usu_id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $_SESSION['flash_msg'] = 'Invitación rechazada.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo rechazar la invitación.';
            }
            $stmt->close();
        }
    } elseif ($_POST['action'] === 'clear_notifications') {
        $stmt = $conn->prepare('DELETE FROM notificacion WHERE usu_id = ?');
        $stmt->bind_param('i', $usu_id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: index.php');
exit;
