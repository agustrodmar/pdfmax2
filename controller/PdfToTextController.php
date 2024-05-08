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
            if ($pages) {
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
    }
}

$controller = new PdfToTextController();
try {
    $controller->convert();
} catch (Exception $e) {
}

