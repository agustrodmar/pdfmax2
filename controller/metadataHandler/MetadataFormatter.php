<?php


class MetadataFormatter
{
    public static function formatPdfDate($dateString)
    {
        if (preg_match('/D:(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $dateString, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
            $hour = $matches[4];
            $minute = $matches[5];
            $second = $matches[6];
            return "$day/$month/$year $hour:$minute:$second";
        }
        return $dateString; // Retorna el original si no coincide con el patrón esperado
    }

    public static function getFriendlyMetadata($metadata): array
    {
        $friendlyMetadata = [];
        $lines = explode("\n", $metadata);
        $nextValue = false;
        $currentKey = '';

        foreach ($lines as $line) {
            if (str_starts_with($line, 'InfoKey:')) {
                $currentKey = trim(str_replace('InfoKey:', '', $line));
                $nextValue = true; // El próximo valor debería ser el valor asociado a esta clave
            } elseif ($nextValue) {
                $value = trim(str_replace('InfoValue:', '', $line));
                switch ($currentKey) {
                    case 'Author':
                    case 'Title':
                    case 'Subject':
                    case 'Keywords':
                        $friendlyMetadata[$currentKey] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        break;
                    case 'ModDate':
                    case 'CreationDate':
                        $friendlyMetadata[$currentKey] = self::formatPdfDate($value);
                        break;
                    default:
                        $friendlyMetadata[$currentKey] = $value;
                }
                $nextValue = false; // Restablece para el próximo ciclo
            }
        }
        return $friendlyMetadata;
    }
}