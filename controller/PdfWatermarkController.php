<?php

use Model\PdfWatermarkerModel;
use Utils\Clean\TempCleaner;

require_once __DIR__ . '/../model/PdfWatermarkerModel.php';
require_once __DIR__ . '/../utils/clean/TempCleaner.php';

class PdfWatermarkController {
    /**
     * Procesa la solicitud para añadir una marca de agua a un PDF.
     *
     * @return void
     * @throws Exception
     */
    public function handleRequest(): void {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $uploadDir = '/var/tmp/pdfmax2_temps/' . uniqid('pdf_watermark_', true);
        if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            error_log(sprintf('El directorio "%s" no pudo ser creado', $uploadDir));
            throw new Exception(sprintf('El directorio "%s" no pudo ser creado', $uploadDir));
        }

        $cleaner = new TempCleaner($uploadDir);

        try {
            error_log("Procesando solicitud de marca de agua...");

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no soportado. Use POST.");
            }

            if (!isset($_FILES['pdf']) || !isset($_FILES['watermark'])) {
                throw new Exception("No se han enviado archivos correctamente.");
            }

            $pdfPath = $uploadDir . '/' . basename($_FILES['pdf']['name']);
            $watermarkPath = $uploadDir . '/' . basename($_FILES['watermark']['name']);
            $outputPdfPath = $uploadDir . '/archivo_salida.pdf';

            // Verificar que los archivos son del tipo esperado
            $pdfFileType = mime_content_type($_FILES['pdf']['tmp_name']);
            $watermarkFileType = mime_content_type($_FILES['watermark']['tmp_name']);
            if ($pdfFileType !== 'application/pdf') {
                throw new Exception("El archivo cargado no es un PDF válido. Tipo: $pdfFileType");
            }
            if ($watermarkFileType !== 'application/pdf') {
                throw new Exception("El archivo de marca de agua no es un PDF válido. Tipo: $watermarkFileType");
            }

            if ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK || $_FILES['watermark']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error en la subida de archivos. PDF error: " . $_FILES['pdf']['error'] . " Watermark error: " . $_FILES['watermark']['error']);
            }

            if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath) || !move_uploaded_file($_FILES['watermark']['tmp_name'], $watermarkPath)) {
                throw new Exception("Error al mover los archivos subidos.");
            }

            error_log("Archivos subidos correctamente: $pdfPath, $watermarkPath");

            $watermarker = new PdfWatermarkerModel();
            $output = $watermarker->addWatermark($pdfPath, $watermarkPath, $outputPdfPath);

            if (!file_exists($outputPdfPath)) {
                throw new Exception("Error al procesar el archivo PDF: $output");
            }

            error_log("Archivo PDF generado correctamente: $outputPdfPath");

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($outputPdfPath) . '"');
            header('Content-Length: ' . filesize($outputPdfPath));
            ob_clean();
            flush();
            readfile($outputPdfPath);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
            error_log("Error: " . $e->getMessage());
        } finally {
            // Limpiar archivos temporales
            $cleaner->clean();
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud
$controller = new PdfWatermarkController();
try {
    $controller->handleRequest();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    error_log("Error en el controlador principal: " . $e->getMessage());
}