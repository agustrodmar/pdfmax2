<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class pdfToTextModel {
    public function convertToText($file): bool|string|null
    {
        $command = "pdftotext " . escapeshellarg($file) . " -";
        return shell_exec($command);
    }

    public function convertToOdt($file): string
    {
        $outputDir = sys_get_temp_dir();
        $htmlFile = tempnam($outputDir, 'output') . '.html';
        $odtFile = tempnam($outputDir, 'output') . '.odt';

        // Convert PDF to HTML first
        $command = "pdftohtml -stdout " . escapeshellarg($file) . " > " . escapeshellarg($htmlFile);
        shell_exec($command);

        // Then convert HTML to ODT with Pandoc
        $command = "pandoc -s " . escapeshellarg($htmlFile) . " -o " . escapeshellarg($odtFile);
        shell_exec($command);

        if (!file_exists($odtFile) || filesize($odtFile) === 0) {
            return "Error generating ODT file.";
        }

        $odtContent = file_get_contents($odtFile);
        unlink($htmlFile);  // Clean up the temporary HTML file
        unlink($odtFile);   // Clean up the temporary ODT file
        return $odtContent;
    }
}


