<?php
require_once '../model/PdfExtractorModel.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Controlador para la extracción de páginas de un archivo PDF.
 */
class PDFExtractorController {
    /**
     * Procesa la solicitud HTTP. Si es un POST, intenta extraer las páginas del PDF.
     *
     * @return string Mensaje de resultado de la operación.
     */
    public function procesarSolicitud(): string
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                throw new Exception("Error: La solicitud debe ser de tipo POST.");
            }

            if (!isset($_FILES["pdfArchivo"]) || !isset($_POST["paginas"])) {
                throw new Exception("Error: Todos los campos son obligatorios.");
            }

            $pdfArchivo = $_FILES["pdfArchivo"]["tmp_name"];
            $paginas = str_replace(",", " ", $_POST["paginas"]);

            // Genera un nombre de archivo temporal único
            $outputFileName = tempnam(sys_get_temp_dir(), 'PDF') . '.pdf';

            $pdfExtractor = new PDFExtractorModel();
            $resultado = $pdfExtractor->extraerPaginas($pdfArchivo, $paginas, $outputFileName);

            if (!file_exists($outputFileName)) {
                throw new Exception("Error: No se pudo crear el archivo de salida.");
            }

            // Envía el archivo al navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($outputFileName) . '"');
            readfile($outputFileName);
            // Limpia y elimina el archivo temporal después de la descarga
            unlink($outputFileName);
            exit;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}

$controller = new PDFExtractorController();
$resultado = $controller->procesarSolicitud();
echo "Resultado: " . $resultado;