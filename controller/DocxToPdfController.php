<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../utils/PdfResponseSender.php';
require_once '../model/DocxToPdfModel.php';

/**
 * Controlador para la conversión de archivos DOCX a PDF.
 */
class DocxToPdfController {
    use PdfResponseSender;

    /**
     * Convierte un archivo DOCX a PDF y lo envía al cliente.
     *
     * @throws Exception Si hay un error durante la conversión.
     */
    public function convert(): void {

        try {
            if ($_FILES['docxFile']['error'] !== UPLOAD_ERR_OK || $_FILES['docxFile']['type'] !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                throw new Exception("Error: archivo no válido o no proporcionado.");
            }
            $model = new DocxToPdfModel();
            $outputPath = $model->convertDocxToPdf($_FILES['docxFile']['tmp_name']);
            if (empty($outputPath)) {
                throw new Exception("Error al convertir el archivo.");
            }
            $this->sendPdfToClient($outputPath);
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
    }
}

$controller = new DocxToPdfController();
try {
    $controller->convert();
} catch (Exception $e) {
}