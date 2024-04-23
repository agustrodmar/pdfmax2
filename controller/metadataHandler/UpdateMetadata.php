<?php
require_once __DIR__ . '/../PdfMetaModifierController.php';


$controller = new PdfMetaModifierController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filePath'])) {
    $filePath = $_SESSION['filePath']; // Asumiendo que esto se guarda después de la carga
    $postData = [
        'author' => $_POST['author'] ?? '',
        'title' => $_POST['title'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'keywords' => $_POST['keywords'] ?? ''
    ];

    // Actualizar metadatos
    $updatedFilePath = $controller->updateAndSaveMetaData($filePath, $postData);
    if (file_exists($updatedFilePath)) {
        $_SESSION['updatedFilePath'] = $updatedFilePath; // Guardar la nueva ruta del archivo
        header('Location: /view/PdfMetaModifierView.php');
        // Después de servir el archivo al cliente, borra el archivo PDF modificado
        unlink($updatedFilePath);

        // Además, si creaste una copia del archivo original en la carpeta uploads, también deberías borrarlo:
        unlink($filePath);
    } else {
        echo "Error al actualizar metadatos.";
    }
}
