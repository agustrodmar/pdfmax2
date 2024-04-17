<?php
require_once '../model/pdfExtractorModel.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class pdfExtractorController {
    /**
     * Es el método con el que proceso la solicitud de extracción de páginas PDF.
     *
     * Este método procesa la solicitud POST para extraer páginas específicas de un archivo PDF
     * y guardarlas en un nuevo archivo PDF.
     *
     * @return string El resultado de la operación, que puede ser un mensaje de éxito o error.
     */
    public function procesarSolicitud(): string {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_FILES["pdfArchivo"]) && isset($_POST["paginas"]) &&
                isset($_POST["outputPath"]) && isset($_POST["outputFileName"])) {
                // Obtengo los datos del formulario
                $pdfArchivo = $_FILES["pdfArchivo"]["tmp_name"];
                $paginas = $_POST["paginas"];
                $outputPath = $_POST["outputPath"];
                $outputFileName = $_POST["outputFileName"];

                $paginas = str_replace(",", " ", $paginas);

                // Muevo el archivo cargado a la ruta proporcionada por el usuario
                $tempPath = $outputPath . '/' . $_FILES["pdfArchivo"]["name"];
                move_uploaded_file($pdfArchivo, $tempPath);

                $pdfExtractor = new pdfExtractorModel();

                // Mando a concatenar la ruta de salida con el nombre del archivo de salida
                $outputPath = rtrim($outputPath, '/') . '/' . $outputFileName . '.pdf';

                echo "<script>console.log('Debug: tempPath = " . $tempPath . "');</script>";
                echo "<script>console.log('Debug: outputPath = " . $outputPath . "');</script>";

                $resultado = $pdfExtractor->extraerPaginas($tempPath, $paginas, $outputPath);

                echo "<script>console.log('Debug: resultado = " . $resultado . "');</script>";

                return $resultado;
            } else {
                return "Error: Todos los campos son obligatorios.";
            }
        } else {
            return "Error: La solicitud debe ser de tipo POST.";
        }
    }
}

$controller = new PDFExtractorController();
$resultado = $controller->procesarSolicitud();

echo "Resultado: " . $resultado;
