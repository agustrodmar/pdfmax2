
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PdfConverterModel {
    public function convertPdf($inputFile, $outputFile, $format): string
    {
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $command = "pdftocairo -" . $format . " -r 300 " . escapeshellarg($inputFile) . " " . escapeshellarg($outputFile) . "." . $extension;
        $output = shell_exec($command . " 2>&1"); // Captura la salida del comando, incluyendo errores
        error_log("Ejecución del comando: $command");
        error_log("Salida del comando: $output");

        // Revisión de archivos generados
        $generatedFiles = glob($outputFile . '*.' . $extension);
        if ($generatedFiles) {
            foreach ($generatedFiles as $file) {
                error_log("Archivo generado: $file, tamaño: " . filesize($file));
            }
        } else {
            error_log("No se encontraron archivos generados con la extensión $extension.");
        }

        return $outputFile . '.' . $extension; // Asegúrate de que la extensión es correcta
    }
}



