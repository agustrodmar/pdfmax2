<?php

use JetBrains\PhpStorm\NoReturn;

require_once '../model/pdfOptimizerModel.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('',1);
class pdfOptimizerController {
    public function __construct() {}

    public function handleRequest(): void
    {
        if (isset($_POST['submit'])) {
            if (!empty($_FILES['pdfFile']['tmp_name'])) {
                $inputFile = $_FILES['pdfFile']['tmp_name'];
                $outputFile = "optimized.pdf"; // Nombre del archivo optimizado

                $qualityMap = [
                    'baja' => 'screen',
                    'media' => 'ebook',
                    'alta' => 'printer'
                ];


                if (isset($_POST['quality']) && isset($qualityMap[$_POST['quality']])) {
                    $quality = $qualityMap[$_POST['quality']];

                    $success = $this->optimizePDF($inputFile, $outputFile, $quality);

                    if ($success) {
                        $this->downloadFile($outputFile);
                    } else {
                        echo "Error al optimizar el PDF.";
                    }
                } else {
                    echo "Por favor, seleccione una calidad.";
                }
            } else {
                echo "Por favor, seleccione un archivo PDF.";
            }
        }
    }

    private function optimizePDF($inputFile, $outputFile, $quality): bool {
        return pdfOptimizerModel::optimizePDF($inputFile, $outputFile, $quality);
    }


    #[NoReturn] private function downloadFile($filePath): void
    {
        header('Content-Disposition: attachment; filename="' . $filePath . '"');
        readfile($filePath);
        exit;
    }
}

$controller = new PDFOptimizerController();
$controller->handleRequest();

