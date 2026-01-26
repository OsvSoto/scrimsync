<?php
// modules/team/profile/manage.php
session_start();
require_once '../../../config/db.php';

// 1. SEGURIDAD DE SESIÓN
if (!isset($_SESSION['loggedin'])) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];
$equ_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Si no hay ID, volver al inicio
if ($equ_id <= 0) {
    header("Location: index.php");
    exit;
}

// 2. VERIFICAR PERMISOS (CRÍTICO)
// Solo permitimos entrar si el usuario tiene permiso de capitán en ESTE equipo
$sql_permisos = "SELECT * FROM permiso_equipo 
                 WHERE usu_id = '$usu_id' AND equ_id = '$equ_id' 
                 AND (per_modif_horario = 1 OR per_elim_miembro = 1) LIMIT 1";
$res_permisos = mysqli_query($conn, $sql_permisos);

if (mysqli_num_rows($res_permisos) == 0) {
    // Si no es capitán, lo mandamos a la vista pública del equipo (o al index)
    header("Location: view.php?id=$equ_id&error=no_permission");
    exit;
}

// 3. OBTENER DATOS DEL EQUIPO
$sql_equipo = "SELECT * FROM equipo WHERE equ_id = '$equ_id'";
$equipo = mysqli_fetch_assoc(mysqli_query($conn, $sql_equipo));

// 4. OBTENER JUEGOS (Para el select de edición)
$sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
$res_juegos = mysqli_query($conn, $sql_juegos);

// 5. LÓGICA DE BÚSQUEDA DE USUARIOS (Para invitar)
// Esta lógica se ejecuta cuando usas el buscador dentro de esta misma página
$resultados_busqueda = [];
$busqueda = '';

if (isset($_GET['search_user']) && !empty($_GET['search_user'])) {
    $busqueda = trim($_GET['search_user']);
    $busqueda_safe = mysqli_real_escape_string($conn, $busqueda);

    // Buscar usuarios que coincidan con el nombre Y que NO estén ya en el equipo
    $sql_search = "SELECT u.usu_id, u.usu_username, u.usu_alias 
                   FROM usuario u 
                   WHERE (u.usu_username LIKE '%$busqueda_safe%' OR u.usu_alias LIKE '%$busqueda_safe%')
                   AND u.usu_id NOT IN (SELECT usu_id FROM permiso_equipo WHERE equ_id = '$equ_id')
                   LIMIT 5";
    
    $res_search = mysqli_query($conn, $sql_search);
    if($res_search) {
        while($r = mysqli_fetch_assoc($res_search)) {
            $resultados_busqueda[] = $r;
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
        
        <div class="max-w-6xl mx-auto mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="view.php?id=<?php echo $equ_id; ?>" class="inline-flex items-center gap-2 text-sm font-bold text-secondary hover:text-primary transition-colors mb-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a la vista del equipo
                </a>
                <h2 class="text-2xl md:text-3xl font-black text-primary tracking-tight">
                    Gestionar: <span class="text-zinc-800"><?php echo htmlspecialchars($equipo['equ_nombre']); ?></span>
                </h2>
            </div>
            
            <button onclick="alert('Funcionalidad pendiente: Eliminar equipo')" class="self-start sm:self-auto text-rose-600 hover:bg-rose-50 px-3 py-2 rounded-lg transition-colors flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar Equipo
            </button>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="max-w-6xl mx-auto mb-6 bg-emerald-100 border border-emerald-200 text-emerald-800 p-4 rounded-lg text-sm font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <?php 
                    if($_GET['success'] == 'updated') echo "Datos del equipo actualizados correctamente.";
                    if($_GET['success'] == 'invited') echo "Invitación enviada con éxito.";
                ?>
            </div>
        <?php endif; ?>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-zinc-200 rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-lg text-zinc-900 mb-6 flex items-center gap-2 pb-4 border-b border-zinc-50">
                        <i data-lucide="edit-3" class="w-5 h-5 text-primary"></i>
                        Información General
                    </h3>
                    
                    <form action="../actions/update_team.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="equ_id" value="<?php echo $equipo['equ_id']; ?>">
                        
                        <div class="space-y-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                                <div class="w-20 h-20 bg-zinc-100 rounded-xl overflow-hidden border border-zinc-200 flex-shrink-0 relative group">
                                    <?php if($equipo['equ_logo']): ?>
                                        <img src="<?php echo BASE_URL . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-zinc-300">
                                            <i data-lucide="image" class="w-8 h-8"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 w-full">
                                    <label class="block text-xs font-bold uppercase tracking-widest text-secondary mb-2">Cambiar Logo</label>
                                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 transition-colors cursor-pointer">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-widest text-secondary mb-2">Nombre del Equipo</label>
                                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($equipo['equ_nombre']); ?>" required
                                        class="w-full bg-zinc-50 border border-zinc-200 p-3 rounded-lg font-bold text-zinc-800 focus:outline-none focus:border-primary transition-colors">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-widest text-secondary mb-2">Juego Principal</label>
                                    <div class="relative">
                                        <select name="jue_id" class="w-full bg-zinc-50 border border-zinc-200 p-3 rounded-lg font-bold text-zinc-800 focus:outline-none focus:border-primary transition-colors appearance-none cursor-pointer">
                                            <?php 
                                            mysqli_data_seek($res_juegos, 0);
                                            while($j = mysqli_fetch_assoc($res_juegos)): 
                                            ?>
                                                <option value="<?php echo $j['jue_id']; ?>" <?php echo ($j['jue_id'] == $equipo['jue_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($j['jue_nombre']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-500"><i data-lucide="chevron-down" class="w-4 h-4"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-zinc-50 flex justify-end">
                            <button type="submit" class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-lg font-bold text-sm uppercase tracking-widest hover:bg-zinc-800 transition-colors shadow-lg shadow-zinc-200">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white border border-zinc-200 rounded-xl p-6 shadow-sm">
                    <h3 class="font-bold text-lg text-zinc-900 mb-6 flex items-center gap-2 pb-4 border-b border-zinc-50">
                        <i data-lucide="user-plus" class="w-5 h-5 text-primary"></i>
                        Invitar Jugadores
                    </h3>

                    <form action="" method="GET" class="mb-6 relative">
                        <input type="hidden" name="id" value="<?php echo $equ_id; ?>"> 
                        <input type="text" name="search_user" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por usuario o alias..." 
                            class="w-full bg-zinc-50 border border-zinc-200 pl-10 pr-4 py-3 rounded-lg text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                        <i data-lucide="search" class="w-4 h-4 text-zinc-400 absolute left-3 top-3.5"></i>
                        <button type="submit" class="hidden">Buscar</button> </form>

                    <?php if (!empty($busqueda)): ?>
                        <div class="space-y-3 animate-in fade-in slide-in-from-top-2">
                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Resultados de búsqueda:</h4>
                            
                            <?php if (count($resultados_busqueda) > 0): ?>
                                <?php foreach($resultados_busqueda as $user): ?>
                                    <div class="flex items-center justify-between p-3 bg-zinc-50 hover:bg-zinc-100 rounded-lg border border-zinc-100 transition-colors">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="w-8 h-8 bg-white border border-zinc-200 rounded-full flex items-center justify-center text-zinc-400 flex-shrink-0">
                                                <i data-lucide="user" class="w-4 h-4"></i>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-sm font-bold text-zinc-900 truncate"><?php echo htmlspecialchars($user['usu_alias'] ?? $user['usu_username']); ?></p>
                                                <p class="text-[10px] text-zinc-500 font-mono">@<?php echo htmlspecialchars($user['usu_username']); ?></p>
                                            </div>
                                        </div>
                                        
                                        <form action="../actions/invite_member.php" method="POST">
                                            <input type="hidden" name="equ_id" value="<?php echo $equ_id; ?>">
                                            <input type="hidden" name="target_usu_id" value="<?php echo $user['usu_id']; ?>">
                                            <button type="submit" class="text-xs font-bold text-primary hover:text-zinc-900 bg-primary/5 hover:bg-primary/20 px-3 py-1.5 rounded transition-colors whitespace-nowrap">
                                                Invitar +
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-6 border border-dashed border-zinc-200 rounded-lg">
                                    <p class="text-sm text-zinc-500">No se encontraron usuarios.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 px-4 bg-zinc-50/50 rounded-lg border border-dashed border-zinc-200">
                            <i data-lucide="search" class="w-8 h-8 text-zinc-300 mx-auto mb-2"></i>
                            <p class="text-xs text-zinc-500">Busca el @usuario de tu amigo para añadirlo al roster.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </main>
</div>

<?php include '../../../includes/footer.php'; ?>