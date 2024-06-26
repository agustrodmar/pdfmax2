<?php

/**
 * Clase PdfPageCounter
 *
 * Utilidad para contar el número de páginas en un archivo PDF usando pdfinfo.
 */
class PdfPageCounter
{
    /**
     * Cuenta el número de páginas en un archivo PDF.
     *
     * @param string $pdfFilePath Ruta del archivo PDF.
     * @return int Número de páginas en el PDF.
     * @throws Exception Si el archivo PDF no se puede abrir o si no se puede determinar el número de páginas.
     */
    public function countPages(string $pdfFilePath): int
    {
        if (!file_exists($pdfFilePath)) {
            throw new Exception('Archivo PDF no encontrado.');
        }

        $command = escapeshellcmd("pdfinfo " . escapeshellarg($pdfFilePath));
        $output = shell_exec($command);

        if (!$output) {
            throw new Exception('Error al ejecutar pdfinfo.');
        }

        if (preg_match('/Pages:\s+(\d+)/i', $output, $matches)) {
            return (int)$matches[1];
        } else {
            throw new Exception('No se pudo determinar el número de páginas.');
        }
    }
}
