<?php

namespace Utils\Clean;

/**
 * Clase CleanupOldTemps
 *
 * Esta clase se encarga de limpiar los directorios temporales antiguos que han estado inactivos por un período de tiempo específico.
 */
class CleanupOldTemps
{
    private string $directory;
    private int $maxAge;

    /**
     * Constructor de la clase CleanupOldTemps.
     *
     * @param string $directory El directorio en el que se encuentran los archivos temporales.
     * @param int $maxAge La edad máxima en segundos que puede tener un archivo antes de ser considerado para la limpieza.
     */
    public function __construct(string $directory, int $maxAge)
    {
        $this->directory = $directory;
        $this->maxAge = $maxAge;
    }

    /**
     * Limpia los archivos y directorios temporales antiguos.
     *
     * Recorre el directorio especificado y elimina los archivos y directorios que tienen una antigüedad mayor a la especificada.
     *
     * @return void
     */
    public function clean(): void
    {
        $now = time();
        $files = glob($this->directory . '/*');

        foreach ($files as $file) {
            if (is_dir($file)) {
                $fileAge = $now - filemtime($file);
                if ($fileAge > $this->maxAge) {
                    $this->deleteDirectory($file);
                }
            }
        }
    }

    /**
     * Elimina un directorio y su contenido.
     *
     * Recorre recursivamente el directorio y elimina todos los archivos y subdirectorios en su interior antes de eliminar el propio directorio.
     *
     * @param string $dir El directorio a eliminar.
     * @return void
     */
    private function deleteDirectory(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}

// Ejemplo de uso
$cleanup = new CleanupOldTemps('/var/tmp/pdfmax2_temps', 86400); // 24 horas
$cleanup->clean();
