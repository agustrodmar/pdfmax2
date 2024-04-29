<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../PdfMetaModifierController.php';

$controller = new PdfMetaModifierController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['filePath'])) {
    $filePath = $_SESSION['filePath'];  // Asumiendo que esto se guarda después de la carga

    // Recoger los datos del formulario, si están presentes
    $postData = [
        'author' => $_POST['author'] ?? null,
        'title' => $_POST['title'] ?? null,
        'subject' => $_POST['subject'] ?? null,
        'keywords' => $_POST['keywords'] ?? null
    ];

    // Filtrar $postData para eliminar campos nulos
    $postData = array_filter($postData);

    // Si no hay datos para actualizar, redirigir de vuelta con un mensaje
    if (empty($postData)) {
        $_SESSION['error'] = 'No se proporcionaron datos para actualizar.';
        header('Location: /view/PdfMetaModifierView.php');
        exit;
    }

    // Actualizar metadatos
    $updatedFilePath = $controller->updateAndSaveMetaData($filePath, $postData);

// Si se ha creado un nuevo archivo, guardar la ruta en la sesión y redirigir al usuario
    if (file_exists($updatedFilePath)) {
        $_SESSION['updatedFilePath'] = $updatedFilePath;
        header('Location: /view/PdfMetaModifierView.php'); // Asegúrate de que esta URL es correcta
        exit;
    } else {
        echo "Error al actualizar metadatos.";
    }


    // Si se ha creado un nuevo archivo, redirigir al usuario y eliminar archivos temporales
    // Actualizar metadatos
    $updatedFilePath = $controller->updateAndSaveMetaData($filePath, $postData);

// Si se ha creado un nuevo archivo, redirigir al usuario y NO eliminar archivos temporales aquí
    if (file_exists($updatedFilePath)) {
        // Log para depuración
        error_log("Archivo actualizado con éxito: " . $updatedFilePath);

        // Guardar la ruta del archivo actualizado en la sesión para su descarga
        $_SESSION['updatedFilePath'] = $updatedFilePath;

        // Redirigir al usuario a la vista donde puede descargar el archivo
        header('Location: /view/PdfMetaModifierView.php');
        exit;
    } else {
        // Log de error
        error_log("Error al actualizar metadatos para el archivo: " . $filePath);

        // Mostrar mensaje de error
        echo "Error al actualizar metadatos.";
    }
} else {
    // Redirigir al usuario si no hay un archivo cargado o si el método no es POST
    header('Location: /view/PdfMetaModifierView.php');
    exit;
}
