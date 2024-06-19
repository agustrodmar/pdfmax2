<?php

require_once __DIR__ . '/../model/PdfSplitModel.php';
require_once __DIR__ . '/../utils/Zipper.php';

class PdfSplitController
{
    /**
     * Procesa la solicitud para dividir un PDF.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $uploadDir = realpath(__DIR__ . '/../tmps') . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && isset($_POST['ranges'])) {
            $pdfPath = $uploadDir . basename($_FILES['pdf']['name']);
            $ranges = $_POST['ranges'];
            $outputDir = $uploadDir . 'split_' . uniqid() . '/';

            // Verificar que el archivo es un PDF
            $fileType = mime_content_type($_FILES['pdf']['tmp_name']);
            if ($fileType !== 'application/pdf') {
                echo "El archivo cargado no es un PDF válido.<br>";
                error_log("Error: El archivo cargado no es un PDF válido. Tipo de archivo: $fileType");
                return;
            }

            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
                    error_log("Archivo subido correctamente: $pdfPath");

                    try {
                        $splitter = new PdfSplitModel();
                        $outputPaths = $splitter->splitPdfByRanges($pdfPath, $outputDir, $ranges);

                        $zipper = new \Utils\Zipper();
                        $zipFile = $zipper->createPdfZip($outputPaths);

                        // Descargar el archivo ZIP
                        header('Content-Type: application/zip');
                        header('Content-Disposition: attachment; filename="split_pdfs.zip"');
                        header('Content-Length: ' . filesize($zipFile));
                        ob_clean();
                        flush();
                        readfile($zipFile);

                        // Eliminar archivos temporales después de la descarga
                        foreach ($outputPaths as $file) {
                            unlink($file);
                        }
                        unlink($zipFile);
                        rmdir($outputDir);

                        exit;
                    } catch (Exception $e) {
                        echo "Error durante la división del PDF: " . $e->getMessage() . "<br>";
                        error_log("Excepción capturada durante la división del PDF: " . $e->getMessage());
                    }
                } else {
                    echo "Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']) . "<br>";
                    error_log("Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']));
                }
            } else {
                echo "Error al cargar el archivo PDF: " . htmlspecialchars($_FILES['pdf']['name']) . " - Código de error: " . $_FILES['pdf']['error'] . "<br>";
                error_log("Error al cargar el archivo PDF: " . htmlspecialchars($_FILES['pdf']['name']) . " - Código de error: " . $_FILES['pdf']['error']);
            }
        } else {
            echo "No se han enviado el archivo PDF o los rangos de páginas.<br>";
            error_log("Error: No se han enviado el archivo PDF o los rangos de páginas.");
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud
$controller = new PdfSplitController();
$controller->handleRequest();
