<?php
// modules/functions/process_image.php
function uploadImage($file, $subfolder, $prefix, $id)
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'upload_error'];
    }

    $fileSize = $file['size'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    if ($fileSize > $maxFileSize) {
        return ['success' => false, 'error' => 'file_too_large'];
    }

    $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $detectedMimeType = $finfo->file($file['tmp_name']);

    if (!in_array($detectedMimeType, $allowedMimeTypes)) {
        return ['success' => false, 'error' => 'invalid_file_type'];
    }

    try {
        // Definir directorio de subida relativo a este archivo
        // modules/functions/../../uploads/ => uploads/
        $uploadBaseDir = __DIR__ . '/../../uploads/';
        $targetDir = $uploadBaseDir . $subfolder . '/';

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newFileName = $prefix . '_' . $id . '_' . bin2hex(random_bytes(8)) . '.jpg';
        $destPath = $targetDir . $newFileName;

        // Procesamiento Imagick
        $image = new Imagick($file['tmp_name']);
        $image->setImageFormat('jpeg');
        $image->setImageCompressionQuality(85);
        $image->cropThumbnailImage(300, 300);
        $image->writeImage($destPath);
        $image->clear();
        $image->destroy();

        // Retornar ruta relativa para la BD
        return [
            'success' => true,
            'path' => 'uploads/' . $subfolder . '/' . $newFileName,
            'error' => null
        ];

    }
    catch (Exception $e) {
        error_log("Error Imagick: " . $e->getMessage());
        return ['success' => false, 'error' => 'image_processing_error'];
    }
}
