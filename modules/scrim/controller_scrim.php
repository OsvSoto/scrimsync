<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

                // buscar si ya hay un scrim aceptado o una soli pendiente entre los 2 equipos en este bloque horario
                $sql_check = "SELECT est_id, equ_id_emisor, equ_id_receptor FROM scrim
                              WHERE (equ_id_emisor IN (?, ?) OR equ_id_receptor IN (?, ?))
                              AND scr_fecha_juego = ?
                              AND (scr_hora_inicio < ? AND scr_hora_fin > ?)
                              AND est_id IN (1, 2)";
                $res_check = $conn->execute_query($sql_check, [
                    $equ_id_emisor, $equ_id_receptor,
                    $equ_id_emisor, $equ_id_receptor,
                    $fecha_juego,
                    $disp['dis_hora_fin'],
                    $disp['dis_hora_inicio']
                ]);

                while ($row = $res_check->fetch_assoc()) {
                    if ($row['est_id'] == 2) { // scrim aceptado
                        $_SESSION['flash_error'] = 'slot_taken';
                        header('Location: index.php');
                        exit;
                    }
                    if ($row['est_id'] == 1 && $row['equ_id_emisor'] == $equ_id_emisor && $row['equ_id_receptor'] == $equ_id_receptor) {
                        $_SESSION['flash_error'] = 'scrim_exists'; // scrim pendiente
                        header('Location: index.php');
                        exit;
                    }
                }

                $sql_scrim = 'INSERT INTO scrim (equ_id_emisor, equ_id_receptor, est_id, scr_fecha_juego, scr_hora_inicio, scr_hora_fin)
                      VALUES (?, ?, ?, ?, ?, ?)';
                $conn->execute_query($sql_scrim, [
                    $equ_id_emisor,
                    $equ_id_receptor,
                    1, // 1-> Pendiente, 2->Aceptado, 3->Cancelado
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
            }
            else {
                $_SESSION['flash_error'] = 'invalid_availability';
                header('Location: index.php');
            }
            exit;
            break;

        case 'accept_scrim':
            $not_id = isset($_POST['not_id']) ? (int)$_POST['not_id'] : 0;
            $scr_id = isset($_POST['scr_id']) ? (int)$_POST['scr_id'] : 0;

            if ($scr_id > 0) {
                mysqli_begin_transaction($conn);
                try {
                    // Obtener datos del scrim a aceptar
                    $sql_data = "SELECT s.*, e1.usu_id as capitan_emisor, e2.equ_nombre as receptor_nombre, e2.usu_id as capitan_receptor
                                 FROM scrim s
                                 JOIN equipo e1 ON s.equ_id_emisor = e1.equ_id
                                 JOIN equipo e2 ON s.equ_id_receptor = e2.equ_id
                                 WHERE s.scr_id = ?";
                    $res_data = $conn->execute_query($sql_data, [$scr_id]);
                    $scrim = $res_data->fetch_assoc();

                    if (!$scrim) throw new Exception("scrim_not_found");

                    // debe ser el capitán receptor o tener permiso_enviar_scrim en el equipo receptor
                    $is_allowed = false;
                    if ($scrim['capitan_receptor'] == $usu_id) {
                        $is_allowed = true;
                    } else {
                        $sql_perm = "SELECT per_enviar_scrim FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
                        $res_perm = $conn->execute_query($sql_perm, [$usu_id, $scrim['equ_id_receptor']]);
                        $perm = $res_perm->fetch_assoc();
                        if ($perm && $perm['per_enviar_scrim'] == 1) {
                            $is_allowed = true;
                        }
                    }
                    if (!$is_allowed) throw new Exception("not_authorized");

                    // Verificar si alguno de los equipos ya tiene un scrim aceptado en este slot
                    $sql_booked = "SELECT 1 FROM scrim
                                   WHERE (equ_id_emisor IN (?, ?) OR equ_id_receptor IN (?, ?))
                                   AND scr_fecha_juego = ?
                                   AND (scr_hora_inicio < ? AND scr_hora_fin > ?)
                                   AND est_id = 2 LIMIT 1";
                    $res_booked = $conn->execute_query($sql_booked, [
                        $scrim['equ_id_emisor'], $scrim['equ_id_receptor'],
                        $scrim['equ_id_emisor'], $scrim['equ_id_receptor'],
                        $scrim['scr_fecha_juego'],
                        $scrim['scr_hora_fin'],
                        $scrim['scr_hora_inicio']
                    ]);

                    if ($res_booked->num_rows > 0) {
                        throw new Exception("slot_taken");
                    }

                    // aceptamos y notificamos al emisor
                    $conn->execute_query('UPDATE scrim SET est_id = 2 WHERE scr_id = ?', [$scr_id]);
                    $msg_acep = "¡El equipo " . $scrim['receptor_nombre'] . " ha aceptado tu solicitud de scrim para el " . $scrim['scr_fecha_juego'] . "!";
                    $conn->execute_query("INSERT INTO notificacion (usu_id, equ_id, scr_id, not_tipo, not_asunto, not_mensaje)
                                          VALUES (?, ?, ?, 'SISTEMA', ?, ?)", [
                                              $scrim['capitan_emisor'],
                                              $scrim['equ_id_emisor'],
                                              $scr_id,
                                              'Solicitud Aceptada',
                                              $msg_acep
                                          ]);

                    // rechazamos todos los demas scrims pendientes
                    $sql_conflicts = "SELECT s.scr_id, s.equ_id_emisor, s.equ_id_receptor,
                                             e1.equ_nombre as nom_emisor, e1.usu_id as cap_emisor,
                                             e2.equ_nombre as nom_receptor, e2.usu_id as cap_receptor
                                      FROM scrim s
                                      JOIN equipo e1 ON s.equ_id_emisor = e1.equ_id
                                      JOIN equipo e2 ON s.equ_id_receptor = e2.equ_id
                                      WHERE s.est_id = 1
                                      AND s.scr_fecha_juego = ?
                                      AND (s.scr_hora_inicio < ? AND s.scr_hora_fin > ?)
                                      AND (s.equ_id_emisor IN (?, ?) OR s.equ_id_receptor IN (?, ?))
                                      AND s.scr_id != ?";
                    $res_conflicts = $conn->execute_query($sql_conflicts, [
                        $scrim['scr_fecha_juego'],
                        $scrim['scr_hora_fin'],
                        $scrim['scr_hora_inicio'],
                        $scrim['equ_id_emisor'], $scrim['equ_id_receptor'],
                        $scrim['equ_id_emisor'], $scrim['equ_id_receptor'],
                        $scr_id
                    ]);

                    $busy_teams = [$scrim['equ_id_emisor'], $scrim['equ_id_receptor']];

                    while ($conflict = $res_conflicts->fetch_assoc()) {
                        if (in_array($conflict['equ_id_receptor'], $busy_teams)) {
                            $dest_usu = $conflict['cap_emisor'];
                            $dest_equ = $conflict['equ_id_emisor'];
                            $equ_ocupado = $conflict['nom_receptor'];
                            $asunto_conf = "Scrim Rechazado";
                            $msg_conf = "El equipo " . $equ_ocupado . " ha rechazado tu solicitud de scrim para el " . $scrim['scr_fecha_juego'];
                        } else {
                            $dest_usu = $conflict['cap_receptor'];
                            $dest_equ = $conflict['equ_id_receptor'];
                            $equ_ocupado = $conflict['nom_emisor'];
                            $asunto_conf = "Scrim Cancelado";
                            $msg_conf = "El equipo " . $equ_ocupado . " ha cancelado su solicitud de scrim para el " . $scrim['scr_fecha_juego'];
                        }
                        $sql_notif_conflict = "INSERT INTO notificacion (usu_id, equ_id, not_tipo, not_asunto, not_mensaje)
                                               VALUES (?, ?, 'SISTEMA', ?, ?)";
                        $conn->execute_query($sql_notif_conflict, [$dest_usu, $dest_equ, $asunto_conf, $msg_conf]);
                        $conn->execute_query("DELETE FROM scrim WHERE scr_id = ?", [$conflict['scr_id']]);
                    }
                    if ($not_id > 0) { // desde el area de notificaciones
                        $conn->execute_query('DELETE FROM notificacion WHERE not_id = ? AND usu_id = ?', [$not_id, $usu_id]);
                    } else { // desde el calendario
                        $conn->execute_query('DELETE FROM notificacion WHERE scr_id = ? AND usu_id = ? AND not_tipo = "SCRIM"', [$scr_id, $usu_id]);
                    }
                    mysqli_commit($conn);
                    $_SESSION['flash_msg'] = 'Scrim aceptado correctamente.';
                }
                catch (Exception $e) {
                    mysqli_rollback($conn);
                    $_SESSION['flash_error'] = $e->getMessage();
                }
            }
            header('Location: ' . ($_POST['redirect'] ?? '../user/notification/index.php'));
            exit;
            break;

        case 'reject_scrim':
            $not_id = isset($_POST['not_id']) ? (int)$_POST['not_id'] : 0;
            $scr_id = isset($_POST['scr_id']) ? (int)$_POST['scr_id'] : 0;

            if ($scr_id > 0) {
                mysqli_begin_transaction($conn);
                try {
                    // identificamos al emisor antes de borrar el scrim y la notificacion
                    $sql_info = "SELECT s.equ_id_emisor, s.equ_id_receptor, s.scr_fecha_juego, e2.equ_nombre as receptor_nombre, e1.usu_id as capitan_emisor, e2.usu_id as capitan_receptor
                                 FROM scrim s
                                 JOIN equipo e1 ON s.equ_id_emisor = e1.equ_id
                                 JOIN equipo e2 ON s.equ_id_receptor = e2.equ_id
                                 WHERE s.scr_id = ?";
                    $res_info = $conn->execute_query($sql_info, [$scr_id]);
                    $scrim = $res_info->fetch_assoc();

                    if ($scrim) {
                        // debe ser el capitán receptor o tener permiso_enviar_scrim en el equipo receptor
                        $is_allowed = false;
                        if ($scrim['capitan_receptor'] == $usu_id) {
                            $is_allowed = true;
                        } else {
                            $sql_perm = "SELECT per_enviar_scrim FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?";
                            $res_perm = $conn->execute_query($sql_perm, [$usu_id, $scrim['equ_id_receptor']]);
                            $perm = $res_perm->fetch_assoc();
                            if ($perm && $perm['per_enviar_scrim'] == 1) {
                                $is_allowed = true;
                            }
                        }
                        if (!$is_allowed) throw new Exception("not_authorized");

                        $asunto = 'Scrim Rechazado';
                        $mensaje = "El equipo " . $scrim['receptor_nombre'] . " ha rechazado tu solicitud de scrim para el " . $scrim['scr_fecha_juego'];

                        $sql_notif = "INSERT INTO notificacion (usu_id, equ_id, not_tipo, not_asunto, not_mensaje)
                                      VALUES (?, ?, 'SISTEMA', ?, ?)";
                        $conn->execute_query($sql_notif, [
                            $scrim['capitan_emisor'],
                            $scrim['equ_id_emisor'],
                            $asunto,
                            $mensaje
                        ]);
                        $conn->execute_query('DELETE FROM scrim WHERE scr_id = ?', [$scr_id]);

                        if ($not_id > 0) { // desde notificaciones
                            $conn->execute_query('DELETE FROM notificacion WHERE not_id = ? AND usu_id = ?', [$not_id, $usu_id]);
                        } else { // desde el calendario
                            $conn->execute_query('DELETE FROM notificacion WHERE scr_id = ? AND usu_id = ? AND not_tipo = "SCRIM"', [$scr_id, $usu_id]);
                        }
                    }
                    mysqli_commit($conn);
                    $_SESSION['flash_msg'] = 'Scrim rechazado correctamente.';
                }
                catch (Exception $e) {
                    mysqli_rollback($conn);
                    $_SESSION['flash_error'] = $e->getMessage() == 'not_authorized' ? 'not_authorized' : 'Error al rechazar el scrim.';
                }
            }
            header('Location: ' . ($_POST['redirect'] ?? '../user/notification/index.php'));
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
                        $sql_update = "UPDATE scrim SET est_id = 3 WHERE scr_id = ?";
                        $conn->execute_query($sql_update, [$scr_id]);

                        // Determinar quién canceló para el mensaje y a quién notificar
                        $soy_emisor = false;
                        if ($scrim['capitan_emisor'] == $usu_id) {
                            $soy_emisor = true;
                        }
                        else {
                            $check_emisor = $conn->execute_query("SELECT 1 FROM permiso_equipo WHERE usu_id = ? AND equ_id = ?", [$usu_id, $scrim['equ_id_emisor']]);
                            if ($check_emisor->num_rows > 0)
                                $soy_emisor = true;
                        }

                        if ($soy_emisor) {
                            $cancelador_nombre = $scrim['emisor_nombre'];
                            $destinatario_id = $scrim['capitan_receptor'];
                            $equ_id_noti = $scrim['equ_id_receptor'];
                        }
                        else {
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
                    }
                    else {
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
