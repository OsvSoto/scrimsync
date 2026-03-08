<?php
// modules/team/profile/view.php
session_start();
require_once '../../../config/db.php';

if (empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$equ_id = (int)$_GET['id'];
$usu_id = $_SESSION['usu_id'] ?? 0;

$sql_equipo = "SELECT e.*, j.jue_nombre, g.gen_nombre
               FROM equipo e
               LEFT JOIN juego j ON e.jue_id = j.jue_id
               LEFT JOIN genero g ON j.gen_id = g.gen_id
               WHERE e.equ_id = ?";
$res_equipo = $conn->execute_query($sql_equipo, [$equ_id]);

if ($res_equipo->num_rows == 0) {
    die("El equipo no existe.");
}
$equipo = $res_equipo->fetch_assoc();

$roles_disponibles = [];
if ($equipo['jue_id']) {
    $sql_roles = "SELECT * FROM rol_predefinido WHERE jue_id = ? ORDER BY rol_nombre ASC";
    $res_roles = $conn->execute_query($sql_roles, [$equipo['jue_id']]);
    if ($res_roles) {
        while ($r = $res_roles->fetch_assoc()) {
            $roles_disponibles[] = $r;
        }
    }
}

$sql_miembros = "SELECT u.usu_username, u.usu_alias, pe.*, r.rol_nombre, u.usu_foto, u.usu_descripcion
                 FROM permiso_equipo pe
                 INNER JOIN usuario u ON pe.usu_id = u.usu_id
                 LEFT JOIN rol_predefinido r ON pe.rol_id = r.rol_id
                 WHERE pe.equ_id = ?";
$res_miembros = $conn->execute_query($sql_miembros, [$equ_id]);

$soy_capitan = false;
$soy_miembro = false;

if ($usu_id > 0) {
    $sql_mis_permisos = "SELECT * FROM permiso_equipo
                       WHERE usu_id = ? AND equ_id = ? LIMIT 1";
    $res_mis_permisos = $conn->execute_query($sql_mis_permisos, [$usu_id, $equ_id]);

    if ($res_mis_permisos->num_rows > 0) {
        $mis_datos = $res_mis_permisos->fetch_assoc();
        $soy_miembro = true;

        if ($mis_datos['per_elim_miembro'] == 1 || $mis_datos['per_modif_horario'] == 1) {
            $soy_capitan = true;
        }
    }
}
$sql_disp = "SELECT * FROM disponibilidad WHERE equ_id = ? ORDER BY dis_dia_semana ASC, dis_hora_inicio ASC";
$res_disp = $conn->execute_query($sql_disp, [$equ_id]);
$dias_semana = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];

include '../../../includes/header.php';
include '../../../includes/user_navbar.php';
?>

<div class="flex min-h-screen bg-subtle">
    <main class="flex-1 w-full pt-16 pb-8">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <a href="index.php" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a mis equipos
            </a>
        </div>

        <?php if (isset($_SESSION['flash_msg'])): ?>
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0"></i>
                        <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_msg']) {
                                case 'kicked':
                                    echo "Miembro eliminado correctamente.";
                                    break;
                                case 'role_assigned':
                                    echo "Rol asignado correctamente.";
                                    break;
                                case 'availability_added':
                                    echo "Horario añadido correctamente.";
                                    break;
                                case 'availability_deleted':
                                    echo "Horario eliminado correctamente.";
                                    break;
                            }
                            ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['flash_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0"></i>
                        <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_error']){
                                case 'db_error':
                                    echo "Error al procesar la solicitud.";
                                    break;
                                case 'no_permission':
                                    echo "No tienes permisos para realizar esta acción.";
                                    break;
                                case 'invalid_input':
                                    echo "Datos de entrada inválidos.";
                                    break;
                                case 'invalid_time_range':
                                    echo "Rango de tiempo inválido.";
                                    break;
                                case 'cant_quit':
                                    echo "No puedes abandonar el equipo siendo el único capitán con miembros activos.";
                                    break;
                            }
                            ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <!-- BANNER EQUIPO -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="bg-surface border-2 border-primary overflow-hidden shadow-hard">
                <div class="h-32 sm:h-48 bg-primary relative">
                    <?php if ($equipo['equ_logo']): ?>
                        <div class="absolute inset-0 opacity-75" style="background-image: url('<?php echo "../../../" . $equipo['equ_logo']; ?>'); background-size: 100px 100px; background-repeat: repeat;"></div>
                    <?php else: ?>
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
                    <?php endif; ?>

                    <div class="absolute top-4 right-4 flex flex-col items-end gap-1">
                        <span class="bg-black text-white text-[10px] font-black px-3 py-1 border border-white/20 uppercase tracking-widest">
                            <?php echo htmlspecialchars($equipo['jue_nombre']); ?>
                        </span>
                        <?php if (!empty($equipo['gen_nombre'])): ?>
                        <span class="bg-black text-white text-[10px] font-black px-3 py-1 border border-white/20 uppercase tracking-widest">
                                <?php echo htmlspecialchars($equipo['gen_nombre']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="px-4 pb-6 sm:px-6 lg:px-8 relative border-t-2">
                    <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-12 mb-6 gap-6">
                        <div class="w-32 h-32 border-2 border-primary bg-surface shadow-hard-sm overflow-hidden shrink-0">
                            <?php if ($equipo['equ_logo']): ?>
                                <img src="<?php echo "../../../" . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-subtle flex items-center justify-center text-4xl font-black text-muted">
                                    <?php echo strtoupper(substr($equipo['equ_nombre'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <h1 class="text-3xl sm:text-4xl font-black text-primary uppercase tracking-tight mb-1">
                                <?php echo htmlspecialchars($equipo['equ_nombre']); ?>
                            </h1>
                        </div>

                        <?php if ($soy_capitan): ?>
                            <div class="flex sm:flex-row items-center sm:items-end gap-1">
                                <a href="manage.php?id=<?php echo $equipo['equ_id']; ?>"
                                    class="text-xs text-white font-black uppercase tracking-widest px-6 py-3 bg-primary hover:bg-primary-hover transition-all flex items-center gap-2">
                                    <i data-lucide="settings-2" class="w-4 h-4"></i>
                                    Gestionar Equipo
                                </a>
                                <form action="../actions/delete_team.php" method="POST" onsubmit="return confirm('¿ESTÁS SEGURO? Esta acción es irreversible y eliminará el equipo permanentemente.');">
                                    <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                    <button type="submit"
                                        title="Eliminar Equipo"
                                        class="transition-all text-xs text-secondary font-black uppercase tracking-widest px-3 py-3 hover:bg-error-text hover:text-surface flex items-center h-full cursor-pointer">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roster Activo -- Disponibilidad -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-black text-primary uppercase tracking-tight">Roster Activo</h3>
                    <span class="bg-subtle border-2 border-border text-secondary px-2 py-1 text-xs font-black">
                        <?php echo mysqli_num_rows($res_miembros); ?> Jugadores
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <?php
                    if ($res_miembros->num_rows > 0):
                        $res_miembros->data_seek(0);
                        while ($miembro = $res_miembros->fetch_assoc()):
                            $es_lider = ($miembro['per_modif_horario'] == 1 || $miembro['per_elim_miembro'] == 1);
                    ?>
                            <div class="bg-surface p-3 border-2 border-primary flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 shadow-hard-sm">

                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-subtle border-2 border-border flex items-center justify-center text-muted shrink-0">
                                        <?php if ($miembro['usu_foto']): ?>
                                            <img src="<?php echo "../../../" . $miembro['usu_foto']; ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i data-lucide="user" class="w-5 h-5"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">

                                            <button
                                                onclick="document.getElementById('userModal_<?php echo $miembro['usu_id']; ?>').showModal()"
                                                class="font-black text-primary uppercase hover:underline tracking-tight text-sm text-left cursor-pointer">
                                                <?php echo htmlspecialchars($miembro['usu_alias'] ?? $miembro['usu_username']); ?>
                                            </button>

                                            <dialog id="userModal_<?php echo $miembro['usu_id']; ?>" class="m-auto rounded-none border-2 border-primary p-0 shadow-hard w-[95%] sm:w-full max-w-md">
                                                <div class="bg-surface p-3">
                                                    <div class="flex justify-end">
                                                        <button onclick="document.getElementById('userModal_<?php echo $miembro['usu_id']; ?>').close()"
                                                            class="text-secondary hover:text-primary transition-colors cursor-pointer">
                                                            <i data-lucide="x" class="w-5 h-5"></i>
                                                        </button>
                                                    </div>
                                                    <div class="p-2">
                                                        <div class="flex flex-col items-center text-center gap-4">
                                                            <div class="w-24 h-24 bg-subtle border-2 border-primary shadow-hard-sm overflow-hidden">
                                                                <?php if ($miembro['usu_foto']): ?>
                                                                    <img src="<?php echo "../../../" . $miembro['usu_foto']; ?>" class="w-full h-full object-cover">
                                                                <?php else: ?>
                                                                    <div class="w-full h-full flex items-center justify-center">
                                                                        <i data-lucide="user" class="w-12 h-12 text-muted"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <h4 class="text-2xl font-black text-primary uppercase tracking-tight">
                                                                    <?php echo htmlspecialchars($miembro['usu_alias'] ?? $miembro['usu_username']); ?>
                                                                </h4>
                                                                <p class="text-sm font-bold text-secondary font-mono">@<?php echo htmlspecialchars($miembro['usu_username']); ?></p>
                                                            </div>

                                                            <div class="w-full mt-2 p-4 bg-subtle border-2 border-border text-left overflow-hidden">
                                                                <p class="text-xs font-bold uppercase text-muted mb-2 tracking-widest">Descripcion</p>
                                                                <div class="max-h-48 overflow-y-auto pr-1">
                                                                    <p class="text-sm text-primary leading-relaxed break-words">
                                                                        <?php echo $miembro['usu_descripcion'] ? nl2br(htmlspecialchars($miembro['usu_descripcion'])) : 'Sin descripción disponible.'; ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dialog>
                                            <?php if ($es_lider): ?>
                                                <i data-lucide="crown" class="w-3 h-3 text-amber-500 fill-amber-500"></i>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-secondary font-mono font-bold">@<?php echo htmlspecialchars($miembro['usu_username']); ?></p>
                                        <?php if ($miembro['rol_nombre']): ?>
                                            <span class="inline-block mt-0.5 bg-subtle text-secondary px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider">
                                                <?php echo htmlspecialchars($miembro['rol_nombre']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($miembro['usu_id'] == $usu_id) : ?>
                                    <div class="flex items-center gap-1 ml-auto sm:ml-0">
                                        <?php if ($soy_capitan): ?>
                                            <form action="../actions/assign_role.php" method="POST" class="inline-block">
                                                <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                                <input type="hidden" name="target_usu_id" value="<?php echo $miembro['usu_id']; ?>">
                                                <select name="rol_id" onchange="this.form.submit()"
                                                    title="Asignar Mi Rol"
                                                    class="bg-surface border-2 border-primary text-[10px] font-black uppercase tracking-widest px-3 py-1.5 focus:outline-none cursor-pointer hover:bg-subtle transition-colors ">
                                                    <?php if (!$miembro['rol_id']): ?>
                                                        <option value="" disabled selected>Asignar Rol</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($roles_disponibles as $rol): ?>
                                                        <option value="<?php echo $rol['rol_id']; ?>" <?php echo ($rol['rol_id'] == $miembro['rol_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($rol['rol_nombre']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </form>
                                        <?php endif; ?>

                                        <form action="../actions/abandon_team.php" method="POST" onsubmit="return handleAbandon(event);">
                                            <input type="hidden" name="usu_id" value="<?php echo $usu_id; ?>">
                                            <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                            <button type="submit"
                                                title="Abandonar Equipo"
                                                class="transition-colors p-2 text-secondary border-error bg-surface hover:bg-error-text hover:text-surface cursor-pointer">
                                                <i data-lucide="log-out" class="w-3 h-3"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($soy_capitan): ?>
                                    <div class="flex items-center gap-1 ml-auto sm:ml-0">
                                        <form action="../actions/assign_role.php" method="POST" class="inline-block">
                                            <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                            <input type="hidden" name="target_usu_id" value="<?php echo $miembro['usu_id']; ?>">
                                            <select name="rol_id" onchange="this.form.submit()"
                                                title="Asignar Rol"
                                                class="bg-surface border-2 border-primary text-[10px] font-black uppercase tracking-widest px-3 py-1.5 focus:outline-none cursor-pointer hover:bg-subtle transition-colors ">
                                                <?php if (!$miembro['rol_id']): ?>
                                                    <option value="" disabled selected>Asignar Rol</option>
                                                <?php endif; ?>
                                                <?php foreach ($roles_disponibles as $rol): ?>
                                                    <option value="<?php echo $rol['rol_id']; ?>" <?php echo ($rol['rol_id'] == $miembro['rol_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($rol['rol_nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>

                                        <form action="../actions/kick_member.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este miembro del equipo?');">
                                            <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                            <input type="hidden" name="equ_nombre" value="<?php echo $equipo['equ_nombre']; ?>">
                                            <input type="hidden" name="target_usu_id" value="<?php echo $miembro['usu_id']; ?>">
                                            <button type="submit"
                                                title="Eliminar Miembro"
                                                class="transition-colors p-2 text-secondary border-error bg-surface hover:bg-error-text hover:text-surface cursor-pointer">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <div class="col-span-2 text-center py-8 text-secondary italic font-bold border-2 border-dashed border-border">
                            No hay miembros en este equipo aún.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2 bg-surface border-2 border-primary shadow-hard flex flex-col h-fit">
                <div class="p-4 border-b-2 border-primary bg-subtle flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar" class="w-6 h-6"></i>
                        <h3 class="text-xl font-black text-primary uppercase tracking-tight">Disponibilidad</h3>
                    </div>
                </div>

                <div class="p-6">
                    <?php if ($res_disp->num_rows > 0): ?>
                        <ul class="space-y-2 mb-6">
                            <?php while ($disp = $res_disp->fetch_assoc()): ?>
                                <li class="flex items-center justify-between bg-white border border-border p-3">
                                    <div class="flex items-center gap-4">
                                        <span class="font-black text-primary uppercase w-24 text-sm"><?php echo $dias_semana[$disp['dis_dia_semana']]; ?></span>
                                        <span class="font-mono text-sm font-bold text-secondary">
                                            <?php echo substr($disp['dis_hora_inicio'], 0, 5); ?> - <?php echo substr($disp['dis_hora_fin'], 0, 5); ?>
                                        </span>
                                    </div>
                                    <?php if ($soy_capitan): ?>
                                        <form action="../availability/controller_availability.php" method="POST" onsubmit="return confirm('¿Eliminar horario?');">
                                            <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                            <input type="hidden" name="dis_id" value="<?php echo $disp['dis_id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit"
                                                title="Eliminar"
                                                class="transition-colors p-2 text-secondary border-error bg-surface hover:bg-error-text hover:text-surface cursor-pointer">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-6 text-secondary italic font-bold border-2 border-dashed border-border mb-6 text-sm">
                            No hay horarios definidos.
                        </div>
                    <?php endif; ?>

                    <?php if ($soy_capitan): ?>
                        <div class="bg-subtle p-4 border border-border">
                            <h4 class="font-black text-xs uppercase tracking-wider mb-3 text-primary flex items-center gap-2">
                                <i data-lucide="clock" class="w-3 h-3"></i> Añadir Horario
                            </h4>
                            <form action="../availability/controller_availability.php" method="POST" class="grid grid-cols-1 md:grid-cols-7 gap-2 items-end">
                                <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                                <input type="hidden" name="action" value="add">

                                <div class="flex flex-col w-full md:col-span-3">
                                    <label class="text-[10px] font-bold uppercase text-muted mb-1">Día</label>
                                    <select name="day" class="w-full bg-white border-2 border-border p-2 text-xs font-bold text-primary focus:outline-none focus:border-primary" required>
                                        <?php foreach ($dias_semana as $num => $nombre): ?>
                                            <option value="<?php echo $num; ?>"><?php echo "$nombre"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="flex flex-col w-full md:col-span-2">
                                    <label class="text-[10px] font-bold uppercase text-muted mb-1">Inicio</label>
                                    <input type="time" name="start_time" class="w-full bg-white border-2 border-border p-2 text-xs font-bold text-primary focus:outline-none focus:border-primary" required>
                                </div>

                                <div class="flex flex-col w-full md:col-span-2">
                                    <label class="text-[10px] font-bold uppercase text-muted mb-1">Fin</label>
                                    <input type="time" name="end_time" class="w-full bg-white border-2 border-border p-2 text-xs font-bold text-primary focus:outline-none focus:border-primary" required>
                                </div>

                                <div class="md:col-span-7 mt-2">
                                    <button type="submit"
                                        class="flex items-center justify-center p-2 gap-2 rounded-none text-sm text-surface font-black uppercase tracking-widest w-full bg-primary hover:bg-primary-hover transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer">
                                        <i data-lucide="save" class="w-4 h-4"></i>Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($soy_capitan && mysqli_num_rows($res_miembros) > 1): ?>
            <dialog id="transferModal" class="m-auto rounded-none border-2 border-primary p-0 shadow-hard w-[95%] sm:w-full max-w-md">
                <div class="bg-surface p-6">
                    <div class="flex justify-between items-center mb-6 border-b-2 border-subtle pb-4">
                        <h3 class="text-xl font-black text-primary uppercase tracking-tight">Elige a un Capitan</h3>
                        <button onclick="document.getElementById('transferModal').close()" class="text-secondary hover:text-primary cursor-pointer">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <p class="text-sm font-bold text-secondary mb-6 leading-relaxed">
                        Debes elegir a un miembro como capitan para poder abandonar el equipo.
                    </p>

                    <form action="../actions/abandon_team.php" method="POST" class="space-y-6">
                        <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">

                        <div class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            <?php
                            $res_miembros->data_seek(0);
                            while ($m = $res_miembros->fetch_assoc()):
                                if ($m['usu_id'] == $usu_id) continue;
                            ?>
                                <label class="relative flex items-center p-4 bg-subtle border-2 border-transparent hover:border-primary cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-white shadow-sm">
                                    <input type="radio" name="new_captain_id" value="<?php echo $m['usu_id']; ?>" required class="mr-3 accent-primary">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-primary uppercase tracking-tight">
                                            <?php echo htmlspecialchars($m['usu_alias'] ?? $m['usu_username']); ?>
                                        </span>
                                        <span class="text-[10px] font-bold text-secondary font-mono">
                                            @<?php echo htmlspecialchars($m['usu_username']); ?>
                                        </span>
                                    </div>
                                </label>
                            <?php endwhile; ?>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-primary text-white py-4 text-xs font-black uppercase tracking-widest hover:bg-primary-hover transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer flex items-center justify-center gap-2">
                                <i data-lucide="crown" class="w-4 h-4"></i>
                                Confirmar y Salir
                            </button>
                        </div>
                    </form>
                </div>
            </dialog>

            <script>
                function handleAbandon(e) {
                    const memberCount = <?php echo mysqli_num_rows($res_miembros); ?>;
                    const isCaptain = <?php echo $soy_capitan ? 'true' : 'false'; ?>;

                    if (isCaptain && memberCount > 1) {
                        e.preventDefault();
                        document.getElementById('transferModal').showModal();
                        return false;
                    }

                    return confirm('¿Estás seguro de que quieres abandonar el equipo?');
                }
            </script>
        <?php else: ?>
            <script>
                function handleAbandon(e) {
                    return confirm('¿Estás seguro de que quieres abandonar el equipo?');
                }
            </script>
        <?php endif; ?>

    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
