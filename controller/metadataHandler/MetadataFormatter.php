<?php

class MetadataFormatter
{
    /**
     * Formatea una cadena de fecha de PDF a un formato de fecha y hora más legible.
     *
     * @param string $dateString Cadena de fecha en formato específico de PDF.
     * @return string Fecha formateada o la cadena original si no cumple con el formato esperado.
     */
    public static function formatPdfDate(string $dateString): string
    {
        if (preg_match('/D:(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $dateString, $matches)) {
            return sprintf("%02d/%02d/%04d %02d:%02d:%02d", $matches[3], $matches[2], $matches[1], $matches[4], $matches[5], $matches[6]);
        }
        return $dateString;
    }

    /**
     * Convierte los metadatos crudos en un formato más amigable y utilizable.
     *
     * @param string $metadata Metadatos en formato de texto crudo.
     * @param int $numPages Número de páginas del PDF.
     * @return array Metadatos formateados como un arreglo asociativo.
     */
    public static function getFriendlyMetadata(string $metadata, int $numPages): array
    {
        $friendlyMetadata = [];
        $lines = explode("\n", $metadata);
        $nextValue = false;
        $currentKey = '';

        foreach ($lines as $line) {
            if (str_starts_with($line, 'InfoKey:')) {
                $currentKey = trim(str_replace('InfoKey:', '', $line));
                $nextValue = true;
            } elseif ($nextValue) {
                $value = trim(str_replace('InfoValue:', '', $line));
                $translatedKey = match ($currentKey) {
                    'Author' => 'Autor',
                    'Title' => 'Título',
                    'Subject' => 'Tema',
                    'Keywords' => 'Palabras Clave',
                    'ModDate' => 'Fecha de Modificación',
                    'CreationDate' => 'Fecha de Creación',
                    'Producer' => 'Productor',
                    'Creator' => 'Creador',
                    default => $currentKey,
                };
                $friendlyMetadata[$translatedKey] = match ($currentKey) {
                    'ModDate', 'CreationDate' => self::formatPdfDate($value),
                    default => html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                };
                $nextValue = false;
            }
        }

        // Se asegura de que siempre se enseñen los campos:
        $friendlyMetadata['Número de Páginas'] = $numPages;
        $friendlyMetadata['Tema'] = $friendlyMetadata['Tema'] ?? '';
        $friendlyMetadata['Palabras Clave'] = $friendlyMetadata['Palabras Clave'] ?? '';
        $friendlyMetadata['Título'] = $friendlyMetadata['Título'] ?? '';

        return $friendlyMetadata;
    }
}
