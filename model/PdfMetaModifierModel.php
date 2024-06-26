<?php

/**
 * Clase PdfMetaModifierModel
 *
 * Modelo para manejar operaciones relacionadas con metadatos de archivos PDF.
 */
class PdfMetaModifierModel {

    /**
     * Obtiene los metadatos de un archivo PDF.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return bool|string|null Metadatos del archivo PDF o null en caso de error.
     */
    public function getMetaData(string $filePath): bool|string|null {
        $command = "pdftk " . escapeshellarg($filePath) . " dump_data";
        return shell_exec($command);
    }

    /**
     * Obtiene el tamaño de un archivo PDF.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return string Tamaño del archivo en bytes.
     */
    public function getFileSize(string $filePath): string {
        return filesize($filePath);
    }

    /**
     * Obtiene el tamaño del papel de un archivo PDF.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return string Tamaño del papel o 'Desconocido' si no se puede determinar.
     */
    public function getPaperSize(string $filePath): string {
        $command = "pdfinfo " . escapeshellarg($filePath);
        $output = shell_exec($command);
        if (preg_match('/Page size:\s+([0-9.]+ x [0-9.]+ [a-z]+)/i', $output, $matches)) {
            return $matches[1];
        }
        return 'Desconocido';
    }

    /**
     * Verifica si un archivo PDF está encriptado.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return string 'Yes', 'No' o 'Error' si no se puede determinar.
     */
    public function isEncrypted(string $filePath): string {
        $command = "pdfinfo " . escapeshellarg($filePath);
        $output = shell_exec($command);
        if ($output === null) {
            error_log("Error al ejecutar pdfinfo: archivo encriptado o pdfinfo no disponible");
            return 'Error';
        }
        if (preg_match('/Encrypted:\s+(yes|no)/i', $output, $matches)) {
            return ucfirst($matches[1]);
        }
        return 'Desconocido';
    }

    /**
     * Verifica si un archivo PDF contiene JavaScript.
     *
     * @param string $filePath Ruta del archivo PDF.
     * @return string 'Sí' si contiene JavaScript, 'No' en caso contrario.
     */
    public function containsJavaScript(string $filePath): string {
        $command = "pdftk " . escapeshellarg($filePath) . " dump_data output | grep -i javascript";
        $output = shell_exec($command);
        return empty($output) ? 'No' : 'Sí';
    }
}
