
<?php


ini_set('display_errors', 1);
error_reporting(E_ALL);


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
     * @param string|null $page
     * @return string Ruta al archivo de salida con la extensión correcta.
     */
    public function convertPdf(string $inputFile, string $outputFile, string $format, string $page = null): string {
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $pageOption = "";
        if ($page) {
            $pageOption = "-f $page -l $page ";
        }

        $command = "pdftocairo -" . $format . " " . $pageOption . "-r 300 " . $inputFile . " " . $outputFile . "." . $extension;

        error_log("Ejecutando comando: $command");

        $output = shell_exec($command . " 2>&1");
        error_log("Salida del comando: $output");

        $files = glob($outputFile . '*.' . $extension);
        if (!$files) {
            error_log("No se crearon archivos para la ruta base: $outputFile y extensión: $extension");
        } else {
            foreach ($files as $file) {
                error_log("Archivo creado: $file");
            }
        }

        return $outputFile . '.' . $extension;
    }
}