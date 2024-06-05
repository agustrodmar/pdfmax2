<?php

session_start(); // Inicia la sesión

// Verificar si el usuario tiene permiso para descargar el archivo
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    echo "Acceso denegado. Por favor, inicie sesión para descargar el archivo.";
    exit;
}

require_once 'PdfConverterController.php';

try {
    $controller = new PdfConverterController();
    $controller->download();
} catch (Exception $e) {
    echo "Error durante la descarga: " . $e->getMessage();
}
