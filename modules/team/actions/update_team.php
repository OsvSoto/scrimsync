    <?php
// modules/team/actions/update_team.php
session_start();
require_once '../../../config/db.php';

//SEGURIDAD: Solo aceptamos POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../profile/index.php");
    exit;
}

//RECIBIR DATOS
$equ_id = (int)$_POST['equ_id'];
$nombre = trim($_POST['nombre']);
$jue_id = (int)$_POST['jue_id'];
$usu_id = $_SESSION['usu_id']; // El usuario que intenta hacer el cambio

//VERIFICAR PERMISOS 
$check_sql = "SELECT * FROM permiso_equipo 
              WHERE usu_id = '$usu_id' AND equ_id = '$equ_id' 
              AND (per_modif_horario = 1 OR per_elim_miembro = 1)";
$check_res = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_res) == 0) {
    die("No tienes permisos para editar este equipo.");
}

// PROCESAR LOGO 
$campo_logo = ""; 
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
    $directorio = '../../../uploads/teams/';
    if (!file_exists($directorio)) mkdir($directorio, 0777, true);
    
    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = 'team_' . $equ_id . '_' . time() . '.' . $ext;
    $ruta_final = $directorio . $nuevo_nombre;
    
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_final)) {
        // Guardamos la ruta relativa para la BD
        $ruta_bd = 'uploads/teams/' . $nuevo_nombre;
        $campo_logo = ", equ_logo = '$ruta_bd'";
    }
}

//ACTUALIZAR BASE DE DATOS
$sql_update = "UPDATE equipo SET 
               equ_nombre = '$nombre', 
               jue_id = '$jue_id' 
               $campo_logo 
               WHERE equ_id = '$equ_id'";

if (mysqli_query($conn, $sql_update)) {
    // Éxito: Volvemos al manage.php con un mensaje (opcional)
    header("Location: ../profile/manage.php?id=$equ_id&success=updated");
} else {
    echo "Error al actualizar: " . mysqli_error($conn);
}
?>