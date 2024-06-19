<?php

class PdfSplitModel
{
    /**
     * Divide un archivo PDF en múltiples documentos según los rangos especificados.
     *
     * @param string $inputPdf Ruta del archivo PDF de entrada.
     * @param string $outputDir Directorio de salida para los archivos divididos.
     * @param array $ranges Array de rangos de páginas.
     * @return array Rutas de los archivos PDF generados.
     * @throws Exception Si ocurre un error durante la división.
     */
    public function splitPdfByRanges(string $inputPdf, string $outputDir, array $ranges): array
    {
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $outputPaths = [];
        $docIndex = 1;

        foreach ($ranges as $range) {
            $startPage = intval($range['start']);
            $endPage = intval($range['end']);

            if ($startPage > $endPage) {
                throw new Exception("El inicio del rango no puede ser mayor que el final del rango.");
            }

            $outputPdfPath = $outputDir . "document_" . $docIndex . ".pdf";
            $command = "pdftk $inputPdf cat $startPage-$endPage output $outputPdfPath";
            $output = [];
            $return_var = null;
            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                error_log("Error en pdftk: " . implode("\n", $output));
                throw new Exception("Error al dividir el PDF.");
            }

            $outputPaths[] = $outputPdfPath;
            $docIndex++;
        }

        return $outputPaths;
    }
}
