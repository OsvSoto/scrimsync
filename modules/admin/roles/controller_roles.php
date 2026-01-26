<?php
// modules/admin/roles/controller_roles.php
session_start();
require_once '../../../config/db.php';

// Validar que vengan datos y usuario sea admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['rol']) && $_SESSION['rol'] == 0) {

    // ENTRADAS
    $p_op = $_POST['p_op']; // "C", "M", o "E"

    // Recogemos datos
    $rol_id     = isset($_POST['rol_id']) ? $_POST['rol_id'] : null;
    $rol_nombre = isset($_POST['rol_nombre']) ? trim($_POST['rol_nombre']) : '';
    $jue_id     = isset($_POST['jue_id']) ? $_POST['jue_id'] : null;

    // MÓDULO: Mantenedor_Roles (rol_predefinido)
    switch ($p_op) {

        // Case: CREAR
        case 'C':
            $sql = "INSERT INTO rol_predefinido (rol_nombre, jue_id) VALUES (?, ?)";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $rol_nombre, $jue_id);

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
            $sql = "UPDATE rol_predefinido SET rol_nombre = ?, jue_id = ? WHERE rol_id = ?";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                // "sii" = String, Integer, Integer
                mysqli_stmt_bind_param($stmt, "sii", $rol_nombre, $jue_id, $rol_id);

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
            $sql = "DELETE FROM rol_predefinido WHERE rol_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $rol_id);
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
