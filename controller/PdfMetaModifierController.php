<?php

require_once __DIR__ . '/../model/PdfMetaModifierModel.php';


/**
 * Controlador para modificar los metadatos de archivos PDF.
 */
class PdfMetaModifierController {

    private PdfMetaModifierModel $model;

    /**
     * Inicializa el controlador y su modelo asociado.
     */
    public function __construct() {
        $this->model = new PdfMetaModifierModel();
    }

    /**
     * Procesa la subida de un archivo y lo mueve a una ubicación temporal segura.
     *
     * @param array $file Array asociativo del archivo cargado.
     * @return string|null Ruta del archivo si la subida es exitosa, null si falla.
     */
    public function handleFileUpload(array $file): ?string {
        $uploadDir = sys_get_temp_dir();
        $uploadPath = $uploadDir . '/' . basename($file['name']);

        try {
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return $uploadPath;
            } else {
                throw new Exception("Error al mover el archivo subido");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene y muestra los metadatos de un archivo PDF.
     *
     * @param string $filePath Ruta al archivo PDF.
     * @return bool|string|null Metadatos del PDF o null si hay errores.
     */
    public function getAndShowMetaData(string $filePath): bool|string|null
    {
        return $this->model->getMetaData($filePath);
    }

    /**
     * Actualiza los metadatos de un archivo PDF y guarda el archivo actualizado.
     *
     * @param string $filePath Ruta al archivo PDF.
     * @param array $postData Datos POST que contienen los nuevos valores de los metadatos.
     * @return string|null Ruta del archivo actualizado si tiene éxito, null si hay errores.
     */
    public function updateAndSaveMetaData(string $filePath, array $postData): ?string {
        try {
            $newMetaData = $this->buildMetaDataString($postData);

            $metaDataFile = tempnam(sys_get_temp_dir(), 'meta');
            file_put_contents($metaDataFile, $newMetaData);

            $updatedFilePath = $filePath . '_temp';
            $command = "pdftk " . escapeshellarg($filePath) . " update_info " . escapeshellarg($metaDataFile) . " output " . escapeshellarg($updatedFilePath);
            exec($command, $output, $return_var);

            if ($return_var != 0) {
                throw new Exception("Error al ejecutar PDFTK: " . implode("\n", $output));
            }

            if (!file_exists($updatedFilePath) || filesize($updatedFilePath) === 0) {
                throw new Exception("El archivo actualizado no se generó correctamente.");
            }

            rename($updatedFilePath, $filePath);
            $_SESSION['updatedFilePath'] = $filePath;
            return $filePath;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Construye una cadena que representa los nuevos metadatos del PDF.
     *
     * @param array $postData Datos post que contienen los nuevos valores de los metadatos.
     * @return string Cadena de texto con los nuevos metadatos formateados.
     */
    private function buildMetaDataString(array $postData): string {
        $newMetaData = "InfoKey: Author\nInfoValue: " . htmlspecialchars($postData['author'], ENT_QUOTES, 'UTF-8') . "\n";
        foreach (['title', 'subject', 'keywords'] as $key) {
            if (isset($postData[$key])) {
                $newMetaData .= "InfoKey: " . ucfirst($key) . "\nInfoValue: " . htmlspecialchars($postData[$key], ENT_QUOTES, 'UTF-8') . "\n";
            }
        }
        $newMetaData .= "InfoKey: ModDate\nInfoValue: " . date("YmdHis") . "\n";
        return $newMetaData;
    }
}