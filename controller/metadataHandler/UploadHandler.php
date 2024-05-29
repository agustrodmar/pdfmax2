<?php

require_once __DIR__ . '/../PdfMetaModifierController.php';

$controller = new PdfMetaModifierController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    // Manejo de la carga del archivo
    $filePath = $controller->handleFileUpload($_FILES['pdfFile']);
    if ($filePath) {
        // Extracción de metadatos
        $metaData = $controller->getAndShowMetaData($filePath);
        $_SESSION['metadata'] = $metaData;
        $_SESSION['filePath'] = $filePath;
        header('Location: /view/PdfMetaModifierView.php');
    } else {
        echo "Error al cargar el archivo.";
    }
}
