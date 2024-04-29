<?php
/**
 * Clase que representa un extractor de páginas de PDF.
 */
class PDFExtractorModel {

    /**
     * Extrae páginas específicas de un PDF y las guarda en un nuevo archivo.
     *
     * @param string $pdfPath Ruta del archivo PDF original.
     * @param string $paginas Páginas a extraer, como cadena.
     * @param string $outputPath Ruta del nuevo archivo PDF.
     * @return string Devuelve cualquier salida del comando como diagnóstico.
     * @throws Exception Si no se puede crear el archivo de salida.
     */
    public function extraerPaginas(string $pdfPath, string $paginas, string $outputPath): string {
        set_time_limit(500);
        $comando = "pdftk $pdfPath cat $paginas output $outputPath 2>&1";
        $salida = shell_exec($comando);

        if (!file_exists($outputPath)) {
            throw new Exception("Error: No se pudo crear el archivo de salida.");
        }

        return $salida ?? 'Proceso completado sin salida del sistema.';
    }

    /**
     * Extrae páginas individuales de un PDF y las guarda en archivos separados.
     *
     * @param string $pdfPath Ruta del archivo PDF original.
     * @param string $paginas Páginas a extraer, pueden incluir rangos.
     * @param string $outputFilesBase Prefijo para las rutas de los archivos de salida.
     * @return array Lista de rutas de los archivos de salida creados.
     * @throws Exception Si no se puede crear alguno de los archivos de salida.
     */
    public function extraerPaginasIndividuales(string $pdfPath, string $paginas, string $outputFilesBase): array {
        $paginasArray = preg_split('/[\s,]+/', $paginas);
        $outputPaths = [];

        foreach ($paginasArray as $pagina) {
            if (str_contains($pagina, '-')) {
                list($start, $end) = explode('-', $pagina);
                for ($i = $start; $i <= $end; $i++) {
                    $outputPath = $outputFilesBase . "_pagina_" . $i . '.pdf';
                    $comando = "pdftk $pdfPath cat $i output $outputPath 2>&1";
                    $salida = shell_exec($comando);

                    if (!file_exists($outputPath)) {
                        throw new Exception("Error: No se pudo crear el archivo de salida para la página $i.");
                    }
                    $outputPaths[] = $outputPath;
                }
            } else {
                $outputPath = $outputFilesBase . "_pagina_" . $pagina . '.pdf';
                $comando = "pdftk $pdfPath cat $pagina output $outputPath 2>&1";
                $salida = shell_exec($comando);

                if (!file_exists($outputPath)) {
                    throw new Exception("Error: No se pudo crear el archivo de salida para la página $pagina.");
                }
                $outputPaths[] = $outputPath;
            }
        }

        return $outputPaths;
    }

}