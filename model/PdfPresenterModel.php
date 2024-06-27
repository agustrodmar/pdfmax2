<?php

class PdfPresenterModel
{
    private string $tempDir = '/var/tmp/pdfmax2_temps/';

    /**
     * Guarda temporalmente un archivo PDF.
     *
     * @param string $file Ruta temporal del archivo PDF subido.
     * @return string Ruta del archivo PDF guardado temporalmente.
     * @throws Exception Si no se puede guardar el archivo.
     */
    public function savePdfTemporarily(string $file): string
    {
        $outputDir = $this->tempDir;
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pdfPath = $outputDir . uniqid('pdf_present_', true) . '.pdf';

        if (!move_uploaded_file($file, $pdfPath)) {
            throw new Exception("Error al mover el archivo subido.");
        }

        return $pdfPath;
    }
}
