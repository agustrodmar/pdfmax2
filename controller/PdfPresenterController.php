<?php

require_once __DIR__ . '/../model/PdfPresenterModel.php';

/**
 * Clase PdfPresenterController
 * Este controlador maneja la subida y eliminación de archivos PDF.
 */
class PdfPresenterController
{
    /**
     * Procesa la solicitud para subir y visualizar un archivo PDF.
     *
     * @return void
     */
    public function procesarSolicitud(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
                $uploadDir = __DIR__ . '/../tmps/'; // Directorio de carga

                // Crear el directorio de carga si no existe
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Validar el archivo PDF cargado
                if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($fileInfo, $_FILES['pdf']['tmp_name']);
                    finfo_close($fileInfo);

                    if ($mimeType === 'application/pdf') {
                        $uniqueId = uniqid('PDF_', true); // Generar un ID único
                        $pdfFile = $uploadDir . $uniqueId . '_' . basename($_FILES['pdf']['name']); // Establecer la ruta de destino con ID único

                        // Mover el archivo cargado al directorio de destino
                        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfFile)) {
                            $relativePath = str_replace(__DIR__ . '/../', '', $pdfFile);
                            header('Location: ../view/PdfView.php?file=' . urlencode($relativePath));
                            exit;
                        } else {
                            echo "Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']) . "<br>";
                        }
                    } else {
                        echo "El archivo subido no es un PDF válido.<br>";
                    }
                } else {
                    echo "Error al cargar el archivo PDF: " . htmlspecialchars($_FILES['pdf']['name']) . " - Código de error: " . $_FILES['pdf']['error'] . "<br>";
                }
            } else {
                echo "No se ha enviado el archivo PDF.<br>";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }
    }

    /**
     * Elimina un archivo PDF especificado.
     *
     * @return void
     */
    public function eliminarArchivo(): void
    {
        if (isset($_GET['file'])) {
            $file = __DIR__ . '/../' . $_GET['file'];

            // Validar que el archivo existe y es un PDF
            if (file_exists($file) && mime_content_type($file) === 'application/pdf') {
                unlink($file);
                echo "Archivo eliminado.";
            } else {
                echo "Archivo no válido o no encontrado.";
            }
        } else {
            echo "No se ha especificado un archivo para eliminar.";
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud o eliminar el archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PdfPresenterController();
    $controller->procesarSolicitud();
} elseif (isset($_GET['delete']) && $_GET['delete'] === 'true') {
    $controller = new PdfPresenterController();
    $controller->eliminarArchivo();
}
