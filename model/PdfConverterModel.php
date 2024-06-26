<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    public function convertPdf(string $inputFile, string $outputFile, string $format, string $pages = null): string {
        // Define la extensión del archivo de salida basada en el formato
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $pageOption = "";
        if ($pages) {
            $pageOption = "-f $pages -l $pages ";
        }

        // Construye el comando para ejecutar pdftocairo
        $command = "pdftocairo -" . $format . " " . $pageOption . "-r 300 " . escapeshellarg($inputFile) .
            " " . escapeshellarg($outputFile) . "." . $extension;
        $output = shell_exec($command . " 2>&1");

        return $outputFile . '.' . $extension;
    }

    /**
     * Comprueba si el PDF está encriptado o dañado.
     *
     * @param string $inputFile Ruta al archivo PDF de entrada.
     * @return bool Verdadero si el archivo está encriptado o dañado, falso en caso contrario.
     */
    public function isPdfEncryptedOrDamaged(string $inputFile): bool {
        $output = shell_exec("pdfinfo " . escapeshellarg($inputFile) . " 2>&1");
        if (str_contains($output, "Encrypted") || str_contains($output, "error")) {
            return true;
        }
        return false;
    }
}
