<?php
// modules/team/profile/index.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}
$usu_id = $_SESSION['usu_id'];

$sql = "SELECT
            pe.*,
            e.equ_nombre,
            e.equ_logo,
            e.equ_id,
            j.jue_nombre
        FROM permiso_equipo pe
        INNER JOIN equipo e ON pe.equ_id = e.equ_id
        LEFT JOIN juego j ON e.jue_id = j.jue_id
        WHERE pe.usu_id = '$usu_id'";

// TODO: refactor a prepare->bind->execute
$result = mysqli_query($conn, $sql);

$mis_equipos = [];
$team_ids = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $mis_equipos[] = $row;
        $team_ids[] = $row['equ_id'];
    }
}

$member_counts = [];
if (!empty($team_ids)) {
    $ids_str = implode(',', array_map('intval', $team_ids));

    $sql_counts = "SELECT equ_id, COUNT(*) as total
                   FROM permiso_equipo
                   WHERE equ_id IN ($ids_str)
                   GROUP BY equ_id";

    $res_counts = mysqli_query($conn, $sql_counts);

    if ($res_counts) {
        while ($row = mysqli_fetch_assoc($res_counts)) {
            $member_counts[$row['equ_id']] = $row['total'];
        }
    }
}

$tiene_equipos = count($mis_equipos) > 0;
$view = $_GET['view'] ?? 'dashboard';

include '../../../includes/header.php';
include '../../../includes/user_navbar.php';
?>

<div class="flex min-h-screen bg-zinc-50">

    <!--
  <div class="hidden md:block fixed left-0 top-0 h-full z-10 pt-16">
    <?php include '../../../includes/user_sidebar.php'; ?>
  </div> -->

    <main class="flex-1 w-full pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div class="mb-6 bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_msg']) {
                                case 'team_created':
                                    echo "Equipo creado exitosamente.";
                                    break;
                                case 'quit_delete':
                                    echo "Equipo eliminado correctamente.";
                                    break;
                                case 'team_deleted':
                                    echo "Equipo eliminado permanentemente.";
                                    break;
                                case 'quit':
                                    echo "Has abandonado el equipo correctamente.";
                                    break;
                                case 'role_assigned':
                                    echo "Rol asignado correctamente.";
                                    break;
                                case 'kicked':
                                    echo "Miembro expulsado correctamente.";
                                    break;
                            }
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
                <div class="mb-6 bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            switch ($_SESSION['flash_error']) {
                                case 'name_required':
                                    echo "El nombre del equipo es obligatorio.";
                                    break;
                                case 'game_required':
                                    echo "Debes seleccionar un juego.";
                                    break;
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
                                    echo "Error interno del sistema.";
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
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php
            if ($view === 'create') {
                echo '<div class="mb-6"><a href="index.php" class="inline-flex items-center gap-2 text-sm font-bold text-secondary hover:text-primary transition-colors"><i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a mis equipos</a></div>';

                $sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
                $result_juegos = mysqli_query($conn, $sql_juegos);

                include '../views/components/form_teams.php';
            } else {
                if ($tiene_equipos) {
                    include '../views/dashboard.php';
                } else {
                    include '../views/create.php';
                }
            }
            ?>
        </div>
    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
