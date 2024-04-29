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
        $outputDir = __DIR__ . '/../tmps/' ;
        $htmlFile = __DIR__ . '/../tmps/' . uniqid('output') . '.html';
        $odtFile = __DIR__ . '/../tmps/' . uniqid('output') . '.odt';

        // pdftohtml para ir de pdf a html
        $command = "pdftohtml -stdout " . escapeshellarg($file) . " > " . escapeshellarg($htmlFile);
        shell_exec($command);

        // Pandoc para ir de html a odt.
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

