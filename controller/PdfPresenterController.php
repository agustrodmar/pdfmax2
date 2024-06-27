<?php

require_once(__DIR__ . '/../model/PdfPresenterModel.php');
require_once __DIR__ . '/../utils/clean/TempCleaner.php';

/**
 * Clase que se encarga de presentar un archivo PDF en el navegador.
 */
class PdfPresenterController {
    private PdfPresenterModel $model;

    public function __construct() {
        $this->model = new PdfPresenterModel();
    }

    /**
     * Esta función se encarga de manejar la carga del archivo PDF y su presentación en el navegador.
     * @throws Exception
     */
    public function present(): void
    {
        $file = $_FILES['file']['tmp_name'];

        if (!$file) {
            throw new Exception("No se ha subido ningún archivo.");
        }

        $pdfPath = $this->model->savePdfTemporarily($file);

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($pdfPath) . '"');
        readfile($pdfPath);

        // Llama a TempCleaner para limpiar los archivos temporales después de la presentación
        $cleaner = new \utils\clean\TempCleaner(dirname($pdfPath));
        $cleaner->clean();
    }
}

$controller = new PdfPresenterController();
try {
    $controller->present();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "Ha ocurrido un error: " . $e->getMessage();
}
