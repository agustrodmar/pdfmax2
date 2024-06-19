<?php

require_once __DIR__ . '/../model/PdfEncrypterModel.php';

class PdfEncryptController
{
    public function handleRequest(): void
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $uploadDir = realpath(__DIR__ . '/../tmps') . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && isset($_POST['password'])) {
            $pdfPath = $uploadDir . basename($_FILES['pdf']['name']);
            $password = htmlspecialchars($_POST['password']);
            $outputPdfPath = $uploadDir . 'archivo_encriptado.pdf';

            // Verificar que el archivo es un PDF
            $fileType = mime_content_type($_FILES['pdf']['tmp_name']);
            if ($fileType !== 'application/pdf') {
                echo "El archivo cargado no es un PDF válido.<br>";
                error_log("Error: El archivo cargado no es un PDF válido. Tipo de archivo: $fileType");
                return;
            }

            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
                    error_log("Archivo subido correctamente: $pdfPath");

                    try {
                        $encrypter = new PdfEncrypterModel();
                        $isEncrypted = $encrypter->encryptPdf($pdfPath, $outputPdfPath, $password);

                        if ($isEncrypted && file_exists($outputPdfPath)) {
                            header('Content-Type: application/pdf');
                            header('Content-Disposition: attachment; filename="' . basename($outputPdfPath) . '"');
                            header('Content-Length: ' . filesize($outputPdfPath));
                            ob_clean();
                            flush();
                            readfile($outputPdfPath);

                            // Eliminar archivos temporales después de la descarga
                            unlink($pdfPath);
                            unlink($outputPdfPath);
                            exit;
                        } else {
                            echo "Error al crear el archivo PDF encriptado.<br>";
                            error_log("Error: No se pudo encriptar el archivo PDF.");
                            if (!$isEncrypted) {
                                echo "Problema al ejecutar qpdf. Verifique los logs para más detalles.<br>";
                                error_log("Error: Problema al ejecutar qpdf.");
                            }
                        }
                    } catch (Exception $e) {
                        echo "Error durante la encriptación del PDF: " . $e->getMessage() . "<br>";
                        error_log("Excepción capturada durante la encriptación del PDF: " . $e->getMessage());
                    }
                } else {
                    echo "Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']) . "<br>";
                    error_log("Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']));
                }
            } else {
                echo "Error al cargar el archivo PDF: " . htmlspecialchars($_FILES['pdf']['name']) . " - Código de error: " . $_FILES['pdf']['error'] . "<br>";
                error_log("Error al cargar el archivo PDF: " . htmlspecialchars($_FILES['pdf']['name']) . " - Código de error: " . $_FILES['pdf']['error']);
            }
        } else {
            echo "No se han enviado el archivo PDF o la contraseña.<br>";
            error_log("Error: No se han enviado el archivo PDF o la contraseña.");
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud
$controller = new PdfEncryptController();
$controller->handleRequest();
