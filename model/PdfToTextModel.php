<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Modelo para la conversión de archivos PDF a texto y ODT.
 */
class pdfToTextModel {
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
        $outputDir = sys_get_temp_dir();
        $htmlFile = tempnam($outputDir, 'output') . '.html';
        $odtFile = tempnam($outputDir, 'output') . '.odt';

        // Convert PDF to HTML first
        $command = "pdftohtml -stdout " . escapeshellarg($file) . " > " . escapeshellarg($htmlFile);
        shell_exec($command);

        // Then convert HTML to ODT with Pandoc
        $command = "pandoc -s " . escapeshellarg($htmlFile) . " -o " . escapeshellarg($odtFile);
        shell_exec($command);

        if (!file_exists($odtFile) || filesize($odtFile) === 0) {
            throw new Exception("Error generating ODT file.");
        }

        $odtContent = file_get_contents($odtFile);
        unlink($htmlFile);
        unlink($odtFile);
        return $odtContent;
    }
}

