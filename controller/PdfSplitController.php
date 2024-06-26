<?php

use utils\clean\TempCleaner;
use Utils\Zipper;

require_once __DIR__ . '/../model/PdfSplitModel.php';
require_once __DIR__ . '/../utils/Zipper.php';
require_once __DIR__ . '/../utils/TempCleaner.php';

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

        $baseUploadDir = '/var/tmp/pdfmax2_temps' . DIRECTORY_SEPARATOR;
        if (!is_dir($baseUploadDir)) {
            mkdir($baseUploadDir, 0777, true);
        }

        $uniqueDir = $baseUploadDir . uniqid('pdf_split_', true) . DIRECTORY_SEPARATOR;
        mkdir($uniqueDir, 0777, true);

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no soportado. Use POST.");
            }

            if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("No se ha seleccionado ningún archivo PDF o hubo un error al cargar el archivo.");
            }

            $pdfPath = $uniqueDir . basename($_FILES['pdf']['name']);
            $fileType = mime_content_type($_FILES['pdf']['tmp_name']);
            if ($fileType !== 'application/pdf') {
                throw new Exception("El archivo cargado no es un PDF válido.");
            }

            if (!isset($_POST['ranges']) || empty($_POST['ranges'])) {
                throw new Exception("No se ha definido ningún rango.");
            }

            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
                error_log("Archivo subido correctamente: $pdfPath");

                $ranges = $_POST['ranges'];
                $outputDir = $uniqueDir . 'split_' . uniqid() . '/';
                mkdir($outputDir, 0777, true);

                $splitter = new PdfSplitModel();
                $outputPaths = $splitter->splitPdfByRanges($pdfPath, $outputDir, $ranges);

                $zipper = new Zipper();
                $zipFile = $zipper->createPdfZip($outputPaths, $uniqueDir);

                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="split_pdfs.zip"');
                header('Content-Length: ' . filesize($zipFile));
                ob_clean();
                flush();
                readfile($zipFile);

                // Limpiar el directorio temporal único
                $cleaner = new TempCleaner($uniqueDir);
                $cleaner->clean();
                rmdir($uniqueDir);

                exit;
            } else {
                throw new Exception("Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']));
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
            error_log("Excepción capturada: " . $e->getMessage());
        }
    }
}

$controller = new PdfSplitController();
$controller->handleRequest();
