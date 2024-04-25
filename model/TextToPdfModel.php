<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Clase que maneja la conversión de documentos ODT a PDF utilizando LibreOffice.
 */
class TextToPdfModel {
    private $libreOfficeHome;

    public function __construct() {
        $this->libreOfficeHome = getenv('LIBREOFFICE_HOME') ?: '/tmp/libreoffice_home';
    }

    /**
     * @throws Exception
     */
    public function convertToPdf(string $file, string $pages): string {
        if (!file_exists($file) || !is_readable($file)) {
            throw new Exception("El archivo especificado no existe o no se puede leer.");
        }

        if (!$this->validatePageRange($pages)) {
            throw new Exception("El rango de páginas especificado no es válido.");
        }

        $outputDir = sys_get_temp_dir();
        $outputFileName = basename($file, ".odt") . ".pdf";
        $outputFile = $outputDir . '/' . $outputFileName;

        $formattedPages = $this->formatPageRange($pages);
        $command = "HOME=" . escapeshellarg($this->libreOfficeHome) . " libreoffice --headless --convert-to pdf:writer_pdf_Export --outdir "
            . escapeshellarg($outputDir) . " " . escapeshellarg($file) . " PageRange=" . escapeshellarg($formattedPages);

        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var !== 0 || !file_exists($outputFile) || filesize($outputFile) === 0) {
            throw new Exception("La conversión falló. LibreOffice no pudo convertir el archivo.");
        }

        return $outputFile;
    }

    private function validatePageRange(string $pages): bool {
        return preg_match('/^\d+(-\d+)?(,\s*\d+(-\d+)?)*$/', $pages);
    }

    private function formatPageRange(string $pages): string {
        return str_replace(' ', '', $pages);
    }
}