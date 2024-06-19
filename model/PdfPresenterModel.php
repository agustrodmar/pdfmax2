<?php

/**
 * Clase PdfPresenterModel
 * Esta clase maneja la presentación de archivos PDF.
 */
class PdfPresenterModel
{
    /**
     * Verifica la existencia y validez del archivo PDF.
     *
     * @param string $pdfFile Ruta del archivo PDF a presentar.
     * @return bool Retorna true si el archivo PDF existe y es válido, false en caso contrario.
     */
    public function presentPdf(string $pdfFile): bool
    {
        // Verificar que el archivo existe y es un PDF
        if (file_exists($pdfFile) && mime_content_type($pdfFile) === 'application/pdf') {
            return true;
        }
        return false;
    }
}
