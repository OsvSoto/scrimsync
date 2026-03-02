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
        $conn->execute_query('UPDATE notificacion SET not_estado_leido = 1 WHERE usu_id = ?', [$usu_id]);
    } elseif ($_POST['action'] === 'mark_as_read') {
        $not_id = isset($_POST['not_id']) ? (int) $_POST['not_id'] : 0;
        if ($not_id > 0) {
            $conn->execute_query('UPDATE notificacion SET not_estado_leido = 1 WHERE usu_id = ? AND not_id = ?', [$usu_id, $not_id]);
        }
    } elseif ($_POST['action'] === 'reject_invite') {
        $not_id = isset($_POST['not_id']) ? (int) $_POST['not_id'] : 0;

        if ($not_id > 0) {
            if ($conn->execute_query('DELETE FROM notificacion WHERE not_id = ? AND usu_id = ?', [$not_id, $usu_id])) {
                $_SESSION['flash_msg'] = 'Invitación rechazada.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo rechazar la invitación.';
            }
        }
    } elseif ($_POST['action'] === 'clear_notifications') {
        $conn->execute_query('DELETE FROM notificacion WHERE usu_id = ?', [$usu_id]);
    }
}

header('Location: index.php');
exit;
