<?php
require_once __DIR__ . '/../model/PdfConverterModel.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Controlador para la conversión de archivos PDF a otros formatos.
 */
class PdfConverterController
{
    private PdfConverterModel $model;

    /**
     * Constructor del controlador.
     * Inicializa el modelo de conversión.
     */
    public function __construct()
    {
        $this->model = new PdfConverterModel();
    }

    /**
     * Convierte un archivo PDF a otro formato y lo envía al cliente.
     *
     * @throws Exception Si hay un error durante la conversión.
     */
    public function convert(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_FILES['pdf'], $_POST['format'], $_POST['pages'])) {
                throw new Exception("Método no soportado o datos faltantes.");
            }

            if ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al cargar el archivo: " . $_FILES['pdf']['error']);
            }

            $inputFile = $_FILES['pdf']['tmp_name'];
            $outputBase = sys_get_temp_dir() . '/' . uniqid('pdf_convert_');
            $format = $_POST['format'];
            $pages = $this->parsePageInput($_POST['pages']);  // Asume que existe un método para parsear el input de páginas

            foreach ($pages as $page) {
                $outputFile = $outputBase . "_page_$page";
                $this->model->convertPdf($inputFile, $outputFile, $format, $page);
            }

            $zipFile = $this->createZip($outputBase, $format);

            // Los encabezados HTTP y la respuesta
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="converted_files.zip"');
            header('Content-Length: ' . filesize($zipFile));
            ob_clean();
            flush();
            readfile($zipFile);
            unlink($zipFile);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
    }

    /**
     * Crea un archivo ZIP con los archivos de salida.
     *
     * @param string $outputFilesBase Ruta base de los archivos de salida.
     * @param string $format Formato de los archivos de salida.
     * @return string Ruta del archivo ZIP creado.
     * @throws Exception Si no se pudo abrir el archivo ZIP.
     */
    private function createZip(string $outputFilesBase, string $format): string
    {
        $zip = new ZipArchive();
        $zipFilename = $outputFilesBase . '.zip';
        $extension = $format === 'jpeg' ? 'jpg' : $format;

        // Ajustar el patrón glob para incluir correctamente SVG que no tiene múltiples archivos con sufijos
        $files = glob($outputFilesBase . '*.' . $extension);
        if (!$files) {
            throw new Exception("No se encontraron archivos para añadir al ZIP. 
            Comprueba la generación de archivos: " . $outputFilesBase . '*.' . $extension);
        }

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            throw new Exception("Cannot open <$zipFilename>\n");
        }

        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();
        return $zipFilename;
    }

    /**
     * Analiza el input de páginas y devuelve un array de páginas.
     *
     * @param string $input Input de páginas.
     * @return array Array de páginas.
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
}

// Crear una instancia de la clase y llamar al método convert
$controller = new PdfConverterController();
try {
    $controller->convert();
} catch (Exception $e) {
}
