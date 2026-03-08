<?php
// modules/admin/genero/controller_genero.php
session_start();
require_once '../../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['tipo']) && $_SESSION['tipo'] == 0) {

    $p_op = $_POST['p_op']; // "C", "M", o "E"

    $gen_id     = isset($_POST['gen_id']) ? $_POST['gen_id'] : null;
    $gen_nombre = isset($_POST['gen_nombre']) ? trim($_POST['gen_nombre']) : '';

    switch ($p_op) {

        // Case: CREAR
        case 'C':
            $sql = "INSERT INTO genero (gen_nombre) VALUES (?)";
            if ($conn->execute_query($sql, [$gen_nombre])) {
                header("Location: index.php?msg=creado");
            } else {
                header("Location: index.php?error=db");
            }
            break;

        // Case: MODIFICAR
        case 'M':
            $sql = "UPDATE genero SET gen_nombre = ? WHERE gen_id = ?";
            if ($conn->execute_query($sql, [$gen_nombre, $gen_id])) {
                header("Location: index.php?msg=modificado");
            } else {
                header("Location: index.php?error=db");
            }
            break;

        // Case: ELIMINAR
        case 'E':
            $sql = "DELETE FROM genero WHERE gen_id = ?";
            try {
                if ($conn->execute_query($sql, [$gen_id])) {
                    header("Location: index.php?msg=eliminado");
                } else {
                    header("Location: index.php?error=db");
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1451) { // error: no se puede eliminar por que es clave foranea en otra tabla
                    header("Location: index.php?error=dependency");
                } else {
                    header("Location: index.php?error=db");
                }
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
