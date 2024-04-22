<?php
// utils/Zipper.php

class Zipper
{
    /**
     * Crea un archivo ZIP a partir de una lista de archivos.
     *
     * @param string $outputFilesBase Base del nombre de los archivos a incluir.
     * @param string $format Formato de los archivos a añadir.
     * @return string El nombre del archivo ZIP creado.
     */
    public function createZip(string $outputFilesBase, string $format): string
    {
        $zip = new ZipArchive();
        $zipFilename = $outputFilesBase . '.zip';
        $extension = $format === 'jpeg' ? 'jpg' : $format;  // Ajusta la extensión correctamente

        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            error_log("No se pudo abrir el archivo ZIP para escritura: " . $zipFilename);
            return "";  // Considerar un manejo de errores alternativo
        }

        // Busca todos los archivos JPEG generados, incluyendo sufijos de número de página
        $files = glob($outputFilesBase . '*.jpg');
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
            error_log("Archivo añadido al ZIP: " . $file);
        }

        if (!$files) {
            error_log("No se encontraron archivos para añadir al ZIP.");
            return "";  // Podría ser útil retornar una indicación de fallo
        }

        $zip->close();
        return $zipFilename;
    }
}