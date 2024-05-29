<?php

require_once __DIR__ . '/../model/PdfWatermarkerModel.php';

class PdfWatermarkController {
    /**
     * Procesa la solicitud para añadir una marca de agua a un PDF.
     *
     * @return void
     */
    public function handleRequest(): void {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $uploadDir = __DIR__ . '/../tmps/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && isset($_FILES['watermark'])) {
            $pdfPath = $uploadDir . basename($_FILES['pdf']['name']);
            $watermarkPath = $uploadDir . basename($_FILES['watermark']['name']);
            $outputPdfPath = $uploadDir . 'archivo_salida.pdf';

            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK && $_FILES['watermark']['error'] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath) && move_uploaded_file($_FILES['watermark']['tmp_name'], $watermarkPath)) {
                    $watermarker = new PdfWatermarkerModel();
                    $output = $watermarker->addWatermark($pdfPath, $watermarkPath, $outputPdfPath);

                    if (file_exists($outputPdfPath)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="' . basename($outputPdfPath) . '"');
                        header('Content-Length: ' . filesize($outputPdfPath));
                        readfile($outputPdfPath);

                        unlink($pdfPath);
                        unlink($watermarkPath);
                        unlink($outputPdfPath);
                        exit;
                    } else {
                        echo "Error al procesar el archivo PDF: $output<br>";
                    }
                } else {
                    echo "Error al mover los archivos subidos.<br>";
                }
            } else {
                echo "Error en la subida de archivos:<br>";
                echo "PDF error: " . $_FILES['pdf']['error'] . "<br>";
                echo "Watermark error: " . $_FILES['watermark']['error'] . "<br>";
            }
        } else {
            echo "No se han enviado archivos correctamente.<br>";
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud
$controller = new PdfWatermarkController();
$controller->handleRequest();
