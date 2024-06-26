<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * Trait para enviar archivos al cliente como descarga.
 */
trait PdfResponseSender {
    /**
     * Envía un archivo PDF al cliente como una descarga.
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
        exit;
    }

    /**
     * Envía un archivo de texto al cliente como una descarga.
     *
     * @param string $content Contenido del archivo de texto a enviar.
     * @param string $filename Nombre del archivo de texto.
     */
    #[NoReturn]
    protected function sendTextToClient(string $content, string $filename): void {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }

    /**
     * Envía un archivo ODT al cliente como una descarga.
     *
     * @param string $outputPath Ruta del archivo ODT a enviar.
     * @throws Exception Si el archivo no existe o no se puede leer antes de enviar.
     */
    #[NoReturn]
    protected function sendOdtToClient(string $outputPath): void {
        if (!file_exists($outputPath) || !is_readable($outputPath)) {
            throw new Exception("El archivo ODT no existe o no se puede leer.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.oasis.opendocument.text');
        header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($outputPath));
        readfile($outputPath);
        exit;
    }
}
