<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Modelo para la conversión de archivos DOCX a PDF.
 */
class DocxToPdfModel {
    /**
     * Convierte un archivo DOCX a PDF utilizando LibreOffice.
     *
     * @param string $file Ruta del archivo DOCX a convertir.
     * @return string Ruta del archivo PDF generado.
     * @throws Exception Si no se pudo crear el archivo de salida.
     */
    public function convertDocxToPdf(string $file): string {
        $outputDir = sys_get_temp_dir();
        $outputFileName = basename($file, ".docx") . ".pdf";
        $outputFile = $outputDir . '/' . $outputFileName; // Construye la ruta completa del archivo de salida

        // Directorio HOME para LibreOffice configurado para evitar problemas de permisos
        $libreOfficeHome = "/tmp/libreoffice_home";

        // Comando para convertir DOCX a PDF usando LibreOffice
        $command = "HOME=$libreOfficeHome libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($outputDir) . " " . escapeshellarg($file);
        shell_exec($command);

        // Verifica si el archivo PDF se ha creado correctamente
        if (!file_exists($outputFile) || filesize($outputFile) === 0) {
            throw new Exception("Error: No se pudo crear el archivo de salida.");
        }

        return $outputFile;
    }
}