<?php
require_once __DIR__ . '/../model/ImageToPdfModel.php';

/**
 * Controlador para manejar la conversión de imágenes a PDF.
 */
class ImageToPdfController
{
    /**
     * Procesa la solicitud de conversión de imágenes a PDF.
     *
     * @return void
     */
    public function procesarSolicitud(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
                $imagePaths = [];
                $uploadDir = __DIR__ . '/../tmps/'; // Directorio de carga

                // Crear el directorio de carga si no existe
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Procesar cada imagen cargada
                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    // Verificar si hubo errores al cargar la imagen
                    if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                        echo "Error al cargar el archivo: " . htmlspecialchars($_FILES['images']['name'][$key]) . " - Código de error: " . $_FILES['images']['error'][$key] . "<br>";
                        continue;
                    }

                    $uniqueId = uniqid('IMG_', true); // Generar un ID único
                    $filePath = $uploadDir . $uniqueId . '_' . basename($_FILES['images']['name'][$key]); // Establecer la ruta de destino con ID único
                    // Mover el archivo cargado al directorio de destino
                    if (move_uploaded_file($tmpName, $filePath)) {
                        $imagePaths[] = $filePath; // Agregar la ruta del archivo al array de rutas de imágenes
                    } else {
                        echo "Error al mover el archivo: " . htmlspecialchars($_FILES['images']['name'][$key]) . "<br>";
                    }
                }

                // Verificar si se han cargado imágenes
                if (!empty($imagePaths)) {
                    $outputPdf = $uploadDir . uniqid('PDF_', true) . '.pdf'; // Ruta del archivo PDF de salida con ID único
                    $converter = new ImageToPdfModel(); // Crear una instancia del modelo
                    $pdfPath = $converter->convertToPdf($imagePaths, $outputPdf); // Convertir las imágenes a PDF

                    // Forzar la descarga del archivo PDF
                    if (file_exists($pdfPath)) {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="' . basename($pdfPath) . '"');
                        header('Content-Length: ' . filesize($pdfPath));
                        readfile($pdfPath);

                        // Eliminar archivos temporales
                        foreach ($imagePaths as $imagePath) {
                            unlink($imagePath);
                        }
                        unlink($pdfPath);
                        exit;
                    } else {
                        echo "Error al crear el archivo PDF.<br>";
                    }
                } else {
                    echo "No se han subido imágenes correctamente.<br>";
                }
            } else {
                echo "No se han enviado imágenes.<br>";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }
    }
}

// Crear una instancia del controlador y procesar la solicitud
$controller = new ImageToPdfController();
$controller->procesarSolicitud();
