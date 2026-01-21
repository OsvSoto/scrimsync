<?php
// modules/admin/games/controller_juego.php
session_start();
require_once '../../../config/db.php';

// Validar que vengan datos y usuario sea admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['rol']) && $_SESSION['rol'] == 0) {

    // ENTRADAS
    $p_op = $_POST['p_op']; // "C", "M", o "E"
    
    // Recogemos datos (Ahora incluimos gen_id)
    $jue_id     = isset($_POST['jue_id']) ? $_POST['jue_id'] : null;
    $jue_nombre = isset($_POST['jue_nombre']) ? trim($_POST['jue_nombre']) : '';
    $gen_id     = isset($_POST['gen_id']) ? $_POST['gen_id'] : null; // Nuevo campo

    // MÓDULO: Mantenedor_Juego
    switch ($p_op) {
        
        // Case: CREAR
        case 'C':
            $sql = "INSERT INTO juego (jue_nombre, gen_id) VALUES (?, ?)";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
        
                mysqli_stmt_bind_param($stmt, "si", $jue_nombre, $gen_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: index.php?msg=creado");
                } else {
                    header("Location: index.php?error=db");
                }
                mysqli_stmt_close($stmt);
            }
            break;

        // Case: MODIFICAR
        case 'M':
            $sql = "UPDATE juego SET jue_nombre = ?, gen_id = ? WHERE jue_id = ?";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // "sii" = String, Integer, Integer 
                mysqli_stmt_bind_param($stmt, "sii", $jue_nombre, $gen_id, $jue_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: index.php?msg=modificado");
                } else {
                    header("Location: index.php?error=db");
                }
                mysqli_stmt_close($stmt);
            }
            break;

        // Case: ELIMINAR 
        case 'E':
            $sql = "DELETE FROM juego WHERE jue_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $jue_id);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: index.php?msg=eliminado");
                } else {
                    header("Location: index.php?error=db");
                }
                mysqli_stmt_close($stmt);
            }
            break;

        default:
            header("Location: index.php?error=op_invalida");
            break;
    }
    
    mysqli_close($conn);

} else {
    header("Location: index.php");
}
?>