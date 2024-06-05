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

        $uploadDir = realpath(__DIR__ . '/../tmps') . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && isset($_FILES['watermark'])) {
            $pdfPath = $uploadDir . basename($_FILES['pdf']['name']);
            $watermarkPath = $uploadDir . basename($_FILES['watermark']['name']);
            $outputPdfPath = $uploadDir . 'archivo_salida.pdf';

            // Verificar que los archivos son del tipo esperado
            $pdfFileType = mime_content_type($_FILES['pdf']['tmp_name']);
            $watermarkFileType = mime_content_type($_FILES['watermark']['tmp_name']);
            if ($pdfFileType !== 'application/pdf') {
                echo "El archivo cargado no es un PDF válido.<br>";
                return;
            }
            if (!str_starts_with($watermarkFileType, 'image/')) {
                echo "El archivo de marca de agua no es una imagen válida.<br>";
                return;
            }

            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK && $_FILES['watermark']['error'] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath) && move_uploaded_file($_FILES['watermark']['tmp_name'], $watermarkPath)) {
                    try {
                        $watermarker = new PdfWatermarkerModel();
                        $output = $watermarker->addWatermark($pdfPath, $watermarkPath, $outputPdfPath);

                        if (file_exists($outputPdfPath)) {
                            header('Content-Type: application/pdf');
                            header('Content-Disposition: attachment; filename="' . basename($outputPdfPath) . '"');
                            header('Content-Length: ' . filesize($outputPdfPath));
                            readfile($outputPdfPath);

                            // Eliminar archivos temporales después de la descarga
                            unlink($pdfPath);
                            unlink($watermarkPath);
                            unlink($outputPdfPath);
                            exit;
                        } else {
                            echo "Error al procesar el archivo PDF: $output<br>";
                        }
                    } catch (Exception $e) {
                        echo "Error durante el procesamiento del PDF: " . $e->getMessage() . "<br>";
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
