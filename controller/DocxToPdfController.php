<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../model/DocxToPdfModel.php';


class DocxToPdfController {
    public function convert(): void
    {
        if ($_FILES['docxFile']['error'] === UPLOAD_ERR_OK && $_FILES['docxFile']['type'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            error_log("Converting DOCX to PDF...");
            $model = new DocxToPdfModel();
            $outputPath = $model->convertDocxToPdf($_FILES['docxFile']['tmp_name']);
            error_log("Conversion complete.");

            if (!empty($outputPath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($outputPath));
                readfile($outputPath);
                unlink($outputPath);  // Clean up the temporary PDF file
                exit;
            } else {
                http_response_code(500);
                echo "Error al convertir el archivo.";
            }
        } else {
            http_response_code(400);
            echo "Error: archivo no válido o no proporcionado.";
        }
    }
}

$controller = new DocxToPdfController();
$controller->convert();

