
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PdfConverterModel {
    public function convertPdf($inputFile, $outputFile, $format, $pages = null): string
    {
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $pageOption = "";
        if ($pages) {
            $pageOption = "-f $pages -l $pages "; // Añade el rango de páginas al comando si se especifica
        }

        $command = "pdftocairo -" . $format . " " . $pageOption . "-r 300 " . escapeshellarg($inputFile) . " " . escapeshellarg($outputFile) . "." . $extension;
        $output = shell_exec($command . " 2>&1"); // Captura la salida del comando, incluyendo errores
        error_log("Ejecución del comando: $command");
        error_log("Salida del comando: $output");

        // Revisión de archivos generados
        $pattern = $outputFile . '*.' . $extension;
        if ($pages) {
            $pattern .= "-" . $pages; // Ajusta el patrón de búsqueda si se especifican páginas
        }
        $generatedFiles = glob($pattern);
        if ($generatedFiles) {
            foreach ($generatedFiles as $file) {
                error_log("Archivo generado: $file, tamaño: " . filesize($file));
            }
        } else {
            error_log("No se encontraron archivos generados con la extensión $extension o página especificada.");
        }

        return $outputFile . '.' . $extension; // Asegúrate de que la extensión es correcta
    }
}



