<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '/var/www/html/pdfmax2/model/PdfConverterModel.php';
require_once '/var/www/html/pdfmax2/utils/Zipper.php';
require_once '/var/www/html/pdfmax2/utils/ProgressTracker.php';
require '/var/www/html/pdfmax2/vendor/autoload.php';

use Utils\Zipper;
use Utils\ProgressTracker;
use Predis\Client as PredisClient;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class PdfConverterController {
    private PdfConverterModel $model;
    private Zipper $zipper;
    private ProgressTracker $tracker;

    public function __construct() {
        $this->model = new PdfConverterModel();
        $this->zipper = new Zipper();
        $this->tracker = new ProgressTracker();
    }

    public function convert(): void {
        error_log("Entrando en la función convert...");
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'], $_POST['format'], $_POST['pages'])) {
            error_log("Método POST y parámetros válidos...");
            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $uniqueId = $_POST['uniqueId'] ?? uniqid('pdf_convert_', true);
                error_log("Unique ID generado: $uniqueId");

                // Guardar el archivo subido en una ubicación permanente
                $uploadDir = '/var/www/html/pdfmax2/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $uploadedFilePath = $uploadDir . basename($_FILES['pdf']['name']);
                move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadedFilePath);

                $inputFile = $uploadedFilePath;
                $outputBase = '/var/www/html/pdfmax2/tmps/' . $uniqueId;
                $format = $_POST['format'];
                $pages = $this->parsePageInput($_POST['pages']);

                $this->tracker->setTotalSteps(count($pages), $uniqueId);

                // Configuración de Redis
                $redis = new PredisClient();

                foreach ($pages as $page) {
                    $jobData = [
                        'inputFile' => $inputFile,
                        'outputBase' => $outputBase,
                        'format' => $format,
                        'page' => $page,
                        'uniqueId' => $uniqueId
                    ];
                    error_log("Solicitud encolada: " . json_encode($jobData));
                    $redis->lpush('pdf_conversion_queue', [json_encode($jobData)]);
                }

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'uniqueId' => $uniqueId, 'message' => 'Proceso de conversión encolado.']);
                exit;
            } else {
                echo "Error al cargar el archivo: " . $_FILES['pdf']['error'];
            }
        } else {
            echo "Método no soportado o datos faltantes.";
        }
    }

    public function download($uniqueId): void {
        error_log("Unique ID recibido para descarga: $uniqueId");

        $zipFile = $_SESSION[$uniqueId . '_zipFile'] ?? null;
        error_log("Intentando descargar el archivo ZIP: $zipFile");

        if ($zipFile && file_exists($zipFile)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);

            register_shutdown_function(function() use ($zipFile, $uniqueId) {
                $this->deleteFiles('/var/www/html/pdfmax2/tmps/' . basename($zipFile, '.zip') . '*');
                error_log("Archivos temporales eliminados para uniqueId: $uniqueId");
            });
        } else {
            error_log("Archivo ZIP no encontrado para uniqueId: $uniqueId, ruta esperada: $zipFile");
            echo "Archivo no encontrado.";
        }
    }

    private function deleteFiles($pattern): void {
        foreach (glob($pattern) as $file) {
            unlink($file);
            error_log("Archivo eliminado: $file");
        }
    }

    private function parsePageInput(string $input): array {
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
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new PdfConverterController();
    $controller->convert();
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $controller = new PdfConverterController();
    $uniqueId = $_GET['uniqueId'] ?? '';
    $controller->download($uniqueId);
}
