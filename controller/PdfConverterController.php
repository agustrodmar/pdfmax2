<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/PdfConverterModel.php';
require_once __DIR__ . '/../utils/Zipper.php';
require_once __DIR__ . '/../utils/ProgressTracker.php';
require_once __DIR__ . '/../view/PdfConverterView.php';

use Utils\Zipper;

// TODO: Cuando el proceso falla, no se está unlinkeando los ficheros generando.
// TODO: Crear un script que limpie el servidor de archivos temporales que no se estén usando.


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
    }

    /**
     * Procesa la solicitud de conversión de PDF, valida la entrada, y prepara una respuesta de descarga.
     * @throws Exception Si la solicitud no es válida o falla la carga del archivo.
     */
    public function convert(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'], $_POST['format'], $_POST['pages'])) {
            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $inputFile = $_FILES['pdf']['tmp_name'];
                $outputBase = __DIR__ . '/../tmps/' . uniqid('pdf_convert_');
                $format = $_POST['format'];
                $pages = $this->parsePageInput($_POST['pages']);

                // Calcula el total de pasos basado en el número de páginas a convertir
                $totalSteps = count($pages);
                $operationId = uniqid('pdf_convert_');
                $_SESSION['operationId'] = $operationId;
                $jsonUrl = '../tmps/' . $operationId . '_progress.json';

                // Inicializa el archivo JSON con el total de pasos y el paso actual
                $progressData = ['totalSteps' => $totalSteps, 'currentStep' => 0];
                file_put_contents($jsonUrl, json_encode($progressData));

                $this->tracker->setOperationId($operationId);

                foreach ($pages as $page) {
                    $outputFile = $outputBase . "_page_$page";
                    $this->model->convertPdf($inputFile, $outputFile, $format, $page);
                    $this->tracker->incrementStep(); // Incremento tracker
                    error_log("Página $page convertida."); // Log de seguimiento
                }

                $zipFile = $this->zipper->createZip($outputBase, $format);
                error_log("Archivo ZIP creado: $zipFile"); // Log de seguimiento

                $_SESSION['zipFile'] = $zipFile;

                echo json_encode(['success' => true]);
                exit;
            } else {
                echo "Error al cargar el archivo: " . $_FILES['pdf']['error'];
                error_log("Error al cargar el archivo: " . $_FILES['pdf']['error']); // Log de seguimiento
            }
        } else {
            echo "Método no soportado o datos faltantes.";
            error_log("Método no soportado o datos faltantes."); // Log de seguimiento
        }
    }

    public function download(): void
    {
        // Recupera el nombre del archivo zip de la sesión
        $zipFile = $_SESSION['zipFile'];

        if (file_exists($zipFile)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="converted_files.zip"');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);

            sleep(5);

            // Elimina el archivo ZIP
            unlink($zipFile);

            // Obtiene todos los archivos PNG en el directorio tmps
            $pngFiles = glob(__DIR__ . '/../tmps/*.png');

            // Elimina cada archivo PNG
            foreach ($pngFiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            unlink($this->tracker->getOperationId() . '_progress.json');
        } else {
            echo "Archivo no encontrado.";
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


