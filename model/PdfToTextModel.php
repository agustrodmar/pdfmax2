<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class pdfToTextModel {
    public function convertToText($file) {
        $command = "pdftotext '$file' -";
        return shell_exec($command);
    }

    public function convertToOdt($file) {
        $htmlFile = tempnam(sys_get_temp_dir(), 'html');
        $odtFile = tempnam(sys_get_temp_dir(), 'odt');
        shell_exec("pdftohtml '$file' '$htmlFile'");
        shell_exec("pandoc '$htmlFile' -o '$odtFile'");
        return file_get_contents($odtFile);
    }
}


