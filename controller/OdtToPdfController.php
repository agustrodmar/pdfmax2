<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../model/OdtToPdfModel.php');

/**
 * Controlador que maneja la solicitud de conversión de documentos.
 */
class OdtToPdfController {
    private $model;

    /**
     * Constructor que inicializa el modelo de conversión.
     */
    public function __construct() {
        $this->model = new OdtToPdfModel();
    }

    /**
     * Procesa la solicitud de conversión de un archivo ODT a PDF.
     * Envía el archivo PDF resultante al cliente o muestra un mensaje de error.
     */
    public function convert(): void {
        $file = $_FILES['file']['tmp_name'];

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
            unlink($outputFile);  // Elimina el archivo PDF temporal
            exit;
        } else {
            echo "Error al convertir el archivo ODT a PDF.";
        }
    }
}

$controller = new OdtToPdfController();
$controller->convert();