<?php
// modules/admin/roles/controller_roles.php
session_start();
require_once '../../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['tipo']) && $_SESSION['tipo'] == 0) {

    $p_op = $_POST['p_op']; // "C", "M", o "E"

    $rol_id     = isset($_POST['rol_id']) ? $_POST['rol_id'] : null;
    $rol_nombre = isset($_POST['rol_nombre']) ? trim($_POST['rol_nombre']) : '';
    $jue_id     = isset($_POST['jue_id']) ? $_POST['jue_id'] : null;

    switch ($p_op) {

        // Case: CREAR
        case 'C':
            $sql = "INSERT INTO rol_predefinido (rol_nombre, jue_id) VALUES (?, ?)";
            if ($conn->execute_query($sql, [$rol_nombre, $jue_id])) {
                header("Location: index.php?msg=creado");
            } else {
                header("Location: index.php?error=db");
            }
            break;

        // Case: MODIFICAR
        case 'M':
            $sql = "UPDATE rol_predefinido SET rol_nombre = ?, jue_id = ? WHERE rol_id = ?";
            if ($conn->execute_query($sql, [$rol_nombre, $jue_id, $rol_id])) {
                header("Location: index.php?msg=modificado");
            } else {
                header("Location: index.php?error=db");
            }
            break;

        // Case: ELIMINAR
        case 'E':
            $sql = "DELETE FROM rol_predefinido WHERE rol_id = ?";
            if ($conn->execute_query($sql, [$rol_id])) {
                header("Location: index.php?msg=eliminado");
            } else {
                header("Location: index.php?error=db");
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
