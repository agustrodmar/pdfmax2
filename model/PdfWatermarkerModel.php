<?php

namespace Model;
use Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PdfWatermarkerModel {
    /**
     * Añade una marca de agua a un archivo PDF.
     *
     * @param string $pdfPath Ruta al archivo PDF original.
     * @param string $watermarkPath Ruta al archivo PDF de la marca de agua.
     * @param string $outputPdfPath Ruta al archivo PDF de salida.
     * @return string Salida del comando pdftk.
     * @throws Exception Si ocurre un error durante la ejecución del comando.
     */
    public function addWatermark(string $pdfPath, string $watermarkPath, string $outputPdfPath): string {
        $command = "pdftk " . escapeshellarg($pdfPath) . " stamp " . escapeshellarg($watermarkPath) . " output " . escapeshellarg($outputPdfPath);
        $output = shell_exec($command . " 2>&1");

        if ($output === null) {
            throw new Exception("Error ejecutando pdftk. Verifica que pdftk esté instalado y accesible desde la línea de comandos.");
        }

        return $output;
    }
}