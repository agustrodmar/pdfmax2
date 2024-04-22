<?php

use JetBrains\PhpStorm\NoReturn;

require_once '../model/PdfOptimizerModel.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('',1);

class PdfOptimizerController {
    public function __construct() {}

    public function handleRequest(): void
    {
        if (isset($_POST['submit'])) {
            if (!empty($_FILES['pdfFile']['tmp_name'])) {
                $inputFile = $_FILES['pdfFile']['tmp_name'];
                $outputFile = "optimized.pdf"; // Nombre del archivo optimizado

                $success = $this->optimizePdf($inputFile, $outputFile);

                if ($success) {
                    $this->downloadFile($outputFile);
                } else {
                    echo "Error al optimizar el PDF.";
                }
            } else {
                echo "Por favor, seleccione un archivo PDF.";
            }
        }
    }

    private function optimizePdf($inputFile, $outputFile): bool {
        return PdfOptimizerModel::optimizePdf($inputFile, $outputFile);
    }

    #[NoReturn] private function downloadFile($filePath): void
    {
        header('Content-Disposition: attachment; filename="' . $filePath . '"');
        readfile($filePath);
        exit;
    }
}

$controller = new PdfOptimizerController();
$controller->handleRequest();
