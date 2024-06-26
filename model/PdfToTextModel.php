<?php

use Utils\Zipper;
require_once __DIR__ . '/PdfExtractorModel.php';
require_once __DIR__ . '/../utils/Zipper.php';
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
        $outputDir = $this->tempDir;
        $htmlFile = $outputDir . uniqid('output') . '.html';
        $odtFile = $outputDir . uniqid('output') . '.odt';

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
        return $odtContent;
    }

    /**
     * Convierte páginas específicas de un archivo PDF a ODT.
     *
     * @param string $file Ruta del archivo PDF a convertir.
     * @param array $pages Las páginas a convertir.
     * @return string Ruta del archivo ZIP con los archivos ODT.
     * @throws Exception Si no se pudo crear el archivo de salida.
     */
    public function convertPagesToOdt(string $file, array $pages): string
    {
        error_log("Iniciando la conversión de páginas a ODT...");

        $outputFilesBase = $this->tempDir . uniqid('output');
        $pdfExtractor = new PDFExtractorModel();
        $pagesString = implode(' ', $pages);

        // Extrae las páginas del PDF en un solo archivo
        $pdfFile = $pdfExtractor->extraerPaginas($file, $pagesString, $outputFilesBase . '.pdf');
        error_log("Archivo PDF con páginas extraídas generado: " . $pdfFile);

        // Convierte el archivo PDF a ODT sin eliminar el archivo ODT
        $htmlFile = $this->tempDir . uniqid('output') . '.html';
        $odtFile = $outputFilesBase . '.odt';

        // pdftohtml para ir de pdf a html
        $command = "pdftohtml -stdout " . escapeshellarg($pdfFile) . " > " . escapeshellarg($htmlFile);
        shell_exec($command);
        error_log("Archivo HTML generado: " . $htmlFile);

        // Pandoc para ir de html a odt.
        $command = "pandoc -s " . escapeshellarg($htmlFile) . " -o " . escapeshellarg($odtFile);
        shell_exec($command);
        error_log("Archivo ODT generado: " . $odtFile);

        if (!file_exists($odtFile) || filesize($odtFile) === 0) {
            throw new Exception("Error generating ODT file.");
        }

        error_log("Archivos temporales generados.");

        return $odtFile;
    }
}