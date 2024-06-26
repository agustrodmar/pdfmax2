<?php

use JetBrains\PhpStorm\NoReturn;

require_once '../model/PdfOptimizerModel.php';

/**
 * Controlador para manejar la optimización y descarga de archivos PDF.
 */
class PdfOptimizerController {
    /**
     * Constructor de la clase.
     */
    public function __construct() {}

    /**
     * Maneja la solicitud HTTP para optimizar y descargar un archivo PDF.
     */
    public function handleRequest(): void
    {
        if (isset($_POST['submit'])) {
            if (!empty($_FILES['pdfFile']['tmp_name'])) {
                $inputFile = $_FILES['pdfFile']['tmp_name'];
                $outputFile = __DIR__ . '/../tmps/' . uniqid('optimized_pdf');

                try {
                    $success = $this->optimizePdf($inputFile, $outputFile);

                    if ($success) {
                        $this->downloadFile($outputFile);
                    } else {
                        echo "Error al optimizar el PDF.";
                    }
                } catch (Exception $e) {
                    echo "Ha ocurrido un error durante la optimización: " . $e->getMessage();
                } finally {
                    if (file_exists($outputFile)) {
                        unlink($outputFile);
                    }
                }
            } else {
                echo "Por favor, seleccione un archivo PDF.";
            }
        }
    }

    /**
     * Llama al modelo para optimizar un PDF.
     *
     * @param string $inputFile Ruta al archivo de entrada.
     * @param string $outputFile Ruta al archivo de salida.
     * @return bool Retorna true si la optimización fue exitosa, de lo contrario false.
     * @throws Exception Lanza excepción si hay un error durante la optimización.
     */
    private function optimizePdf(string $inputFile, string $outputFile): bool {
        return PdfOptimizerModel::optimizePdf($inputFile, $outputFile);
    }

    /**
     * Envía el archivo PDF optimizado al cliente y termina la ejecución del script.
     *
     * @param string $filePath Ruta del archivo a enviar.
     */
    #[NoReturn] private function downloadFile(string $filePath): void
    {
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Type: application/pdf');
        readfile($filePath);
        unlink($filePath);
        exit;
    }
}

$controller = new PdfOptimizerController();
$controller->handleRequest();

