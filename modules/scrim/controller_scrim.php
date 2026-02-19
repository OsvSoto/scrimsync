<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /*
     * NOTE: necesito usu_id: para saber quien envia el reto
     * Pero como un usuario puede ser capitan de muchos equipos, necesito
     * equ_id (desde emisor)
     */
    $usu_id = $_SESSION['usu_id'];
    $action = $_POST['action'];
    switch ($action) {
        case 'enviar':
            $equ_id_emisor = $_POST['equ_id_emisor'];
            $equ_id_receptor = $_POST['equ_id_receptor'];
            $dis_id = $_POST['dis_id'];

            $sql_validate = '
            SELECT e1.jue_id as jue1, e2.jue_id as jue2, e1.equ_nombre as nom1, e2.equ_nombre as nom2, e2.usu_id as capitan_receptor
            FROM equipo e1
            JOIN equipo e2 ON e2.equ_id = ?
            WHERE e1.equ_id = ?
            ';
            $res_validate = $conn->execute_query($sql_validate, [$equ_id_receptor, $equ_id_emisor]);
            $teams_info = $res_validate->fetch_assoc();

            if (!$teams_info || $teams_info['jue1'] !== $teams_info['jue2']) {
                $_SESSION['flash_error'] = 'invalid_game';
                header('Location: index.php');
                exit;
            }

            $sql_disp = 'SELECT dis_dia_semana, dis_hora_inicio, dis_hora_fin FROM disponibilidad WHERE dis_id = ? AND equ_id = ?';
            $res_disp = $conn->execute_query($sql_disp, [$dis_id, $equ_id_receptor]);
            $disp = $res_disp->fetch_assoc();

            if ($disp) {
                $sql_emisor_disp = '
                SELECT COUNT(*) as coincidencia
                FROM disponibilidad
                WHERE equ_id = ?
                AND dis_dia_semana = ?
                AND dis_hora_inicio <= ?
                AND dis_hora_fin >= ?
                ';
                $res_emisor_disp = $conn->execute_query($sql_emisor_disp, [
                    $equ_id_emisor,
                    $disp['dis_dia_semana'],
                    $disp['dis_hora_inicio'],
                    $disp['dis_hora_fin']
                ]);
                $emisor_ok = $res_emisor_disp->fetch_assoc()['coincidencia'] > 0;

                if (!$emisor_ok) {
                    $_SESSION['flash_error'] = 'mutual_error';
                    header('Location: index.php');
                    exit;
                }

                // Calcular la próxima fecha para ese día de la semana
                $days = [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday'
                ];
                $day_name = $days[$disp['dis_dia_semana']];
                $fecha_juego = date('Y-m-d', strtotime("next $day_name"));

                // Insertar el scrim
                $sql_scrim = 'INSERT INTO scrim (equ_id_emisor, equ_id_receptor, est_id, scr_fecha_juego, scr_hora_inicio, scr_hora_fin)
                      VALUES (?, ?, ?, ?, ?, ?)';
                $conn->execute_query($sql_scrim, [
                    $equ_id_emisor,
                    $equ_id_receptor,
                    1,  // 1-> Pendiente, 2->Aceptado, 3->Cancelado
                    $fecha_juego,
                    $disp['dis_hora_inicio'],
                    $disp['dis_hora_fin']
                ]);

                $scr_id = $conn->insert_id;

                // Notificar al capitán del equipo receptor
                $asunto = 'Nueva Solicitud de Scrim';
                $mensaje = 'El equipo ' . $teams_info['nom1'] . ' ha solicitado un scrim contra ' . $teams_info['nom2'] . ' para el ' . $fecha_juego . ' a las ' . $disp['dis_hora_inicio'];

                $sql_notif = "INSERT INTO notificacion (usu_id, equ_id, scr_id, not_tipo, not_asunto, not_mensaje)
                      VALUES (?, ?, ?, 'SCRIM', ?, ?)";
                $conn->execute_query($sql_notif, [
                    $teams_info['capitan_receptor'],
                    $equ_id_receptor,
                    $scr_id,
                    $asunto,
                    $mensaje
                ]);

                $_SESSION['flash_msg'] = 'scrim_sent';
                header('Location: index.php');
            } else {
                $_SESSION['flash_error'] = 'invalid_availability';
                header('Location: index.php');
            }
            exit;
            break;

        case 'accept_scrim':
            $not_id = isset($_POST['not_id']) ? (int) $_POST['not_id'] : 0;
            $scr_id = isset($_POST['scr_id']) ? (int) $_POST['scr_id'] : 0;

            if ($not_id > 0 && $scr_id > 0) {
                mysqli_begin_transaction($conn);
                try {
                    // Actualizar estado del scrim
                    $est_id = 2; // 2 -> Aceptado
                    $stmt_scr = $conn->prepare('UPDATE scrim SET est_id = ? WHERE scr_id = ?');
                    $stmt_scr->bind_param('ii', $est_id, $scr_id);
                    $stmt_scr->execute();
                    $stmt_scr->close();

                    // Eliminar la notificación
                    $stmt_not = $conn->prepare('DELETE FROM notificacion WHERE not_id = ? AND usu_id = ?');
                    $stmt_not->bind_param('ii', $not_id, $usu_id);
                    $stmt_not->execute();
                    $stmt_not->close();

                    mysqli_commit($conn);
                    $_SESSION['flash_msg'] = 'Scrim aceptado correctamente.';
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $_SESSION['flash_error'] = 'Error al aceptar el scrim.';
                }
            }
            header('Location: ../user/notification/index.php');
            exit;
            break;

        case 'reject_scrim':
            $not_id = isset($_POST['not_id']) ? (int) $_POST['not_id'] : 0;
            $scr_id = isset($_POST['scr_id']) ? (int) $_POST['scr_id'] : 0;

            if ($not_id > 0 && $scr_id > 0) {
                mysqli_begin_transaction($conn);
                try {
                    // Eliminar el scrim
                    $stmt_scr = $conn->prepare('DELETE FROM scrim WHERE scr_id = ?');
                    $stmt_scr->bind_param('i', $scr_id);
                    $stmt_scr->execute();
                    $stmt_scr->close();

                    // Eliminar la notificación
                    $stmt_not = $conn->prepare('DELETE FROM notificacion WHERE not_id = ? AND usu_id = ?');
                    $stmt_not->bind_param('ii', $not_id, $usu_id);
                    $stmt_not->execute();
                    $stmt_not->close();

                    mysqli_commit($conn);
                    $_SESSION['flash_msg'] = 'Scrim rechazado y eliminado.';
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $_SESSION['flash_error'] = 'Error al rechazar el scrim.';
                }
            }
            header('Location: ../user/notification/index.php');
            exit;
            break;

        case 'cancelar':
            $scr_id = $_POST['scr_id'] ?? 0;
            if ($scr_id > 0) {
                // Obtener info del scrim y validar permisos
                $sql_get = "
                    SELECT s.*,
                           e1.equ_nombre as emisor_nombre, e1.usu_id as capitan_emisor,
                           e2.equ_nombre as receptor_nombre, e2.usu_id as capitan_receptor
                    FROM scrim s
                    JOIN equipo e1 ON s.equ_id_emisor = e1.equ_id
                    JOIN equipo e2 ON s.equ_id_receptor = e2.equ_id
                    WHERE s.scr_id = ?
                ";
                $res = $conn->execute_query($sql_get, [$scr_id]);
                $scrim = $res->fetch_assoc();

                if ($scrim) {
                    // Verificar si el usuario es el capitán de uno de los dos equipos
                    // O si tiene permisos en permiso_equipo
                    $is_allowed = false;
                    $sql_perm = "SELECT per_enviar_scrim FROM permiso_equipo WHERE usu_id = ? AND equ_id IN (?, ?)";
                    $res_perm = $conn->execute_query($sql_perm, [$usu_id, $scrim['equ_id_emisor'], $scrim['equ_id_receptor']]);
                    $perm = $res_perm->fetch_assoc();

                    if ($scrim['capitan_emisor'] == $usu_id || $scrim['capitan_receptor'] == $usu_id || ($perm && $perm['per_enviar_scrim'] == 1)) {
                        $is_allowed = true;
                    }

                    if ($is_allowed) {
                        // Actualizar a estado 3 (Cancelado)
                        $sql_update = "UPDATE scrim SET est_id = 3 WHERE scr_id = ?";
                        $conn->execute_query($sql_update, [$scr_id]);

                        // Determinar quién canceló para el mensaje y a quién notificar
                        $soy_emisor = false;
                        if ($scrim['capitan_emisor'] == $usu_id) {
                            $soy_emisor = true;
                        } else {
                            $check_emisor = $conn->execute_query("SELECT 1 FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?", [$usu_id, $scrim['equ_id_emisor']]);
                            if ($check_emisor->num_rows > 0) $soy_emisor = true;
                        }

                        if ($soy_emisor) {
                            $cancelador_nombre = $scrim['emisor_nombre'];
                            $destinatario_id = $scrim['capitan_receptor'];
                            $equ_id_noti = $scrim['equ_id_receptor'];
                        } else {
                            $cancelador_nombre = $scrim['receptor_nombre'];
                            $destinatario_id = $scrim['capitan_emisor'];
                            $equ_id_noti = $scrim['equ_id_emisor'];
                        }

                        $asunto = 'Scrim Cancelado';
                        $mensaje = 'El equipo ' . $cancelador_nombre . ' ha cancelado el scrim programado para el ' . $scrim['scr_fecha_juego'];

                        $sql_notif = "INSERT INTO notificacion (usu_id, equ_id, scr_id, not_tipo, not_asunto, not_mensaje)
                                      VALUES (?, ?, ?, 'SISTEMA', ?, ?)";
                        $conn->execute_query($sql_notif, [$destinatario_id, $equ_id_noti, $scr_id, $asunto, $mensaje]);

                        $_SESSION['flash_msg'] = 'scrim_cancelled';
                    } else {
                        $_SESSION['flash_error'] = 'not_authorized';
                    }
                }
            }
            header('Location: ../user/calendar/index.php');
            exit;
            break;

        default:
            header('Location: index.php');
            break;
    }
}
