<?php

require_once(__DIR__ . '/../model/PdfToTextModel.php');

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
        $pages = isset($_POST['pages']) ? explode(',', $_POST['pages']) : null;

        if ($format == 'txt') {
            $output = $this->model->convertToText($file);
            $filename = 'output.txt';
            $contentType = 'text/plain';
        } else if ($format == 'odt') {
            if ($pages && !empty($pages[0])) {
                $output = $this->model->convertPagesToOdt($file, $pages);
                $filename = 'output.zip';
                $contentType = 'application/zip';
            } else {
                $output = $this->model->convertToOdt($file);
                $filename = 'output.odt';
                $contentType = 'application/vnd.oasis.opendocument.text';
            }
        }

        if ($output) {
            if ($format == 'odt' && (!$pages || empty($pages[0]))) {
                // Guardar el archivo ODT en un directorio temporal para descargarlo
                $odtFilePath = '/var/tmp/pdfmax2_temps/' . uniqid('output') . '.odt';
                file_put_contents($odtFilePath, $output);

                header('Content-Description: File Transfer');
                header('Content-Type: ' . $contentType);
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($odtFilePath));
                readfile($odtFilePath);
                unlink($odtFilePath); // Eliminar el archivo ODT después de la descarga
            } else {
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $contentType);
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . strlen($output));
                echo $output;
            }
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