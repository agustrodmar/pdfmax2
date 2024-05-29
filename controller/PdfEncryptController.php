<?php

require_once __DIR__ . '/../model/PdfEncrypterModel.php';

class PdfEncryptController
{
    /**
     * Procesa la solicitud para encriptar un PDF.
     *
     * @return void
     */
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
            $password = $_POST['password'];
            $outputPdfPath = $uploadDir . 'archivo_encriptado.pdf';

            if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
                    $encrypter = new PdfEncrypterModel();
                    $isEncrypted = $encrypter->encryptPdf($pdfPath, $outputPdfPath, $password);

                    if ($isEncrypted && file_exists($outputPdfPath)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="' . basename($outputPdfPath) . '"');
                        header('Content-Length: ' . filesize($outputPdfPath));
                        readfile($outputPdfPath);

                        // Eliminar archivos temporales después de la descarga
                        unlink($pdfPath);
                        unlink($outputPdfPath);
                        exit;
                    } else {
                        echo "Error al crear el archivo PDF encriptado.<br>";
                        if (!$isEncrypted) {
                            echo "Problema al ejecutar qpdf. Verifique los logs para más detalles.<br>";
                        }
                    }
                } else {
                    echo "Error al mover el archivo: " . htmlspecialchars($_FILES['pdf']['name']) . "<br>";
                }
            } else {
                echo "Error al cargar el archivo PDF: " . htmlspecialchars($_FILES['pdf']['name']) . " - Código de error: " . $_FILES['pdf']['error'] . "<br>";
            }
        } else {
            echo "No se han enviado el archivo PDF o la contraseña.<br>";
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud
$controller = new PdfEncryptController();
$controller->handleRequest();
?>
