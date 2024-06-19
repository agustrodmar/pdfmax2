<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class PdfMetaModifierModel {

    public function getMetaData($filePath): bool|string|null
    {
        $command = "pdftk " . escapeshellarg($filePath) . " dump_data";
        return shell_exec($command);
    }

}