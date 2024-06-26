<?php

namespace utils\clean;

class TempCleaner
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Elimina todos los archivos en el directorio temporal.
     */
    public function clean(): void {
        $this->cleanDirectory($this->directory);
        if (!file_exists($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }


    /**
     * Elimina todos los archivos y subdirectorios dentro de un directorio dado.
     *
     * @param string $dir El directorio a limpiar.
     */
    private function cleanDirectory(string $dir): void
    {
        $files = glob($dir . '/*'); // Obtiene todos los archivos en el subdirectorio

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Elimina el archivo
            } elseif (is_dir($file)) {
                $this->cleanDirectory($file); // Limpia el subdirectorio recursivamente
                rmdir($file); // Elimina el subdirectorio
            }
        }
        rmdir($dir); // Elimina el directorio después de limpiar su contenido
    }
}
