<?php

namespace Utils;
use ZipArchive;


/**
 * Clase para manejar la creación de archivos ZIP.
 */
class Zipper
{
    /**
     * Crea un archivo ZIP con todos los archivos de salida generados.
     * @param string $outputFilesBase Ruta base de los archivos de salida.
     * @param string $format Formato de los archivos de salida.
     * @return string Ruta del archivo ZIP creado.
     */
    public function createZip(string $outputFilesBase, string $format): string
    {
        $zip = new ZipArchive();
        $zipFilename = $outputFilesBase . '.zip';
        $extension = $format === 'jpeg' ? 'jpg' : $format; // para manejar correctamente JPEG

        $files = glob($outputFilesBase . '*.' . $extension);
        if (!$files) {
            exit("No se encontraron archivos generados.");
        }

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            exit("Cannot open <$zipFilename>\n");
        }

        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();
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
        return $zipFilename;
    }

}