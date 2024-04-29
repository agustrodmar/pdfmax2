<?php

require_once(__DIR__ . '/../model/TextToPdfModel.php');
require_once __DIR__ . '/../utils/PdfResponseSender.php';

class TextToPdfController {
    private TextToPdfModel $model;
    use PdfResponseSender;

    public function __construct() {
        $this->model = new TextToPdfModel();
    }

    public function convert(): void {
        try {
            $file = $_FILES['file']['tmp_name'];
            $pages = $_POST['pages'] ?? '';

            if (!$file || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error en la carga del archivo o archivo no recibido.");
            }

            // Soporta varios formatos de archivos
            $allowedTypes = [
                'application/vnd.oasis.opendocument.text', // ODT
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
                'text/plain' // TXT
            ];

            if (!in_array($_FILES['file']['type'], $allowedTypes)) {
                throw new Exception("Tipo de archivo no soportado. Los archivos deben ser ODT, DOCX o TXT.");
            }

            $outputFile = $this->model->convertToPdf($file, $pages);
            $this->sendPdfToClient($outputFile);
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
    }
}

$controller = new TextToPdfController();
$controller->convert();
