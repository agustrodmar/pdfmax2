<?php

session_start();
require_once __DIR__ . '/PdfMetaModifierController.php';

$controller = new PdfMetaModifierController();

/**
 * Maneja la carga de un archivo PDF.
 *
 * Se asegura de que la solicitud sea de tipo POST y que el archivo exista.
 * Luego, procesa la carga del archivo, obtiene sus metadatos y maneja redirecciones y errores.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    try {
        $filePath = $controller->handleFileUpload($_FILES['pdfFile']);
        if ($filePath) {
            try {
                $metaData = $controller->getAndShowMetaData($filePath);
                $_SESSION['metadata'] = $metaData;
                $_SESSION['filePath'] = $filePath;
                header('Location: /pdfmax2/view/PdfMetaModifierView.php');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /pdfmax2/view/PdfMetaModifierView.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Error al cargar el archivo.";
            header('Location: /pdfmax2/view/PdfMetaModifierView.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /pdfmax2/view/PdfMetaModifierView.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Solicitud inválida o archivo no encontrado.";
    header('Location: /pdfmax2/view/PdfMetaModifierView.php');
    exit();
}