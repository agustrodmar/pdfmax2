<?php

/**
 * Modelo para la optimización de archivos PDF.
 */
class PdfOptimizerModel {
    /**
     * Optimiza un archivo PDF utilizando Ghostscript.
     *
     * @param string $inputFile Ruta del archivo PDF a optimizar.
     * @param string $outputFile Ruta del archivo PDF optimizado.
     * @return bool Verdadero si la optimización fue exitosa, falso en caso contrario.
     * @throws Exception Si hay un error durante la optimización.
     */
    public static function optimizePdf(string $inputFile, string $outputFile): bool {
        // Ruta completa al ejecutable de Ghostscript
        $gsPath = '/usr/bin/gs';

        // Crear el comando para Ghostscript
        $quality = $_POST['quality'];
        $command = "$gsPath -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/$quality -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile $inputFile";

        // Ejecutar el comando
        exec($command, $output, $returnVar);

        // Verificar si la optimización fue exitosa
        if ($returnVar === 0) {
            return true; // Optimización exitosa
        } else {
            throw new Exception("Error al ejecutar Ghostscript. Código de retorno: $returnVar");
        }
    }
}
