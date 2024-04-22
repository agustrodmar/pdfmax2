<?php
ini_set('display_errors', 1);
ini_set('log_errors', '1');
ini_set('error_reporting', E_ALL);

class PdfOptimizerModel {
    public static function optimizePdf($inputFile, $outputFile): bool {
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
            echo "Error al ejecutar Ghostscript. Código de retorno: $returnVar";
            return false; // Error en la optimización
        }
    }
}