<?php

require_once __DIR__ . '/../utils/clean/TempCleaner.php';
require_once __DIR__ . '/../model/PdfPresenterModel.php';

class PdfPresenterController {
    /**
     * Maneja la solicitud para ver y eliminar un archivo PDF.
     *
     * @return void
     * @throws Exception
     */
    public function handleRequest(): void {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        error_log("Iniciando PdfPresenterController");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
            $this->handleFileUpload();
        } elseif (isset($_GET['delete']) && $_GET['delete'] === 'true' && isset($_GET['file'])) {
            $this->deleteFile($_GET['file']);
        } else {
            echo "No se ha especificado un archivo PDF.";
            error_log("No se ha especificado un archivo PDF.");
        }
    }

    /**
     * Maneja la subida del archivo PDF.
     *
     * @return void
     * @throws Exception
     */
    private function handleFileUpload(): void {
        $uploadDir = '/var/tmp/pdfmax2_temps/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file = $_FILES['pdf'];
        $fileName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', basename($file['name']));
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $model = new PdfPresenterModel();
            if ($model->presentPdf($filePath)) {
                // Devolver la URL segura del archivo
                echo json_encode(['fileUrl' => "/pdfmax2_temps/$fileName"]);
            } else {
                echo "Archivo no válido.";
                error_log("Archivo no válido: $filePath");
            }
        } else {
            echo "Error al subir el archivo.";
            error_log("Error al subir el archivo: " . $file['name']);
        }
    }

    /**
     * Elimina el archivo PDF especificado.
     *
     * @param string $file El archivo PDF a eliminar.
     * @return void
     */
    private function deleteFile(string $file): void {
        error_log("Eliminando archivo PDF: $file");

        $filePath = realpath(__DIR__ . '/../' . $file);
        if ($filePath && mime_content_type($filePath) === 'application/pdf') {
            unlink($filePath);
            echo "Archivo eliminado: $file";
            error_log("Archivo eliminado: $file");
        } else {
            echo "Error al eliminar el archivo.";
            error_log("Error al eliminar el archivo: $file");
        }
    }
}

$controller = new PdfPresenterController();
try {
    $controller->handleRequest();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    error_log("Error en el controlador principal: " . $e->getMessage());
}
