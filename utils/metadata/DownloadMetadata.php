<?php

use utils\clean\TempCleaner;

session_start();

// Ruta de logs predeterminada
$logPath = ini_get('error_log');

/**
 * Maneja la descarga de un archivo PDF actualizado.
 */
try {
    // Verifica si la variable de sesión 'updatedFilePath' está definida
    if (!isset($_SESSION['updatedFilePath'])) {
        throw new Exception("La variable de sesión 'updatedFilePath' no está definida.");
    }

    $file_path = $_SESSION['updatedFilePath'];

    // Verifica si el archivo existe
    if (!file_exists($file_path)) {
        throw new Exception("Archivo no encontrado: " . $file_path);
    }

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

    // Invoca la clase TempCleaner para limpiar los archivos temporales
    require_once '../clean/TempCleaner.php';

    $tempCleaner = new TempCleaner('/var/tmp/pdfmax2_temps');
    $tempCleaner->clean();

    // Limpia la sesión después de la descarga
    unset($_SESSION['updatedFilePath']);
} catch (Exception $e) {
    error_log($e->getMessage(), 3, $logPath);
    echo "Lo siento, el archivo no está disponible para descargar.";
    exit;
}
exit;
