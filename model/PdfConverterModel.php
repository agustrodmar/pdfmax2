
<?php

/**
 * Modelo para convertir archivos PDF a formatos de imagen utilizando pdftocairo.
 */
class PdfConverterModel {

    /**
     * Convierte un archivo PDF a un formato de imagen especificado.
     *
     * @param string $inputFile Ruta al archivo PDF de entrada.
     * @param string $outputFile Ruta base para los archivos de salida.
     * @param string $format Formato de salida deseado.
     * @param string|null $pages Rango de páginas a convertir.
     * @return string Ruta al archivo de salida con la extensión correcta.
     */
    public function convertPdf(string $inputFile, string $outputFile, string $format, string $pages = null): string
    {
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $pageOption = "";
        if ($pages) {
            $pageOption = "-f $pages -l $pages ";
        }

        $command = "pdftocairo -" . $format . " " . $pageOption . "-r 300 " . escapeshellarg($inputFile) . " " . escapeshellarg($outputFile) . "." . $extension;
        $output = shell_exec($command . " 2>&1");

        // Revisión de archivos generados
        $pattern = $outputFile . '*.' . $extension;
        if ($pages) {
            $pattern .= "-" . $pages;
        }
        //  $generatedFiles = glob($pattern);

        return $outputFile . '.' . $extension;
    }
}



