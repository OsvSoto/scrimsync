<?php
// modules/user/calendar/controller_calendar.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../../../modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];

// obtener todos los equipos donde el usuario es miembro
$sql_all_teams = "
    SELECT e.equ_id, e.equ_nombre, 'captain' as role, 1 as can_accept
    FROM equipo e
    WHERE e.usu_id = ?
    UNION
    SELECT e.equ_id, e.equ_nombre, 'member' as role, pe.per_enviar_scrim as can_accept
    FROM equipo e
    JOIN permiso_equipo pe ON e.equ_id = pe.equ_id
    WHERE pe.usu_id = ? AND e.usu_id != ?
";
$res_all_teams = $conn->execute_query($sql_all_teams, [$usu_id, $usu_id, $usu_id]);

$user_teams = [];
$my_teams_list = []; // para el dropdown
while ($row = $res_all_teams->fetch_assoc()) {
    $user_teams[$row['equ_id']] = [
        'equ_nombre' => $row['equ_nombre'],
        'role' => $row['role'],
        'can_accept' => $row['can_accept'] == 1
    ];
    $my_teams_list[] = $row;
}

// para filtrar por equipos
$filter_team_id = isset($_GET['t']) ? intval($_GET['t']) : 0;
$team_ids_to_query = [];

if ($filter_team_id > 0 && isset($user_teams[$filter_team_id])) {
    $team_ids_to_query = [$filter_team_id];
} else {
    $team_ids_to_query = array_keys($user_teams);
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

if (!empty($team_ids_to_query)) {
    $placeholders = implode(',', array_fill(0, count($team_ids_to_query), '?'));

    $sql_scrims = "
    SELECT s.*, es.est_descripcion,
           e1.equ_nombre as emisor_nombre, e1.equ_logo as emisor_logo,
           e2.equ_nombre as receptor_nombre, e2.equ_logo as receptor_logo
    FROM scrim s
    JOIN estado_scrim es ON s.est_id = es.est_id
    JOIN equipo e1 ON s.equ_id_emisor = e1.equ_id
    JOIN equipo e2 ON s.equ_id_receptor = e2.equ_id
    WHERE (s.equ_id_emisor IN ($placeholders) OR s.equ_id_receptor IN ($placeholders))
      AND MONTH(s.scr_fecha_juego) = ? AND YEAR(s.scr_fecha_juego) = ?
    ";

    $params = array_merge($team_ids_to_query, $team_ids_to_query, [$month, $year]);
    $res_scrims = $conn->execute_query($sql_scrims, $params);

    while ($row = $res_scrims->fetch_assoc()) {
        // preparar si soy equipo receptor o emisor
        $is_emisor = isset($user_teams[$row['equ_id_emisor']]) && (empty($team_ids_to_query) || in_array($row['equ_id_emisor'], $team_ids_to_query));
        $is_receptor = isset($user_teams[$row['equ_id_receptor']]) && (empty($team_ids_to_query) || in_array($row['equ_id_receptor'], $team_ids_to_query));

        if ($is_receptor) {
            $row['opponent_name'] = $row['emisor_nombre'];
            $row['opponent_logo'] = $row['emisor_logo'];
            $row['user_team_id'] = $row['equ_id_receptor'];
            $row['is_user_receptor'] = true;
        } else {
            $row['opponent_name'] = $row['receptor_nombre'];
            $row['opponent_logo'] = $row['receptor_logo'];
            $row['user_team_id'] = $row['equ_id_emisor'];
            $row['is_user_receptor'] = false;
        }

        // permisos para este scrim
        $team_info = $user_teams[$row['user_team_id']];
        $row['user_can_manage'] = ($team_info['role'] === 'captain' || $team_info['can_accept']);

        $scrims[] = $row;
    }

    $sql_avail = "SELECT * FROM disponibilidad WHERE equ_id IN ($placeholders) ORDER BY dis_dia_semana, dis_hora_inicio";
    $res_avail = $conn->execute_query($sql_avail, $team_ids_to_query);
    while ($row = $res_avail->fetch_assoc()) {
        $availability[] = $row;
    }
}

$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$numberDays = date('t', $firstDayOfMonth);
$dateComponents = getdate($firstDayOfMonth);
$monthName = $dateComponents['month'];
$dayOfWeek = $dateComponents['wday']; // 1:Lunes, 2:Martes, 3:miercoles, etc...
