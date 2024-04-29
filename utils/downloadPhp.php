<?php
session_start();


// Verifica si existe un archivo actualizado para descargar en la sesión
if (isset($_SESSION['updatedFilePath']) && file_exists($_SESSION['updatedFilePath'])) {
    $file_path = $_SESSION['updatedFilePath'];
    $file_name = basename($file_path);

    // Log de acceso al archivo
    error_log("Intentando descargar el archivo: " . $file_path, 3, $logPath);

    // Configura los headers adecuados para descargar el archivo
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));

    // Limpia el buffer de salida y transmite el archivo al cliente
    flush();
    readfile($file_path);

    // Log de archivo descargado correctamente
    error_log("Archivo descargado con éxito: " . $file_path, 3, $logPath);

    // Elimina el archivo y limpia la sesión después de la descarga
    unlink($file_path);
    unset($_SESSION['updatedFilePath']);

    exit;
} else {
    // Log de error si el archivo no se encuentra
    error_log("Archivo no encontrado para descargar: " . $_SESSION['updatedFilePath'], 3, $logPath);
    echo "Lo siento, el archivo no está disponible para descargar.";
}
