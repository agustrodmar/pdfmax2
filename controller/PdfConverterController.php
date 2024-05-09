<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/PdfConverterModel.php';
require_once __DIR__ . '/../utils/Zipper.php';
require_once __DIR__ . '/../utils/ProgressTracker.php';

use Utils\Zipper;

class PdfConverterController
{
    private PdfConverterModel $model;
    private Zipper $zipper;
    private ProgressTracker $tracker;

    public function __construct()
    {
        $this->model = new PdfConverterModel();
        $this->zipper = new Zipper();
        $this->tracker = new ProgressTracker();
    }

    public function convert(): void
    {
        $uniqueId = $_POST['uniqueId'] ?? uniqid('pdf_convert_', true);  // Genera uniqueId si no se provee
        $_SESSION[$uniqueId . '_zipFile'] = __DIR__ . "/../tmps/{$uniqueId}.zip";
        error_log("Inicio de conversión: uniqueId = $uniqueId");

        if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $inputFile = $_FILES['pdf']['tmp_name'];
            $outputBase = __DIR__ . '/../tmps/' . $uniqueId;
            $format = $_POST['format'];
            $pages = $this->parsePageInput($_POST['pages']);

            $this->tracker->setTotalSteps(count($pages), $uniqueId);
            error_log("Total de pasos configurados: " . count($pages));

            foreach ($pages as $page) {
                $outputFile = "{$outputBase}_page_{$page}";
                $this->model->convertPdf($inputFile, $outputFile, $format, $page);
                $this->tracker->incrementStep($uniqueId);
                error_log("Página $page convertida.");
            }

            $zipFile = $this->zipper->createZip($outputBase, $format);
            $_SESSION[$uniqueId . '_zipFile'] = $zipFile;
            error_log("Archivo ZIP creado: $zipFile");

            echo json_encode(['success' => true]);
        } else {
            error_log("Error al cargar el archivo: " . $_FILES['pdf']['error']);
            echo "Error al cargar el archivo: " . $_FILES['pdf']['error'];
        }
    }


    public function download(): void
    {
        $uniqueId = $_GET['uniqueId'] ?? '';
        $zipFile = $_SESSION[$uniqueId . '_zipFile'] ?? null;
        error_log("Intentando descargar el archivo: $zipFile");

        if ($zipFile && file_exists($zipFile)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);
            unlink($zipFile);
            $this->deleteFiles(__DIR__ . '/../tmps/' . $uniqueId . '*');
        } else {
            error_log("Archivo no encontrado para uniqueId: $uniqueId, ruta esperada: $zipFile");
            echo "Archivo no encontrado.";
        }
    }

    private function parsePageInput(string $input): array
    {
        $pages = [];
        foreach (explode(',', $input) as $part) {
            if (str_contains($part, '-')) {
                list($start, $end) = explode('-', $part);
                for ($i = $start; $i <= $end; $i++) {
                    $pages[] = $i;
                }
            } else {
                $pages[] = $part;
            }
        }
        return $pages;
    }

    private function deleteFiles($pattern): void
    {
        foreach (glob($pattern) as $file) {
            unlink($file);
        }
    }
}

$controller = new PdfConverterController();
try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $controller->convert();
    } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $controller->download();
    }
} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
}
