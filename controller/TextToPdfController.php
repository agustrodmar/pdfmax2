<?php

require_once(__DIR__ . '/../model/TextToPdfModel.php');
require_once __DIR__ . '/../utils/PdfResponseSender.php';

/**
 * Clase TextToPdfController
 *
 * Esta clase se encarga de manejar las solicitudes de conversión de texto a PDF.
 * Utiliza el modelo TextToPdfModel para realizar la conversión y luego envía el PDF resultante al cliente.
 */
class TextToPdfController {
    /**
     * @var TextToPdfModel $model Una instancia del modelo TextToPdfModel.
     */
    private TextToPdfModel $model;
    use PdfResponseSender;

    /**
     * Constructor de la clase TextToPdfController.
     *
     * Inicializa una nueva instancia del modelo TextToPdfModel.
     */
    public function __construct() {
        $this->model = new TextToPdfModel();
    }

    /**
     * Método convert
     *
     * Este método maneja las solicitudes de conversión de texto a PDF.
     * Si la solicitud es válida, llama al modelo para convertir el archivo de entrada a PDF y luego envía el PDF resultante al cliente.
     * Si ocurre algún error durante la conversión, se envía un mensaje de error al cliente.
     *
     * @throws Exception Si ocurre algún error durante la conversión.
     */
    public function convert(): void {
        try {
            $file = $_FILES['file']['tmp_name'];
            $pages = $_POST['pages'] ?? '';

            if (!$file || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error en la carga del archivo o archivo no recibido.");
            }

            // Soporta ODT DOCKX y TXT
            $allowedTypes = [
                'application/vnd.oasis.opendocument.text', // ODT
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
                'text/plain' // TXT
            ];

            if (!in_array($_FILES['file']['type'], $allowedTypes)) {
                throw new Exception("Tipo de archivo no soportado. Los archivos deben ser ODT, DOCX o TXT.");
            }

            $outputFile = $this->model->convertToPdf($file, $pages);
            $this->sendPdfToClient($outputFile);
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
    }
}

$controller = new TextToPdfController();
try {
    $controller->convert();
} catch (Exception $e) {
}
