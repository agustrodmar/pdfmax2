<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Clase que maneja la conversión de documentos a PDF utilizando LibreOffice y pdftk.
 */
class TextToPdfModel {
    private string $libreOfficeHome;

    /**
     * Constructor de la clase.
     * Inicializa el directorio home de LibreOffice.
     */
    public function __construct() {
        $this->libreOfficeHome = getenv('LIBREOFFICE_HOME') ?: '/tmp/libreoffice_home';
    }

    /**
     * Convierte un archivo a PDF y extrae un rango específico de páginas.
     *
     * @param string $file Ruta del archivo original a convertir.
     * @param string $pages Rango de páginas a extraer del documento convertido.
     * @return string Ruta del archivo PDF final con las páginas especificadas.
     * @throws Exception Si el archivo no existe, no se puede leer, la conversión falla, o la extracción de páginas falla.
     */
    public function convertToPdf(string $file, string $pages): string {
        if (!file_exists($file) || !is_readable($file)) {
            throw new Exception("El archivo especificado no existe o no se puede leer.");
        }

        $outputDir = __DIR__ . '/../tmps/';
        $outputFileName = basename($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . ".pdf";
        $outputFile = $outputDir . '/' . $outputFileName;

        $command = "HOME=$this->libreOfficeHome libreoffice --headless --convert-to pdf:writer_pdf_Export --outdir "
            . escapeshellarg($outputDir) . " " . escapeshellarg($file);

        exec($command, $output, $returnVar);
        sleep(1); // un sleep para asegurar que el sistema de archivos se actualiza.

        if ($returnVar !== 0 || !file_exists($outputFile) || filesize($outputFile) === 0) {
            throw new Exception("La conversión inicial falló. LibreOffice no pudo convertir el archivo.");
        }

        // Ruta del archivo PDF final que contendrá solo las páginas seleccionadas
        $finalOutputFile = $outputDir . '/' . 'final_' . basename($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . ".pdf";

        // Comando para extraer rangos de páginas usando pdftk
        $pdftkCommand = "pdftk " . escapeshellarg($outputFile) . " cat " . escapeshellarg($pages) . " output " . escapeshellarg($finalOutputFile);
        exec($pdftkCommand, $pdftkOutput, $pdftkReturnVar);

        if ($pdftkReturnVar !== 0 || !file_exists($finalOutputFile) || filesize($finalOutputFile) === 0) {
            throw new Exception("Error al extraer el rango de páginas con pdftk.");
        }

        unlink($outputFile); // Eliminar el PDF completo generado inicialmente.

        return $finalOutputFile;
    }
}