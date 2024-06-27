<?php

require_once __DIR__ . '/PdfExtractorModel.php';
require_once __DIR__ . '/../utils/clean/TempCleaner.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Modelo para la conversión de archivos PDF a texto y ODT.
 */
class PdfToTextModel
{
    private string $tempDir = '/var/tmp/pdfmax2_temps/';

    /**
     * Convierte un archivo PDF a texto.
     *
     * @param string $file Ruta del archivo PDF a convertir.
     * @return bool|string|null Texto extraído del PDF.
     */
    public function convertToText(string $file): bool|string|null
    {
        $command = "pdftotext " . escapeshellarg($file) . " -";
        return shell_exec($command);
    }

    /**
     * Convierte un archivo PDF a ODT.
     *
     * @param string $file Ruta del archivo PDF a convertir.
     * @return string Contenido del archivo ODT generado.
     * @throws Exception Si no se pudo crear el archivo de salida.
     */
    public function convertToOdt(string $file): string
    {
        $outputFilesBase = $this->tempDir . uniqid('output');
        $htmlFile = $outputFilesBase . '.html';
        $odtFile = $outputFilesBase . '.odt';

        // pdftohtml para convertir de pdf a html
        $command = "pdftohtml -c -noframes " . escapeshellarg($file) . " " . escapeshellarg($htmlFile);
        error_log("Ejecutando comando pdftohtml: $command");
        $output = shell_exec($command);
        error_log("Salida pdftohtml: $output");

        // Verificar si el archivo HTML fue generado correctamente
        if (!file_exists($htmlFile) || filesize($htmlFile) === 0) {
            throw new Exception("Error generando el archivo HTML. Archivo no encontrado o vacío.");
        }

        // Limpiar el contenido HTML para eliminar la ruta del archivo
        $htmlContent = file_get_contents($htmlFile);
        $htmlContent = preg_replace('/<title>.*<\/title>/s', '', $htmlContent);
        $htmlContent = preg_replace('/<meta name="generator" content="pdftohtml.*?\/>/s', '', $htmlContent);
        file_put_contents($htmlFile, $htmlContent);

        // Pandoc para convertir de html a odt
        $command = "pandoc " . escapeshellarg($htmlFile) . " -o " . escapeshellarg($odtFile);
        error_log("Ejecutando comando pandoc: $command");
        $output = shell_exec($command);
        error_log("Salida pandoc: $output");

        // Verificar si el archivo ODT fue generado correctamente
        if (!file_exists($odtFile) || filesize($odtFile) === 0) {
            throw new Exception("Error generando el archivo ODT. Archivo no encontrado o vacío.");
        }

        $odtContent = file_get_contents($odtFile);
        return $odtContent;
    }
}
