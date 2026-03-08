<?php
// modules/admin/backup/controller_backup.php
ob_start();
session_start();
require_once '../../../config/db.php';

// Validar Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] != 0) {
    header("Location: ../../../modules/auth/login.php");
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'export') {
    $tables = array();
    $result = mysqli_query($conn, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    $sql = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
    $sql .= "SET time_zone = '+00:00';\n\n";

    foreach ($tables as $table) {
        $res = mysqli_query($conn, "SHOW CREATE TABLE `$table`") or die(mysqli_error($conn));
        $row = mysqli_fetch_row($res);
        $sql .= "-- --------------------------------------------------------\n";
        $sql .= "-- Estructura para la tabla `$table`\n";
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql .= $row[1] . ";\n\n";

        $res = mysqli_query($conn, "SELECT * FROM `$table`") or die(mysqli_error($conn));
        $num_rows = mysqli_num_rows($res);
        if ($num_rows > 0) {
            $sql .= "-- Volcado de datos para la tabla `$table`\n";
            $sql .= "INSERT INTO `$table` VALUES\n";
            $i = 0;
            while ($row = mysqli_fetch_row($res)) {
                $sql .= "(";
                for ($j = 0; $j < count($row); $j++) {
                    if (isset($row[$j])) {
                        $escaped = mysqli_real_escape_string($conn, $row[$j]);
                        $sql .= "'" . $escaped . "'";
                    } else {
                        $sql .= 'NULL';
                    }
                    if ($j < (count($row) - 1)) {
                        $sql .= ', ';
                    }
                }
                $sql .= ")";
                if ($i < ($num_rows - 1)) {
                    $sql .= ",\n";
                } else {
                    $sql .= ";\n";
                }
                $i++;
            }
            $sql .= "\n";
        }
    }

    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    $temp_sql = tempnam(sys_get_temp_dir(), 'sql');
    file_put_contents($temp_sql, $sql);

    $zip_filename = "backup_scrimsync_" . date('Y-m-d_H-i-s') . ".zip";
    $temp_zip = tempnam(sys_get_temp_dir(), 'zip');

    $zip = new ZipArchive();
    if ($zip->open($temp_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die("Could not open zip file");
    }
    $zip->addFile($temp_sql, "scrimsync_dump.sql");
    $zip->close();

    if (ob_get_length()) ob_clean();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
    header('Content-Length: ' . filesize($temp_zip));
    header('Pragma: no-cache');
    header('Expires: 0');
    readfile($temp_zip);

    unlink($temp_sql);
    unlink($temp_zip);
    exit;
} elseif ($action == 'import') {
    if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: index.php?error=upload_failed");
        exit;
    }

    $zip = new ZipArchive();
    if ($zip->open($_FILES['backup_file']['tmp_name']) === TRUE) {
        $sql_content = $zip->getFromName('scrimsync_dump.sql');
        $zip->close();

        if ($sql_content === FALSE) {
            header("Location: index.php?error=no_sql_in_zip");
            exit;
        }

        if (mysqli_multi_query($conn, $sql_content)) {
            do {
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_more_results($conn) && mysqli_next_result($conn));

            header("Location: index.php?msg=restored");
        } else {
            $error = mysqli_error($conn);
            header("Location: index.php?error=" . urlencode("SQL Error: $error"));
        }
    } else {
        header("Location: index.php?error=bad_zip");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
