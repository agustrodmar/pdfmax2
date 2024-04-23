<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../model/OdtToPdfModel.php');

class OdtToPdfController {
    private OdtToPdfModel $model;

    public function __construct() {
        $this->model = new OdtToPdfModel();
    }

    public function convert(): void {
        $file = $_FILES['file']['tmp_name'];
        error_log("Temporary file path: " . $file);

        $outputFile = $this->model->convertToPdf($file);

        if ($outputFile) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($outputFile));
            readfile($outputFile);
            unlink($outputFile);  // Limpia el archivo PDF temporal
            exit;
        } else {
            echo "Error al convertir el archivo ODT a PDF.";
        }
    }
}

$controller = new OdtToPdfController();
$controller->convert();