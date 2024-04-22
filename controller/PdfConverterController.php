<?php
require_once __DIR__ . '/../model/PdfConverterModel.php';
require_once __DIR__ . '/../utils/Zipper.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PdfConverterController
{
    private PdfConverterModel $model;

    public function __construct()
    {
        $this->model = new PdfConverterModel();
    }

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
    private function createZip($outputFilesBase, $format): string
    {
        $zip = new ZipArchive();
        $zipFilename = $outputFilesBase . '.zip';
        $extension = $format === 'jpeg' ? 'jpg' : $format; // Ajustar para manejar correctamente JPEG

        // Ajustar el patrón glob para incluir correctamente SVG que no tiene múltiples archivos con sufijos
        $files = glob($outputFilesBase . '*.' . $extension);
        if (!$files) {
            error_log("No se encontraron archivos para añadir al ZIP. Comprueba la generación de archivos: " . $outputFilesBase . '*.' . $extension);
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
    private function parsePageInput($input): array
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
$controller->convert();