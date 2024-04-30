<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * Trait para enviar archivos PDF al cliente como descarga
 * para la clases de TextToPdf.
 */
trait PdfResponseSender {
    /**
     * Envía un archivo PDF al cliente como una descarga y elimina el archivo temporal.
     *
     * @param string $outputPath Ruta del archivo PDF a enviar.
     * @throws Exception Si el archivo no existe o no se puede leer antes de enviar.
     */
    #[NoReturn]
    protected function sendPdfToClient(string $outputPath): void {
        if (!file_exists($outputPath) || !is_readable($outputPath)) {
            throw new Exception("El archivo PDF no existe o no se puede leer.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($outputPath));
        readfile($outputPath);
        unlink($outputPath);
        exit;
    }
}
