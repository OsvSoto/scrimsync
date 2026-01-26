<?php
// modules/team/profile/index.php
session_start();
require_once '../../../config/db.php';

// 1. Seguridad
if (!isset($_SESSION['loggedin'])) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];

// 2. Lógica: Traer TODOS los equipos
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

$result = mysqli_query($conn, $sql);

$mis_equipos = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $mis_equipos[] = $row;
    }
}

$tiene_equipos = count($mis_equipos) > 0;
$view = $_GET['view'] ?? 'dashboard';

include '../../../includes/header.php';
include '../../../includes/user_navbar.php'; 
?>

<div class="flex min-h-screen bg-zinc-50">
    
    <div class="hidden md:block fixed left-0 top-0 h-full z-10 pt-16"> 
        <?php include '../../../includes/user_sidebar.php'; ?>
    </div>

    <main class="flex-1 w-full md:ml-64 p-4 pt-24 md:p-8 md:pt-28">
        <?php 
        if ($view === 'create') {
            echo '<div class="mb-6"><a href="index.php" class="inline-flex items-center gap-2 text-sm font-bold text-secondary hover:text-primary transition-colors"><i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a mis equipos</a></div>';
            
            $sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
            $result_juegos = mysqli_query($conn, $sql_juegos);
            
            // Ajustamos el include para buscar en la carpeta correcta
            include '../views/components/form_teams.php'; 
            
        } else {
            if ($tiene_equipos) {
                include '../views/dashboard.php';
            } else {
                include '../views/create.php'; 
            }
        }
        ?>
    </main>
</div>

<?php include '../../../includes/footer.php'; ?>