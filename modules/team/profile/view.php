<?php
// modules/team/profile/view.php
session_start();
require_once '../../../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$equ_id = (int)$_GET['id'];
$usu_id = $_SESSION['usu_id'] ?? 0;

$sql_equipo = "SELECT e.*, j.jue_nombre 
               FROM equipo e 
               LEFT JOIN juego j ON e.jue_id = j.jue_id 
               WHERE e.equ_id = '$equ_id'";
$res_equipo = mysqli_query($conn, $sql_equipo);

if (mysqli_num_rows($res_equipo) == 0) {
    die("El equipo no existe.");
}
$equipo = mysqli_fetch_assoc($res_equipo);

$sql_miembros = "SELECT u.usu_username, u.usu_alias, pe.* FROM permiso_equipo pe
                 INNER JOIN usuario u ON pe.usu_id = u.usu_id
                 WHERE pe.equ_id = '$equ_id'"; 
$res_miembros = mysqli_query($conn, $sql_miembros);

$soy_capitan = false;
$soy_miembro = false;

if ($usu_id > 0) {
    // Buscamos mis permisos en este equipo específico
    $sql_mis_permisos = "SELECT * FROM permiso_equipo 
                         WHERE usu_id = '$usu_id' AND equ_id = '$equ_id' LIMIT 1";
    $res_mis_permisos = mysqli_query($conn, $sql_mis_permisos);
    
    if (mysqli_num_rows($res_mis_permisos) > 0) {
        $mis_datos = mysqli_fetch_assoc($res_mis_permisos);
        $soy_miembro = true;
        // Definimos capitán si tiene permiso de eliminar miembros o modificar horario
        if ($mis_datos['per_elim_miembro'] == 1 || $mis_datos['per_modif_horario'] == 1) {
            $soy_capitan = true;
        }
    }
}

include '../../../includes/header.php';
include '../../../includes/user_navbar.php'; 
?>

<div class="flex min-h-screen bg-zinc-50">
    
    <div class="hidden md:block fixed left-0 top-0 h-full z-10 pt-16"> 
        <?php include '../../../includes/user_sidebar.php'; ?>
    </div>

    <main class="flex-1 w-full md:ml-64 p-4 pt-24 md:p-8 md:pt-28">
        
        <div class="max-w-6xl mx-auto mb-6">
            <a href="index.php" class="inline-flex items-center gap-2 text-sm font-bold text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a mis equipos
            </a>
        </div>

        <div class="max-w-6xl mx-auto bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm mb-8">
            <div class="h-32 sm:h-48 bg-zinc-900 relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
                
                <div class="absolute top-4 right-4">
                    <span class="bg-black/50 backdrop-blur text-white text-xs font-bold px-3 py-1 rounded-full border border-white/20 uppercase tracking-widest">
                        <?php echo htmlspecialchars($equipo['jue_nombre']); ?>
                    </span>
                </div>
            </div>
            
            <div class="px-6 pb-6 sm:px-10 relative">
                <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-12 mb-6 gap-6">
                    <div class="w-32 h-32 rounded-2xl border-4 border-white bg-white shadow-lg overflow-hidden flex-shrink-0">
                        <?php if($equipo['equ_logo']): ?>
                            <img src="<?php echo BASE_URL . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-zinc-100 flex items-center justify-center text-4xl font-black text-zinc-300">
                                <?php echo strtoupper(substr($equipo['equ_nombre'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex-1 text-center sm:text-left">
                        <h1 class="text-3xl sm:text-4xl font-black text-zinc-900 tracking-tight mb-1">
                            <?php echo htmlspecialchars($equipo['equ_nombre']); ?>
                        </h1>
                    </div>

                    <?php if ($soy_capitan): ?>
                        <a href="manage.php?id=<?php echo $equipo['equ_id']; ?>" 
                           class="bg-primary text-white px-6 py-3 rounded-lg font-bold uppercase tracking-widest text-sm hover:bg-zinc-800 transition-all shadow-lg shadow-zinc-200 flex items-center gap-2">
                            <i data-lucide="settings-2" class="w-4 h-4"></i>
                            Gestionar Equipo
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-black text-primary tracking-tight">Roster Activo</h3>
                    <span class="bg-zinc-100 text-zinc-600 px-2 py-1 rounded text-xs font-bold">
                        <?php echo mysqli_num_rows($res_miembros); ?> Jugadores
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php 
                    // Reiniciamos puntero por si acaso
                    if(mysqli_num_rows($res_miembros) > 0):
                        mysqli_data_seek($res_miembros, 0);
                        while($miembro = mysqli_fetch_assoc($res_miembros)): 
                            // Determinar si este miembro es capitan
                            $es_lider = ($miembro['per_modif_horario'] == 1 || $miembro['per_elim_miembro'] == 1);
                    ?>
                        <div class="bg-white p-4 rounded-xl border border-zinc-200 flex items-center gap-4 hover:border-zinc-300 transition-colors">
                            <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center text-zinc-400">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-zinc-900">
                                        <?php echo htmlspecialchars($miembro['usu_alias'] ?? $miembro['usu_username']); ?>
                                    </h4>
                                    <?php if($es_lider): ?>
                                        <i data-lucide="crown" class="w-3 h-3 text-amber-500 fill-amber-500"></i>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-secondary font-mono">@<?php echo htmlspecialchars($miembro['usu_username']); ?></p>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <div class="col-span-2 text-center py-8 text-secondary italic">
                            No hay miembros en este equipo aún.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div>

<?php include '../../../includes/footer.php'; ?>