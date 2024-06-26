<?php

session_start();
require_once __DIR__ . '/PdfMetaModifierController.php'; // Ajustar la ruta según la nueva ubicación

$controller = new PdfMetaModifierController();


/**
 * Maneja la actualización de los metadatos del archivo PDF.
 *
 * Se asegura de que la solicitud sea de tipo POST y que exista un archivo en la sesión.
 * Luego, obtiene los datos del formulario, los filtra y los envía al controlador para la actualización.
 * Finalmente, maneja las redirecciones y errores.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['filePath'])) {
    $filePath = $_SESSION['filePath'];

    $postData = [
        'author' => $_POST['author'] ?? null,
        'title' => $_POST['title'] ?? null,
        'subject' => $_POST['subject'] ?? null,
        'keywords' => $_POST['keywords'] ?? null
    ];

    // Log de los datos recibidos para actualización
    error_log("Datos recibidos para actualización:\n" . print_r($postData, true));

    $postData = array_filter($postData);

    if (empty($postData)) {
        $_SESSION['error'] = 'No se proporcionaron datos para actualizar.';
        header('Location: /pdfmax2/view/PdfMetaModifierView.php');
        exit();
    }

    $updatedFilePath = $controller->updateAndSaveMetaData($filePath, $postData);

    if (file_exists($updatedFilePath)) {
        $_SESSION['updatedFilePath'] = $updatedFilePath;
        header('Location: /pdfmax2/view/PdfMetaModifierView.php');
        exit();
    } else {
        echo "Error al actualizar metadatos.";
    }
} else {
    header('Location: /pdfmax2/view/PdfMetaModifierView.php');
    exit();
}
