<?php


session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('',1);

class PdfMetaModifierModel {

    public function getMetaData($filePath): bool|string|null
    {
        $command = "pdftk " . escapeshellarg($filePath) . " dump_data";
        return shell_exec($command);
    }

    public function updateMetaData($filePath, $newMetaData): string
    {
        $metaDataFile = tempnam(sys_get_temp_dir(), 'meta');
        file_put_contents($metaDataFile, $newMetaData);

        $newFilePath = $filePath . '_modified.pdf';
        $command = "pdftk " . escapeshellarg($filePath) . " update_info " . escapeshellarg($metaDataFile) . " output " . escapeshellarg($newFilePath);
        shell_exec($command);

        return $newFilePath;
    }
}