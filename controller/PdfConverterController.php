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
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'], $_POST['format'])) {
            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $inputFile = $_FILES['pdf']['tmp_name'];
                $outputBase = sys_get_temp_dir() . '/' . uniqid('pdf_convert_');
                $format = $_POST['format'];

                $outputFilesBase = $this->model->convertPdf($inputFile, $outputBase, $format);
                $zipFile = $this->createZip($outputFilesBase, $format);

                // Los encabezados HTTP
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="converted_files.zip"');
                header('Content-Length: ' . filesize($zipFile));
                ob_clean();  // Limpiar cualquier salida de buffer antes de enviar el archivo
                flush();     // Vaciar los buffers del sistema
                readfile($zipFile);
                unlink($zipFile); // Eliminar el archivo ZIP después de enviarlo
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
        $extension = $format === 'jpeg' ? 'jpg' : $format; // Corrige la extensión para JPEG

        // Corregir el patrón de búsqueda para incluir archivos con cualquier sufijo numérico
        $files = glob($outputFilesBase . '*.' . $extension . '*');
        if (!$files) {
            error_log("No se encontraron archivos para añadir al ZIP. Comprueba la generación de archivos.");
            exit("No se encontraron archivos generados.");
        }

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            exit("Cannot open <$zipFilename>\n");
        }

        // Agregar cada archivo generado al ZIP
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();
        return $zipFilename;
    }
}

// Crear una instancia de la clase y llamar al método convert
$controller = new PdfConverterController();
$controller->convert();