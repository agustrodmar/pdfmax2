<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DocxToPdfModel {
    /**
     * Convierte un archivo DOCX a PDF utilizando LibreOffice.
     *
     * @param string $file Ruta del archivo DOCX a convertir.
     * @return string Ruta del archivo PDF generado o una cadena vacía si falla.
     */
    public function convertDocxToPdf($file): string {
        $outputDir = sys_get_temp_dir();
        $outputFileName = basename($file, ".docx") . ".pdf";
        $outputFile = $outputDir . '/' . $outputFileName; // Construye la ruta completa del archivo de salida

        // Directorio HOME para LibreOffice configurado para evitar problemas de permisos
        $libreOfficeHome = "/tmp/libreoffice_home";

        // Comando para convertir DOCX a PDF usando LibreOffice
        $command = "HOME={$libreOfficeHome} libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($outputDir) . " " . escapeshellarg($file);
        shell_exec($command);

        // Verifica si el archivo PDF se ha creado correctamente
        if (!file_exists($outputFile) || filesize($outputFile) === 0) {
            return "";
        }

        return $outputFile;
    }
}
