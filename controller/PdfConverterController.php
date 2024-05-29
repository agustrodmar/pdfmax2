<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/PdfConverterModel.php';
require_once __DIR__ . '/../utils/Zipper.php';
require_once __DIR__ . '/../utils/ProgressTracker.php';

use Utils\Zipper;

/**
 * Controlador para manejar la conversión de archivos PDF a diferentes formatos de imagen.
 */
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
        error_log("PdfConverterController inicializado.");
    }

    /**
     * Procesa la solicitud de conversión de PDF, valida la entrada, y prepara una respuesta de descarga.
     * @throws Exception Si la solicitud no es válida o falla la carga del archivo.
     */
    public function convert(): void
    {
        error_log("Iniciando conversión de PDF.");
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'], $_POST['format'], $_POST['pages'])) {
            error_log("Método POST y parámetros recibidos.");
            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                error_log("Archivo cargado correctamente.");
                $this->tracker->reset();
                $inputFile = $_FILES['pdf']['tmp_name'];
                $outputBase = __DIR__ . '/../tmps/' . uniqid('pdf_convert_');
                $format = $_POST['format'];
                $pages = $this->parsePageInput($_POST['pages']);

                $this->tracker->setTotalSteps(count($pages));

                foreach ($pages as $page) {
                    $outputFile = $outputBase . "_page_$page";
                    $this->model->convertPdf($inputFile, $outputFile, $format, $page);
                    $this->tracker->incrementStep();
                    error_log("Página $page convertida.");
                }

                $zipFile = $this->zipper->createZip($outputBase, $format);
                error_log("Archivo ZIP creado: $zipFile");

                $_SESSION['zipFile'] = $zipFile;

                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => "Error al cargar el archivo: " . $_FILES['pdf']['error']]);
                error_log("Error al cargar el archivo: " . $_FILES['pdf']['error']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => "Método no soportado o datos faltantes."]);
            error_log("Método no soportado o datos faltantes.");
        }
    }

    public function download(): void
    {
        error_log("Iniciando descarga de archivo.");
        $zipFile = $_SESSION['zipFile'];

        if (file_exists($zipFile)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="converted_files.zip"');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);

            sleep(10);

            unlink($zipFile);
            error_log("Archivo ZIP eliminado: $zipFile");

            $pngFiles = glob(__DIR__ . '/../tmps/*.png');
            $jpegFiles = glob(__DIR__ . '/../tmps/*.jpeg');
            $svgFiles = glob(__DIR__ . '/../tmps/*.svg');

            foreach (array_merge($pngFiles, $jpegFiles, $svgFiles) as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            error_log("Archivos temporales eliminados.");
        } else {
            echo "Archivo no encontrado.";
            error_log("Archivo no encontrado: $zipFile");
        }
    }

    /**
     * Analiza un string de entrada para extraer rangos de páginas individuales.
     * @param string $input String con rangos de páginas.
     * @return array Array de páginas individuales.
     */
    private function parsePageInput(string $input): array
    {
        $pages = [];
        $parts = explode(',', $input);
        foreach ($parts as $part) {
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

    /**
     * Este método elimina los archivos temporales generados después de la creación del Zip.
     *
     * @param $pattern; el patrón de búsqueda de los nombres de los archivos que elimina.
     * @return void
     */
    private function deleteFiles($pattern): void
    {
        foreach (glob($pattern) as $file) {
            unlink($file);
        }
    }
}

// Crear una instancia de la clase y llamar al método convert
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
