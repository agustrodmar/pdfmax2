<?php
require_once __DIR__ . '/../model/PdfExtractorModel.php';
require_once __DIR__ . '/../utils/Zipper.php';

/**
 * Controlador para la extracción de páginas de un archivo PDF.
 */
class PDFExtractorController {

    /**
     * Procesa la solicitud HTTP para la descarga de un archivo PDF modificado.
     *
     * @return string Mensaje de resultado de la operación.
     */
    public function procesarSolicitud(): string
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Error: La solicitud debe ser de tipo POST.");
            }

            if (!isset($_FILES["pdfArchivo"]) || !isset($_POST["paginas"])) {
                throw new Exception("Error: Todos los campos son obligatorios.");
            }

            $pdfArchivo = $_FILES["pdfArchivo"]["tmp_name"];
            $paginas = str_replace(",", " ", $_POST["paginas"]);
            $outputFileName = __DIR__ . '/../tmps/' . 'PDF' . '.pdf';

            $pdfExtractor = new PDFExtractorModel();
            $pdfExtractor->extraerPaginas($pdfArchivo, $paginas, $outputFileName);

            if (!file_exists($outputFileName)) {
                throw new Exception("Error: No se pudo crear el archivo de salida.");
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($outputFileName) . '"');
            readfile($outputFileName);
            unlink($outputFileName);
            exit;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Procesa la solicitud HTTP para la descarga de múltiples archivos PDF en un archivo ZIP.
     *
     * @return string Mensaje de resultado de la operación.
     */
    public function procesarSolicitudIndividual(): string
    {
        try {
            set_time_limit(500);
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Error: La solicitud debe ser de tipo POST.");
            }

            if (!isset($_FILES["pdfArchivo"]) || !isset($_POST["paginas"])) {
                throw new Exception("Error: Todos los campos son obligatorios.");
            }

            $pdfArchivo = $_FILES["pdfArchivo"]["tmp_name"];
            $paginas = str_replace(",", " ", $_POST["paginas"]);
            // En lugar de usar sys_get_temp_dir(), especifica la ruta relativa
            $outputFilesBase = __DIR__ . '/../tmps/' . 'PDF';


            $pdfExtractor = new PDFExtractorModel();
            $outputPaths = $pdfExtractor->extraerPaginasIndividuales($pdfArchivo, $paginas, $outputFilesBase);

            $zipper = new Utils\Zipper();
            $zipFilename = $zipper->createPdfZip($outputPaths);

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFilename) . '"');
            readfile($zipFilename);
            array_map('unlink', $outputPaths);
            unlink($zipFilename);
            exit;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}

$controller = new PDFExtractorController();
$resultado = ($_POST["downloadMode"] === 'multiple') ? $controller->procesarSolicitudIndividual() : $controller->procesarSolicitud();
echo "Resultado: " . $resultado;
