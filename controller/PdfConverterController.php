<?php
require_once __DIR__ . '/../model/PdfConverterModel.php';


/**
 * Controlador para manejar la conversión de archivos PDF a diferentes formatos de imagen.
 */
class PdfConverterController
{
    private PdfConverterModel $model;

    public function __construct()
    {
        $this->model = new PdfConverterModel();
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
            } else {
                echo "Error al cargar el archivo: " . $_FILES['pdf']['error'];
            }
        } else {
            echo "Método no soportado o datos faltantes.";
        }
    }

    /**
     * Crea un archivo ZIP con todos los archivos de salida generados.
     * @param string $outputFilesBase Ruta base de los archivos de salida.
     * @param string $format Formato de los archivos de salida.
     * @return string Ruta del archivo ZIP creado.
     */
    private function createZip(string $outputFilesBase, string $format): string
    {
        $zip = new ZipArchive();
        $zipFilename = $outputFilesBase . '.zip';
        $extension = $format === 'jpeg' ? 'jpg' : $format; // para manejar correctamente JPEG

        // patrón glob para incluir correctamente SVG que no tiene múltiples archivos con sufijos
        $files = glob($outputFilesBase . '*.' . $extension);
        if (!$files) {
            exit("No se encontraron archivos generados.");
        }

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            exit("Cannot open <$zipFilename>\n");
        }

        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();
        return $zipFilename;
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
}

// Crear una instancia de la clase y llamar al método convert
$controller = new PdfConverterController();
try {
    $controller->convert();
} catch (Exception $e) {
}