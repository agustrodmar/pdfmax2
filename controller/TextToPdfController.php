<?php

use utils\clean\TempCleaner;

require_once(__DIR__ . '/../model/TextToPdfModel.php');
require_once __DIR__ . '/../utils/PdfResponseSender.php';
require_once __DIR__ . '/../utils/clean/TempCleaner.php';

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
        $uploadDir = '/var/tmp/pdfmax2_temps/' . uniqid('pdf_upload_', true);
        if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            throw new Exception(sprintf('El directorio "%s" no pudo ser creado', $uploadDir));
        }

        $cleaner = new TempCleaner($uploadDir);

        try {
            $file = $_FILES['file']['tmp_name'];
            $pages = $_POST['pages'] ?? '';

            if (!$file || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error en la carga del archivo o archivo no recibido.");
            }

            // Soporta ODT, DOCX y TXT
            $allowedTypes = [
                'application/vnd.oasis.opendocument.text', // ODT
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
                'text/plain' // TXT
            ];

            $fileType = mime_content_type($file);
            $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            if (!in_array($fileType, $allowedTypes) || !in_array($fileExtension, ['odt', 'docx', 'txt'])) {
                throw new Exception("Tipo de archivo no soportado. Los archivos deben ser ODT, DOCX o TXT.");
            }

            // Ignorar el rango de páginas si el archivo es TXT
            if ($fileExtension === 'txt') {
                $pages = '';
            }

            // Mover el archivo subido al directorio temporal seguro
            $safeFilePath = $uploadDir . '/' . basename($_FILES['file']['name']);
            if (!move_uploaded_file($file, $safeFilePath)) {
                throw new Exception("Error al mover el archivo subido.");
            }

            $outputFile = $this->model->convertToPdf($safeFilePath, $pages);

            // Validar el rango de páginas si se ha especificado
            if ($pages && $fileExtension !== 'txt') {
                $totalPages = $this->getTotalPages($outputFile);
                if (!$this->isValidPageRange($pages, $totalPages)) {
                    throw new Exception("El rango de páginas especificado no es válido. El documento tiene $totalPages páginas.");
                }
            }

            $this->sendPdfToClient($outputFile);
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        } finally {
            // Limpiar el directorio temporal
            $cleaner->clean();
        }
    }


    /**
     * Obtiene el número total de páginas en un archivo PDF.
     *
     * @param string $filePath La ruta del archivo PDF.
     * @return int El número total de páginas en el PDF.
     * @throws Exception Si no se puede obtener el número de páginas.
     */
    private function getTotalPages(string $filePath): int {
        $output = [];
        $returnVar = 0;
        exec("pdfinfo " . escapeshellarg($filePath), $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception("No se pudo obtener el número total de páginas del documento PDF.");
        }

        foreach ($output as $line) {
            if (preg_match('/^Pages:\s+(\d+)$/', $line, $matches)) {
                return (int)$matches[1];
            }
        }

        throw new Exception("No se pudo determinar el número de páginas del documento PDF.");
    }

    /**
     * Verifica si el rango de páginas es válido respecto al número total de páginas del documento.
     *
     * @param string $pages El rango de páginas a verificar.
     * @param int $totalPages El número total de páginas en el documento.
     * @return bool True si el rango es válido, False en caso contrario.
     */
    private function isValidPageRange(string $pages, int $totalPages): bool {
        $pageRanges = explode(',', $pages);
        foreach ($pageRanges as $range) {
            if (str_contains($range, '-')) {
                list($start, $end) = explode('-', $range);
                if ($start > $end || $start < 1 || $end > $totalPages) {
                    return false;
                }
            } else {
                if ($range < 1 || $range > $totalPages) {
                    return false;
                }
            }
        }
        return true;
    }
}

$controller = new TextToPdfController();
try {
    $controller->convert();
} catch (Exception $e) {
    echo $e->getMessage();
}
