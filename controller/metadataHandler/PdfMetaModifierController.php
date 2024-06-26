<?php

require_once __DIR__ . '/../../model/PdfMetaModifierModel.php';
require_once __DIR__ . '/../../utils/metadata/PdfPageCounter.php';
require_once __DIR__ . '/MetadataFormatter.php';

/**
 * Controlador para modificar los metadatos de archivos PDF.
 */
class PdfMetaModifierController {
    public PdfMetaModifierModel $model;
    private PdfPageCounter $pageCounter;

    /**
     * Inicializa el controlador y su modelo asociado.
     */
    public function __construct() {
        $this->model = new PdfMetaModifierModel();
        $this->pageCounter = new PdfPageCounter();
    }

    /**
     * Procesa la subida de un archivo y lo mueve a una ubicación temporal segura.
     *
     * @param array $file Array asociativo del archivo cargado.
     * @return string|null Ruta del archivo si la subida es exitosa, null si falla.
     * @throws Exception Si el tipo de archivo o el nombre no son válidos.
     */
    public function handleFileUpload(array $file): ?string {
        $uploadDir = sys_get_temp_dir();
        $fileName = basename($file['name']);

        // Validación de tipo de archivo
        $fileType = mime_content_type($file['tmp_name']);
        if ($fileType !== 'application/pdf') {
            throw new Exception("Solo se permiten archivos PDF.");
        }

        // Validación de nombre de archivo (sin caracteres extraños)
        if (!preg_match('/^[a-zA-Z0-9_\-.]+$/', $fileName)) {
            throw new Exception("El nombre del archivo contiene caracteres no permitidos.");
        }

        $uploadPath = $uploadDir . '/' . $fileName;

        try {
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return $uploadPath;
            } else {
                throw new Exception("Error al mover el archivo subido.");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene y muestra los metadatos de un archivo PDF.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return bool|string|null Metadatos del archivo PDF.
     */
    public function getAndShowMetaData(string $filePath): bool|string|null {
        return $this->model->getMetaData($filePath);
    }

    /**
     * Obtiene y renderiza los metadatos de un archivo PDF.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return array Metadatos formateados.
     * @throws Exception Si el archivo está encriptado o no se puede leer.
     */
    public function getAndRenderMetaData(string $filePath): array {
        if (empty($filePath)) {
            throw new Exception("Ruta del archivo PDF no especificada.");
        }

        $encryptionStatus = $this->model->isEncrypted($filePath);
        if ($encryptionStatus === 'Yes') {
            throw new Exception("El archivo PDF está encriptado y no se pueden leer los metadatos.");
        } elseif ($encryptionStatus === 'Error') {
            throw new Exception("No se pudo leer el archivo PDF. Puede estar encriptado o dañado.");
        }

        $metaData = $this->model->getMetaData($filePath);
        $numPages = $this->pageCounter->countPages($filePath);
        $friendlyMetadata = MetadataFormatter::getFriendlyMetadata($metaData, $numPages);
        return ['metadata' => $friendlyMetadata];
    }

    /**
     * Actualiza y guarda los metadatos de un archivo PDF.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @param array $postData Datos recibidos del formulario.
     * @return string|null Ruta del archivo actualizado si es exitoso, null si falla.
     */
    public function updateAndSaveMetaData(string $filePath, array $postData): ?string {
        try {
            // Construir comando exiftool para actualizar metadatos
            $command = "exiftool -overwrite_original";

            if (!empty($postData['author'])) {
                $command .= " -Author=" . escapeshellarg($postData['author']);
            }

            if (!empty($postData['title'])) {
                $command .= " -Title=" . escapeshellarg($postData['title']);
            }

            if (!empty($postData['subject'])) {
                $command .= " -Subject=" . escapeshellarg($postData['subject']);
            }

            if (!empty($postData['keywords'])) {
                $command .= " -Keywords=" . escapeshellarg($postData['keywords']);
            }

            $command .= " " . escapeshellarg($filePath);

            // Log del comando exiftool
            error_log("Comando exiftool ejecutado: " . $command);

            // Ejecutar comando exiftool
            exec($command, $output, $return_var);

            // Log del resultado del comando exiftool
            error_log("Resultado del comando exiftool: " . implode("\n", $output));

            if ($return_var != 0) {
                throw new Exception("Error al ejecutar exiftool: " . implode("\n", $output));
            }

            // Verificar si el archivo se actualizó correctamente
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                throw new Exception("El archivo actualizado no se generó correctamente.");
            }

            // Renombrar el archivo si se ha proporcionado un nuevo título
            if (!empty($postData['title'])) {
                $newFilePath = dirname($filePath) . '/' . preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $postData['title']) . '.pdf';
                rename($filePath, $newFilePath);
                $filePath = $newFilePath;
            }

            // Asegurar que la sesión se actualice con la nueva ruta del archivo
            $_SESSION['updatedFilePath'] = $filePath;
            return $filePath;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
