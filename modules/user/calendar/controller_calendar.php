<?php
// modules/user/calendar/controller_calendar.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../../../modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];

$sql_team = "SELECT * FROM equipo WHERE usu_id = ? LIMIT 1";
// TODO: Usar execute_query o pasarlo a OOP
$stmt = mysqli_prepare($conn, $sql_team);
mysqli_stmt_bind_param($stmt, "i", $usu_id);
mysqli_stmt_execute($stmt);
$result_team = mysqli_stmt_get_result($stmt);
$team = mysqli_fetch_assoc($result_team);
$is_captain = true;

if (!$team) {
    $sql_member = "SELECT e.* FROM equipo e JOIN permiso_equipo pe ON e.equ_id = pe.equ_id WHERE pe.usu_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql_member);
    mysqli_stmt_bind_param($stmt, "i", $usu_id);
    mysqli_stmt_execute($stmt);
    $result_team = mysqli_stmt_get_result($stmt);
    $team = mysqli_fetch_assoc($result_team);
    $is_captain = false;
}

$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

$scrims = [];
$availability = [];

if ($team) {
    $equ_id = $team['equ_id'];
    $sql_scrims = "
    SELECT s.*, es.est_descripcion,
           e1.equ_nombre as emisor_nombre, e1.equ_logo as emisor_logo,
           e2.equ_nombre as receptor_nombre, e2.equ_logo as receptor_logo
    FROM scrim s
    JOIN estado_scrim es ON s.est_id = es.est_id
    JOIN equipo e1 ON s.equ_id_emisor = e1.equ_id
    JOIN equipo e2 ON s.equ_id_receptor = e2.equ_id
    WHERE (s.equ_id_emisor = ? OR s.equ_id_receptor = ?)
      AND MONTH(s.scr_fecha_juego) = ? AND YEAR(s.scr_fecha_juego) = ?
    ";

    $stmt = mysqli_prepare($conn, $sql_scrims);
    mysqli_stmt_bind_param($stmt, "iiii", $equ_id, $equ_id, $month, $year);
    mysqli_stmt_execute($stmt);
    $res_scrims = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($res_scrims)) {
        if ($row['equ_id_emisor'] == $equ_id) {
            $row['opponent_name'] = $row['receptor_nombre'];
            $row['opponent_logo'] = $row['receptor_logo'];
        } else {
            $row['opponent_name'] = $row['emisor_nombre'];
            $row['opponent_logo'] = $row['emisor_logo'];
        }
        $scrims[] = $row;
    }
    mysqli_stmt_close($stmt);

    $sql_avail = "SELECT * FROM disponibilidad WHERE equ_id = ? ORDER BY dis_dia_semana, dis_hora_inicio";
    $stmt = mysqli_prepare($conn, $sql_avail);
    mysqli_stmt_bind_param($stmt, "i", $equ_id);
    mysqli_stmt_execute($stmt);
    $res_avail = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res_avail)) {
        $availability[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$numberDays = date('t', $firstDayOfMonth);
$dateComponents = getdate($firstDayOfMonth);
$monthName = $dateComponents['month'];
$dayOfWeek = $dateComponents['wday']; // 1:Lunes, 2:Martes
