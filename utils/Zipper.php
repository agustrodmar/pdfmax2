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

        // patrón glob para incluir correctamente SVG que no tiene múltiples archivos con sufijos
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
}
