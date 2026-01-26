<?php
// modules/team/actions/create_teams.php
session_start();
require_once '../../../config/db.php';

// 1. Verificar seguridad básica
if (!isset($_SESSION['loggedin'])) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}

// 2. Verificar que es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../profile/index.php"); 
    exit;
}

// 3. Recoger datos
$usu_id = $_SESSION['usu_id'];
$nombre = trim($_POST['nombre']);

// Recibir ID del juego
$jue_id = isset($_POST['jue_id']) ? (int)$_POST['jue_id'] : 0;

// Validaciones
if (empty($nombre)) {
    die("Error: El nombre es obligatorio.");
}
if ($jue_id <= 0) {
    die("Error: Debes seleccionar un juego válido.");
}

// 4. Manejo de la Imagen (Logo)
$ruta_logo = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
    $directorio_destino = '../../../uploads/teams/';
    
    if (!file_exists($directorio_destino)) {
        mkdir($directorio_destino, 0777, true);
    }

    $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = 'team_' . $usu_id . '_' . time() . '.' . $extension;
    $ruta_completa = $directorio_destino . $nombre_archivo;
    
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_completa)) {
        // Guardamos la ruta relativa para la BD
        $ruta_logo = 'uploads/teams/' . $nombre_archivo;
    }
}

// =========================================================
// 5. INICIO DE LA TRANSACCIÓN 
// =========================================================
mysqli_begin_transaction($conn);

try {
    // Insertar el Equipo
    $sql_equipo = "INSERT INTO equipo (usu_id, jue_id, equ_nombre, equ_logo) VALUES (?, ?, ?, ?)";
    
    $stmt_equipo = mysqli_prepare($conn, $sql_equipo);
    mysqli_stmt_bind_param($stmt_equipo, "iiss", $usu_id, $jue_id, $nombre, $ruta_logo);
    
    if (!mysqli_stmt_execute($stmt_equipo)) {
        throw new Exception("Error al crear el equipo: " . mysqli_error($conn));
    }
    
    $nuevo_equ_id = mysqli_insert_id($conn);
    
    // Insertar los Permisos (Hacer al usuario Capitán)
    // Tabla permiso_equipo con todos los poderes (1)
    $sql_permisos = "INSERT INTO permiso_equipo 
                    (usu_id, equ_id, rol_id, per_modif_horario, per_enviar_scrim, per_elim_miembro) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        $rol_id = null;
        $permiso_si = 1; // 1 = Verdadero (Tiene permiso)
    
    $stmt_permisos = mysqli_prepare($conn, $sql_permisos);
    mysqli_stmt_bind_param($stmt_permisos, "iiiiii", 
        $usu_id, 
        $nuevo_equ_id, 
        $rol_id, 
        $permiso_si, // Puede modificar horario
        $permiso_si, // Puede enviar scrims
        $permiso_si  // Puede eliminar miembros
    );

    if (!mysqli_stmt_execute($stmt_permisos)) {
        throw new Exception("Error al asignar permisos de capitán.");
    }
    mysqli_commit($conn);
    
    // Redirigimos al dashboard 
    header("Location: ../profile/index.php?success=team_created");
    exit;

} catch (Exception $e) {
    // SI ALGO FALLA: Deshacemos todo
    mysqli_rollback($conn);
    
    // Borramos la imagen si se subió, para no dejar basura
    if ($ruta_logo && file_exists('../../../' . $ruta_logo)) {
        unlink('../../../' . $ruta_logo);
    }
    
    die("Ocurrió un error fatal: " . $e->getMessage());
}
?>