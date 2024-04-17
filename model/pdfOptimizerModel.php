<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class pdfOptimizerModel {
    public static function optimizePDF($inputFile, $outputFile, $quality): bool {
        // Agrego la ruta  /usr/bin a la variable de entorno PATH
        putenv("PATH=/usr/bin:" . getenv("PATH"));

        // Ruta completa al ejecutable Ghostscript
        $gsPath = '/usr/bin/gs';

        // Comando de Ghostscript para optimizar el PDF
        $command = "$gsPath -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/$quality -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile $inputFile";

        // ahora se ejectuta el comando de Ghostscript
        exec($command, $output, $returnVar);

        // if exitosa...
        if ($returnVar === 0) {
            return true; // Optimización exitosa
        } else {
            echo "Error al ejecutar Ghostscript. Código de retorno: $returnVar";
            return false; // Error en la optimización
        }
    }
}
