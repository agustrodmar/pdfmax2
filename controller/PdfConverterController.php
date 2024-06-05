<?php

// Muestra errores para facilitar la depuración
use Utils\Zipper;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Requiere los archivos necesarios para la funcionalidad del controlador
require_once __DIR__ . '/../model/PdfConverterModel.php';
require_once __DIR__ . '/../utils/Zipper.php';

/**
 * Controlador para manejar la conversión de archivos PDF a diferentes formatos de imagen.
 */
class PdfConverterController
{
    private PdfConverterModel $model;
    private Zipper $zipper;

    public function __construct()
    {
        // Inicializa el modelo y el zipper
        $this->model = new PdfConverterModel();
        $this->zipper = new Zipper();
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

                foreach ($pages as $page) {
                    $outputFile = $outputBase . "_page_$page";
                    $this->model->convertPdf($inputFile, $outputFile, $format, $page);
                }

                // Crea un archivo ZIP con los resultados
                $zipFile = $this->zipper->createZip($outputBase, $format);
                $_SESSION['zipFile'] = $zipFile;

                // Enviar el archivo ZIP para descarga
                $this->download($zipFile);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => "Error al cargar el archivo: " . $_FILES['pdf']['error']]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => "Método no soportado o datos faltantes."]);
        }
    }

    /**
     * Maneja la descarga del archivo ZIP generado y elimina archivos temporales.
     */
    public function download(string $zipFile): void
    {
        if (file_exists($zipFile)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="converted_files.zip"');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);

            // Espera 10 segundos antes de eliminar el archivo ZIP
            sleep(10);
            unlink($zipFile);

            // Elimina los archivos temporales generados
            $this->deleteFiles(__DIR__ . '/../tmps/*');
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

// Crear una instancia de la clase y llamar al método convert si el método es POST
$controller = new PdfConverterController();
try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $controller->convert();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => "Método no soportado."]);
    }
} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
}
