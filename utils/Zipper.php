<?php

namespace Utils;
use ZipArchive;

/**
 * Clase para manejar la creación de archivos ZIP.
 */
class Zipper {
    public function createZip(string $outputFilesBase, string $format): string {
        error_log("Iniciando creación del archivo ZIP...");
        $zip = new ZipArchive();
        $zipFilename = $outputFilesBase . '.zip';
        $extension = $format === 'jpeg' ? 'jpg' : $format;

        $files = glob($outputFilesBase . '*.' . $extension);
        if (!$files) {
            error_log("No se encontraron archivos generados con la ruta base: $outputFilesBase y extensión: $extension");
            exit("No se encontraron archivos generados.");
        }

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            error_log("No se puede abrir el archivo <$zipFilename>");
            exit("Cannot open <$zipFilename>\n");
        }

        foreach ($files as $file) {
            if ($zip->addFile($file, basename($file))) {
                error_log("Archivo añadido al ZIP: $file");
            } else {
                error_log("Error al añadir archivo al ZIP: $file");
            }
        }

        $zip->close();
        error_log("Archivo ZIP creado exitosamente: $zipFilename");
        return $zipFilename;
    }

    /**
     * Crea un archivo ZIP específicamente para los archivos PDF individuales.
     * @param array $outputPaths
     * @return string Ruta del archivo ZIP creado.
     */
    public function createPdfZip(array $outputPaths): string {
        set_time_limit(500);
        $zip = new ZipArchive();
        $zipFilename = __DIR__ . '/../tmps/' . '/PDFs.zip';

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            error_log("Cannot open <$zipFilename>");
            exit("Cannot open <$zipFilename>\n");
        }

        foreach ($outputPaths as $filePath) {
            if ($zip->addFile($filePath, basename($filePath))) {
                error_log("Archivo añadido al ZIP: $filePath");
            } else {
                error_log("Error al añadir archivo al ZIP: $filePath");
            }
        }

        $zip->close();
        error_log("Archivo ZIP creado exitosamente: $zipFilename");
        return $zipFilename;
    }

}