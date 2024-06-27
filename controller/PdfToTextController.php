<?php

require_once(__DIR__ . '/../model/PdfToTextModel.php');
require_once __DIR__ . '/../utils/clean/TempCleaner.php';

/**
 * Clase que se encarga de convertir un archivo PDF a texto o a formato ODT.
 */
class PdfToTextController {
    private PdfToTextModel $model;

    public function __construct() {
        $this->model = new PdfToTextModel();
    }

    /**
     * Esta función se encarga de convertir el archivo PDF a texto o a formato ODT.
     * @throws Exception
     */
    public function convert(): void
    {
        $file = $_FILES['file']['tmp_name'];
        $format = $_POST['format'];

        if ($format == 'txt') {
            $output = $this->model->convertToText($file);
            $filename = 'output.txt';
            $contentType = 'text/plain';
        } else if ($format == 'odt') {
            $output = $this->model->convertToOdt($file);
            $filename = 'output.odt';
            $contentType = 'application/vnd.oasis.opendocument.text';
        }

        if ($output) {
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($output));
            echo $output;
            exit;
        } else {
            echo "Error al convertir el archivo.";
        }
        // Llama a TempCleaner para limpiar los archivos temporales después de cada operación
        $cleaner = new \utils\clean\TempCleaner('/var/tmp/pdfmax2_temps/');
        $cleaner->clean();
    }
}

$controller = new PdfToTextController();
try {
    $controller->convert();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
}
