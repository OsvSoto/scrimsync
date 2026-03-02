<?php
// modules/team/profile/manage.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];
$equ_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($equ_id <= 0) {
    header("Location: index.php");
    exit;
}

// Solo el capitan puede entrar
$sql_permisos = "SELECT * FROM permiso_equipo
                 WHERE usu_id = ? AND equ_id = ?
                 AND (per_modif_horario = 1 OR per_elim_miembro = 1) LIMIT 1";
$res_permisos = $conn->execute_query($sql_permisos, [$usu_id, $equ_id]);

if (mysqli_num_rows($res_permisos) == 0) {
    header("Location: view.php?id=$equ_id&error=no_permission");
    exit;
}

$sql_equipo = "SELECT * FROM equipo WHERE equ_id = ?";
$equipo = $conn->execute_query($sql_equipo, [$equ_id])->fetch_assoc();

$sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
$res_juegos = $conn->query($sql_juegos);

$resultados_busqueda = [];
$busqueda = '';

if (isset($_GET['search_user']) && !empty($_GET['search_user'])) {
    $busqueda = trim($_GET['search_user']);

    // usuarios que coiniciden y no estan en el equipo
    $sql_search = "SELECT u.usu_id, u.usu_username, u.usu_alias
                   FROM usuario u
                   WHERE (u.usu_username LIKE ? OR u.usu_alias LIKE ?)
                   AND u.usu_id NOT IN (SELECT usu_id FROM permiso_equipo WHERE equ_id = ?)
                   LIMIT 5";

    $search_param = "%$busqueda%";
    $res_search = $conn->execute_query($sql_search, [$search_param, $search_param, $equ_id]);
    if ($res_search) {
        while ($r = $res_search->fetch_assoc()) {
            $resultados_busqueda[] = $r;
        }
    }
}

include '../../../includes/header.php';
include '../../../includes/user_navbar.php';
?>

<div class="flex min-h-screen bg-zinc-50">

    <div class="hidden md:block fixed left-0 top-0 h-full z-10 pt-16"></div>
    <main class="flex-1 w-full pt-16 pb-8">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="view.php?id=<?php echo $equ_id; ?>" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-secondary hover:text-primary transition-colors mb-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a la vista del equipo
                </a>
                <h2 class="text-2xl md:text-3xl font-black text-primary uppercase tracking-tight">
                    Gestionar: <span class="text-primary-hover"><?php echo htmlspecialchars($equipo['equ_nombre']); ?></span>
                </h2>
            </div>
        </div>

        <?php if (isset($_SESSION['flash_msg'])): ?>
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_msg']) {
                                case 'updated':
                                    echo "Datos del equipo actualizados correctamente.";
                                    break;
                                case 'invited':
                                    echo "Invitación enviada con éxito.";
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
                <div class="bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_error']) {
                                case 'file_too_large':
                                    echo "La imagen es muy grande (Máx 5MB).";
                                    break;
                                case 'invalid_file_type':
                                    echo "Formato no válido. Solo JPG, PNG o WebP.";
                                    break;
                                case 'image_processing_error':
                                    echo "Error al procesar la imagen.";
                                    break;
                                case 'db_error':
                                    echo "Error al actualizar la base de datos.";
                                    break;
                                case 'no_permission':
                                    echo "No tienes permisos para realizar esta acción.";
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

        <?php if (isset($_SESSION['debug_error'])): ?>
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 bg-primary border-2 border-error-border p-4 shadow-hard-error">
                <h4 class="text-error-border font-black text-xs uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i data-lucide="bug" class="w-4 h-4"></i> Debug Info
                </h4>
                <pre class="text-xs text-white font-mono overflow-x-auto whitespace-pre-wrap"><?php echo htmlspecialchars($_SESSION['debug_error']); ?></pre>
                <div class="mt-2 text-right">
                    <span class="text-[10px] text-secondary cursor-pointer hover:text-white" onclick="this.parentElement.parentElement.remove()">[Cerrar]</span>
                </div>
            </div>
            <?php unset($_SESSION['debug_error']); ?>
        <?php endif; ?>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface border-2 border-primary p-6 shadow-hard">
                    <h3 class="font-black text-lg text-primary mb-6 flex items-center gap-2 pb-4 border-b-2 border-subtle uppercase tracking-tight">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                        Información General
                    </h3>

                    <form action="../actions/update_team.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">

                        <div class="space-y-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                                <div class="w-20 h-20 bg-subtle border-2 border-primary flex-shrink-0 relative group">
                                    <?php if ($equipo['equ_logo']): ?>
                                        <img src="<?php echo "../../../" . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-muted">
                                            <i data-lucide="image" class="w-8 h-8"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 w-full">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2">Cambiar Logo</label>
                                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-secondary file:mr-4 file:py-2 file:px-4 file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-subtle file:text-primary hover:file:bg-border transition-colors cursor-pointer">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2">Nombre del Equipo</label>
                                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($equipo['equ_nombre']); ?>" required
                                        class="w-full bg-subtle border-2 border-border p-3 font-bold text-sm text-primary focus:outline-none focus:border-primary transition-colors">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2">Juego Principal</label>
                                    <div class="relative">
                                        <select name="jue_id" class="w-full bg-subtle border-2 border-border p-3 font-bold text-sm text-primary focus:outline-none focus:border-primary transition-colors appearance-none cursor-pointer">
                                            <?php
                                            $res_juegos->data_seek(0);
                                            while ($j = $res_juegos->fetch_assoc()):
                                            ?>
                                                <option value="<?php echo $j['jue_id']; ?>" <?php echo ($j['jue_id'] == $equipo['jue_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($j['jue_nombre']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-secondary"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t-2 border-subtle flex justify-end">
                            <button type="submit" class="w-full sm:w-auto bg-primary text-white px-6 py-3 font-black text-xs uppercase tracking-widest hover:bg-primary-hover transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] flex items-center justify-center gap-2 cursor-pointer">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface border-2 border-primary p-6 shadow-hard">
                    <h3 class="font-black text-lg text-primary mb-6 flex items-center gap-2 pb-4 border-b-2 border-subtle uppercase tracking-tight">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        Invitar Jugadores
                    </h3>

                    <form action="" method="GET" class="mb-6 relative">
                        <input type="hidden" name="id" value="<?php echo $equ_id; ?>">
                        <input type="text" name="search_user" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por usuario o alias..."
                            class="text-primary bg-surface border-2 border-border pl-10 pr-4 py-3 text-sm font-bold hover:border-primary focus:border-primary outline-none w-full shadow-hard-sm hover:shadow-hard focus:shadow-hard transition-all">
                        <i data-lucide="search" class="w-4 h-4 text-muted absolute left-3 top-3.5"></i>
                        <button type="submit" class="hidden">Buscar</button>
                    </form>

                    <?php if (!empty($busqueda)): ?>
                        <div class="space-y-3 animate-in fade-in slide-in-from-top-2">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-muted mb-2">Resultados de búsqueda:</h4>

                            <?php if (count($resultados_busqueda) > 0): ?>
                                <?php foreach ($resultados_busqueda as $user): ?>
                                    <div class="flex items-center justify-between p-3 bg-subtle hover:bg-border border-2 border-border transition-colors">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="w-8 h-8 bg-surface border-2 border-border flex items-center justify-center text-muted flex-shrink-0">
                                                <i data-lucide="user" class="w-4 h-4"></i>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-sm font-bold text-primary truncate"><?php echo htmlspecialchars($user['usu_alias'] ?? $user['usu_username']); ?></p>
                                                <p class="text-[10px] text-secondary font-mono">@<?php echo htmlspecialchars($user['usu_username']); ?></p>
                                            </div>
                                        </div>

                                        <form action="../actions/invite_member.php" method="POST">
                                            <input type="hidden" name="equ_id" value="<?php echo $equ_id; ?>">
                                            <input type="hidden" name="target_usu_id" value="<?php echo $user['usu_id']; ?>">
                                            <button type="submit" class="text-[10px] font-black text-primary hover:text-white bg-primary/10 hover:bg-primary px-3 py-1.5 transition-colors whitespace-nowrap uppercase tracking-wider">
                                                Invitar +
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-6 border-2 border-dashed border-border">
                                    <p class="text-sm text-secondary">No se encontraron usuarios.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 px-4 bg-subtle/50 border-2 border-dashed border-border">
                            <i data-lucide="search" class="w-8 h-8 text-muted mx-auto mb-2"></i>
                            <p class="text-xs text-secondary font-bold">Busca el @usuario de tu amigo para añadirlo al roster.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
