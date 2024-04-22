<?php

class pdfModifierModel {
    public function __construct() {
        // Constructor
    }

    /**
     * Obtiene los metadatos de un archivo PDF.
     *
     * @param string $pdfPath La ruta del archivo PDF.
     * @return array Los metadatos del PDF.
     * @throws Exception
     */
    public function getMetadata(string $pdfPath): array {
        exec("pdftk $pdfPath dump_data_utf8 2>&1", $output, $return_var);
        if ($return_var != 0) {
            throw new Exception("Error al ejecutar el comando pdftk: " . implode("\n", $output));
        }

        $outputString = implode("\n", $output);
        return json_decode($outputString, true) ?: [];
    }


    /**
     * Actualiza los metadatos de un archivo PDF.
     *
     * @param string $pdfPath La ruta del archivo PDF.
     * @param array $metadata Los nuevos metadatos a actualizar.
     * @return bool True si la actualización fue exitosa, False en caso contrario.
     */
    public function updateMetadata(string $pdfPath, array $metadata): bool {
        $metadataString = '';
        foreach ($metadata as $key => $value) {
            $metadataString .= "$key: " . escapeshellarg($value) . " ";
        }

        // actualizar los metadatos con pdftk
        exec("pdftk $pdfPath update_info_utf8 \"$metadataString\" output updated.pdf", $output, $return_var);

        // miro si hay errores
        if ($return_var != 0) {
            return false; // Actualización fallida
        }

        // se le cambia el nombre al archivo actualizado
        rename('updated.pdf', $pdfPath);
        return true; // Actualización exitosa
    }
}
