<?php
/**
 * Clase que representa un extractor de páginas de PDF.
 */
class pdfExtractorModel {
    /**
     * Método para extraer páginas de un PDF.
     *
     * @param string $pdfPath Ruta del PDF.
     * @param string $paginas Las páginas a extraer.
     * @param string $outputPath Ruta donde tiene que guardarse el nuevo archivo PDF.
     * @return string Mensaje de resultado de la operación.
     * @throws Exception Si no se pudo crear el archivo de salida.
     */
    public function extraerPaginas(string $pdfPath, string $paginas, string $outputPath): string {
        $comando = "pdftk $pdfPath cat $paginas output $outputPath 2>&1";
        $salida = shell_exec($comando);
        echo "Salida y errores: " . $salida . "<br>";

        if (!file_exists($outputPath)) {
            throw new Exception("Error: No se pudo crear el archivo de salida.");
        }

        return $salida ?? '';
    }
}