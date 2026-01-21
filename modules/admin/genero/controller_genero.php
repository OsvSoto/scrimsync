<?php
// modules/admin/genero/controller_genero.php
session_start();
require_once '../../../config/db.php';

// Validar que vengan datos y usuario sea admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['rol']) && $_SESSION['rol'] == 0) {

    // ENTRADAS
    $p_op = $_POST['p_op']; // "C", "M", o "E"

    // Recogemos datos
    $gen_id     = isset($_POST['gen_id']) ? $_POST['gen_id'] : null;
    $gen_nombre = isset($_POST['gen_nombre']) ? trim($_POST['gen_nombre']) : '';

    // MÓDULO: Mantenedor_Genero
    switch ($p_op) {

        // Case: CREAR
        case 'C':
            $sql = "INSERT INTO genero (gen_nombre) VALUES (?)";

            if ($stmt = mysqli_prepare($conn, $sql)) {

                mysqli_stmt_bind_param($stmt, "s", $gen_nombre);

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
            $sql = "UPDATE genero SET gen_nombre = ? WHERE gen_id = ?";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                // "si" = String, Integer
                mysqli_stmt_bind_param($stmt, "si", $gen_nombre, $gen_id);

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
            $sql = "DELETE FROM genero WHERE gen_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $gen_id);
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
