<?php
// modules/scrim/index.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: ' . BASE_URL . 'modules/auth/login.php');
    exit;
}
$usu_id = $_SESSION['usu_id'];
$search = isset($_GET['e']) ? trim($_GET['e']) : '';
$search_juego = isset($_GET['j']) ? $_GET['j'] : '';

$sql_juegos = 'SELECT * FROM juego ORDER BY jue_nombre ASC';
$res_juegos = $conn->query($sql_juegos);

$where_clauses = [];
$params = [];

// Sacar equipos a los que el usu_id pertenece
$where_clauses[] = 'e.equ_id NOT IN (SELECT equ_id FROM permiso_equipo WHERE usu_id = ?)';
$params[] = $usu_id;
$where_clauses[] = 'e.usu_id != ?';
$params[] = $usu_id;

if (!empty($search)) {
    $where_clauses[] = 'e.equ_nombre LIKE ?';
    $params[] = "%$search%";
}

if (!empty($search_juego)) {
    $where_clauses[] = 'e.jue_id = ?';
    $params[] = $search_juego;
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Consulta todos los equipos del sistema
$sql_teams = "
SELECT
  e.equ_id,
  e.usu_id AS capitan_id,
  u.usu_username AS capitan,
  e.jue_id,
  e.equ_nombre,
  e.equ_logo,
  j.jue_nombre AS juego,
  g.gen_nombre AS genero,
  COUNT(DISTINCT d.dis_id) AS horarios,
  COUNT(DISTINCT p.per_id) AS miembros
FROM equipo e
LEFT JOIN usuario u ON e.usu_id = u.usu_id
LEFT JOIN juego j ON e.jue_id = j.jue_id
LEFT JOIN genero g ON j.gen_id = g.gen_id
LEFT JOIN disponibilidad d ON d.equ_id = e.equ_id
LEFT JOIN permiso_equipo p ON p.equ_id = e.equ_id
$where_sql
GROUP BY
  e.equ_id, capitan_id, capitan, e.jue_id, e.equ_nombre, e.equ_logo, juego, genero
ORDER BY horarios DESC, e.equ_nombre ASC";

$sql_horarios = "
SELECT
  d.dis_id,
  d.equ_id,
  d.dis_dia_semana as dia,
  d.dis_hora_inicio as hora_inicio,
  d.dis_hora_fin as hora_fin
FROM
  disponibilidad d
INNER JOIN equipo e ON d.equ_id = e.equ_id
$where_sql
";

$res_teams = $conn->execute_query($sql_teams, $params);
$res_horarios = $conn->execute_query($sql_horarios, $params);

$dias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];

// Listado de mis equipos
$sql_my_teams = 'SELECT equ_id, equ_nombre, jue_id FROM equipo WHERE usu_id = ?';
$res_my_teams = $conn->execute_query($sql_my_teams, [$usu_id]);
$my_teams = [];
while ($row = $res_my_teams->fetch_assoc()) {
    $my_teams[] = $row;
}

$equipos = [];
if ($res_teams) {
    while ($row = $res_teams->fetch_assoc()) {
        $equipos[] = $row;
    }
}

$horarios_map = [];
if ($res_horarios) {
    while ($row = $res_horarios->fetch_assoc()) {
        $horarios_map[$row['equ_id']][] = $row;
    }
}

include '../../includes/header.php';
include '../../includes/user_navbar.php';
?>

<div class="flex min-h-screen bg-background">
    <main class="flex-1 w-full pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div class="mb-6 bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0"></i>
                        <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            if ($_SESSION['flash_msg'] == 'scrim_sent')
                                echo 'Solicitud de scrim enviada con éxito';
                            ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <?php unset($_SESSION['flash_msg']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-6 bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0"></i>
                        <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_error']) {
                                case 'invalid_game':
                                    echo 'Error: Los equipos deben ser del mismo juego.';
                                    break;
                                case 'mutual_error':
                                    echo 'Error: Tu equipo seleccionado no tiene este horario disponible en su perfil.';
                                    break;
                                case 'invalid_availability':
                                    echo 'Error: El horario seleccionado ya no está disponible.';
                                    break;
                                case 'scrim_exists':
                                    echo 'Error: Ya existe una solicitud pendiente para este horario entre estos equipos.';
                                    break;
                                case 'slot_taken':
                                    echo 'Error: Uno de los equipos ya tiene una partida en este horario.';
                                    break;
                                default:
                                    echo 'Ocurrió un error al procesar la solicitud.';
                                    break;
                            }
                            ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-primary uppercase tracking-tight mb-2">
                        Búsqueda de Equipos
                    </h2>
                    <p class="text-secondary text-sm font-medium uppercase tracking-widest">
                        Encuentra tu proximo rival.
                    </p>
                </div>

                <div class="w-full md:w-auto">
                    <form action="" method="GET" class="flex flex-col md:flex-row gap-3 items-end">
                        <!-- Buscar por nombre -->
                        <div class="flex flex-col gap-1 w-full md:w-64">
                            <label class="text-[10px] font-black uppercase tracking-widest text-secondary px-1">Nombre</label>
                            <input type="text" name="e" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar equipo..."
                                class="text-primary bg-surface border-2 border-border p-2.5 text-sm hover:border-primary focus:border-primary outline-none w-full shadow-hard-sm hover:shadow-hard focus:shadow-hard transition-all">
                        </div>

                        <!-- Filtrar por juego -->
                        <div class="flex flex-col gap-1 w-full md:w-48">
                            <label class="text-[10px] font-black uppercase tracking-widest text-secondary px-1">Juego</label>
                            <div class="relative">
                                <label>
                                    <select name="j" onchange="this.form.submit()"
                                        class="transition-colors w-full bg-surface border-2 border-border p-2.5 pr-10 font-bold text-sm text-primary appearance-none cursor-pointer shadow-hard-sm hover:shadow-hard hover:border-primary focus:shadow-hard focus:border-primary ">
                                        <option value="">Todos los juegos</option>
                                        <?php
                                        $res_juegos->data_seek(0);
                                        while ($j = $res_juegos->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $j['jue_id']; ?>" <?php echo ($search_juego == $j['jue_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($j['jue_nombre']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </label>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-secondary">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto">
                            <button type="submit" class="flex-1 md:flex-none bg-primary text-white p-2.5 hover:bg-primary-hover transition-colors shadow-hard-sm hover:shadow-hard hover:border-primary ">
                                <i data-lucide="search" class="w-5 h-5 mx-auto"></i>
                            </button>
                            <?php if (!empty($search) || !empty($search_juego)): ?>
                                <a href="index.php" class="transition-colors flex-1 md:flex-none bg-surface text-secondary hover:text-primary border-2 hover:border-primary secondary p-2.5 flex items-center justify-center shadow-hard-sm hover:shadow-hard" title="Limpiar filtros">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($equipos as $equipo): ?>
                    <?php $inicial = strtoupper(substr($equipo['equ_nombre'] ?? '', 0, 1)); ?>
                    <div
                        class="bg-surface border-2 border-primary shadow-hard-sm hover:shadow-hard transition-all group flex flex-col h-full">

                        <div class="h-24 sm:h-28 bg-subtle relative border-b-2 border-primary">
                            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(var(--color-primary) 1px, transparent 1px); background-size: 8px 8px;"></div>
                            <div class="absolute top-3 left-3 z-10">
                                <span class="bg-surface border-2 border-primary text-primary text-[10px] font-black px-2 py-1 uppercase tracking-wider">
                                    <?php echo htmlspecialchars($equipo['juego'] ?? 'General'); ?>
                                </span>
                            </div>

                            <?php if (!empty($equipo['genero'])): ?>
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="bg-surface border-2 border-primary text-primary text-[10px] font-black px-2 py-1 uppercase tracking-wider">
                                        <?php echo htmlspecialchars($equipo['genero']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="absolute -bottom-6 left-6">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 border-2 border-primary bg-surface shadow-hard-sm overflow-hidden flex items-center justify-center">
                                    <?php if ($equipo['equ_logo']): ?>
                                        <img src="<?php echo '../../../' . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-subtle flex items-center justify-center text-muted font-black text-xl sm:text-2xl">
                                            <?php echo $inicial; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 pt-8 sm:pt-10 flex-grow flex flex-col">
                            <div class="mb-4">
                                <h3 class="font-black text-base sm:text-lg text-primary leading-tight uppercase tracking-tight truncate group-hover:text-primary-hover transition-colors">
                                    <?php echo htmlspecialchars($equipo['equ_nombre']); ?>
                                </h3>
                            </div>

                            <div class="flex items-center gap-4 py-3 border-t-2 border-subtle mb-6">
                                <div class="text-center flex-1">
                                    <span class="block text-[10px] text-muted font-black uppercase tracking-widest">Miembros</span>
                                    <span class="font-black text-primary text-sm"><?php echo isset($equipo['miembros']) ? $equipo['miembros'] : 0; ?></span>
                                </div>
                                <div class="w-px h-6 bg-border"></div>
                                <div class="text-center flex-1">
                                    <span class="block text-[10px] text-muted font-black uppercase tracking-widest">Capitan</span>
                                    <span class="font-black text-primary text-sm truncate block max-w-[80px] mx-auto"><?php echo htmlspecialchars($equipo['capitan'] ?? 'N/A'); ?></span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <?php if ($equipo['horarios']): ?>

                                    <button
                                        onclick="document.getElementById('teamProfile_<?php echo $equipo['equ_id']; ?>').showModal()"
                                        class="cursor-pointer w-full flex items-center justify-center gap-2 bg-primary text-white py-3 sm:py-2.5 text-xs font-black uppercase tracking-widest hover:bg-primary-hover hover:border-primary-hover transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        Ver Disponibilidad
                                    </button>

                                    <dialog id="teamProfile_<?php echo $equipo['equ_id']; ?>"
                                        class="m-auto rounded-none border-2 border-primary p-0 shadow-hard">
                                        <div class="w-full max-w-2xl bg-surface p-6">
                                            <div class="flex justify-between items-center pb-4 mb-4 border-b-2 border-subtle">
                                                <h3 class="text-xl font-black uppercase text-primary tracking-tight">HORARIOS</h3>
                                                <button onclick="document.getElementById('teamProfile_<?php echo $equipo['equ_id']; ?>').close()"
                                                    class="text-secondary hover:text-primary transition-colors cursor-pointer">
                                                    <i data-lucide="x" class="w-6 h-6"></i>
                                                </button>
                                            </div>

                                            <form action="controller_scrim.php" method="POST" class="space-y-6">
                                                <input type="hidden" name="equ_id_receptor" value="<?php echo $equipo['equ_id']; ?>">

                                                <div>
                                                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2 px-1">Tu Equipo</label>
                                                    <select name="equ_id_emisor" required
                                                        class="w-full bg-surface border-2 border-border p-3 font-bold text-sm text-primary appearance-none cursor-pointer focus:border-primary outline-none shadow-hard-sm">
                                                        <option value="">Selecciona tu equipo...</option>
                                                        <?php foreach ($my_teams as $mt): ?>
                                                            <?php if ($mt['jue_id'] == $equipo['jue_id']): ?>
                                                                <option value="<?php echo $mt['equ_id']; ?>">
                                                                    <?php echo htmlspecialchars($mt['equ_nombre']); ?>
                                                                </option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2 px-1">Selecciona un Horario</label>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                        <?php
                                                        $team_horarios = $horarios_map[$equipo['equ_id']] ?? [];
                                                        if (empty($team_horarios)):
                                                        ?>
                                                            <p class="col-span-2 text-center text-secondary py-6 uppercase text-[10px] font-black tracking-widest bg-subtle">
                                                                Sin horarios registrados
                                                            </p>
                                                        <?php else: ?>
                                                            <?php foreach ($team_horarios as $h): ?>
                                                                <label class="relative flex items-center p-4 bg-subtle border-2 border-transparent hover:border-primary cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-white shadow-sm">
                                                                    <input type="radio" name="dis_id" value="<?php echo $h['dis_id']; ?>" required class="mr-3 accent-primary">
                                                                    <div class="flex flex-col">
                                                                        <span class="text-[10px] font-black uppercase tracking-widest text-secondary">
                                                                            <?php echo $dias[$h['dia']] ?? 'Día'; ?>
                                                                        </span>
                                                                        <span class="font-bold text-primary text-sm">
                                                                            <?php echo date('H:i', strtotime($h['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($h['hora_fin'])); ?>
                                                                        </span>
                                                                    </div>
                                                                </label>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="pt-4">
                                                    <?php if (empty($team_horarios)): ?>
                                                        <div class="w-full bg-secondary text-white py-4 text-xs font-black uppercase tracking-widest text-center cursor-not-allowed">
                                                            No disponible
                                                        </div>
                                                    <?php else: ?>
                                                        <button type="submit" name="action" value="enviar"
                                                            class="w-full bg-primary text-white py-4 text-xs font-black uppercase tracking-widest hover:bg-primary-hover transition-colors cursor-pointer flex items-center justify-center gap-2">
                                                            <i data-lucide="send" class="w-4 h-4"></i>
                                                            Enviar Solicitud
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </div>
                                    </dialog>

                                <?php else: ?>
                                    <div class="cursor-not-allowed w-full flex items-center justify-center gap-2 bg-secondary text-white  py-3 sm:py-2.5 text-xs font-black uppercase tracking-widest">
                                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                                        No Disponible
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
