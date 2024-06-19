<?php

/**
 * Clase para encriptar un archivo PDF utilizando qpdf.
 */
class PdfEncrypterModel
{
    /**
     * Encripta un archivo PDF utilizando qpdf.
     *
     * @param string $inputPdf Ruta del archivo PDF de entrada.
     * @param string $outputPdf Ruta del archivo PDF de salida.
     * @param string $password Contraseña para la encriptación.
     * @return bool True si la encriptación fue exitosa, false en caso contrario.
     */
    public function encryptPdf(string $inputPdf, string $outputPdf, string $password): bool
    {
        // Limpiar y asegurar las rutas para el comando
        $inputPdf = escapeshellarg($inputPdf);
        $outputPdf = escapeshellarg($outputPdf);
        $password = escapeshellarg($password);

        $command = "qpdf --encrypt $password $password 256 -- $inputPdf $outputPdf";
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Registrar información de depuración
        error_log("Comando ejecutado: $command");
        error_log("Salida de qpdf: " . implode("\n", $output));
        error_log("Valor de retorno de qpdf: $return_var");

        if ($return_var !== 0) {
            error_log("Error en qpdf: " . implode("\n", $output));
        }

        return $return_var === 0;
    }
}
