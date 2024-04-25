
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Modelo para convertir archivos PDF a diferentes formatos.
 */
class PdfConverterModel {
    /**
     * Convierte un archivo PDF a un formato específico.
     *
     * @param string $inputFile Ruta al archivo PDF de entrada.
     * @param string $outputFile Ruta base para los archivos de salida.
     * @param string $format Formato de salida deseado.
     * @param string|null $pages Rango de páginas a convertir.
     * @return string Ruta al archivo de salida con la extensión correcta.
     * @throws Exception Si la conversión falla o si hay problemas con el comando ejecutado.
     */
    public function convertPdf(string $inputFile, string $outputFile, string $format, string $pages = null): string {
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $pageOption = "";
        if ($pages) {
            $pageOption = "-f $pages -l $pages ";
        }

        $command = "pdftocairo -" . $format . " " . $pageOption . "-r 300 " . escapeshellarg($inputFile) . " " . escapeshellarg($outputFile) . "." . $extension;
        $output = shell_exec($command . " 2>&1");
        error_log("Ejecución del comando: $command");
        error_log("Salida del comando: $output");

        $pattern = $outputFile . '*.' . $extension;
        if ($pages) {
            $pattern .= "-" . $pages;
        }
        $generatedFiles = glob($pattern);
        if (!$generatedFiles) {
            throw new Exception("No se encontraron archivos generados, posible fallo en la conversión.");
        }

        return $outputFile . '.' . $extension;
    }
}



