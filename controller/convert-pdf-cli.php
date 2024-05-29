<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

use Utils\ProgressTracker;

require_once __DIR__ . '/../utils/ProgressTracker.php';
require_once __DIR__ . '/../model/PdfConverterModel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$inputFile = $argv[1];
$outputBase = $argv[2];
$format = $argv[3];
$page = $argv[4];
$uniqueId = $argv[5];

$model = new PdfConverterModel();
$tracker = new ProgressTracker();

error_log("convert-pdf-cli.php iniciado con argumentos: " . print_r($argv, true));

error_log("Iniciando la conversión de PDF a imagen para la página $page...");
$outputFile = $model->convertPdf($inputFile, $outputBase, $format, $page);
error_log("Conversión completada para la página $page, archivo de salida: $outputFile");

$tracker->incrementStep($uniqueId); // Actualizar el progreso

$files = glob($outputBase . '*.' . ($format === 'jpeg' ? 'jpg' : $format));
if (!$files) {
    error_log("No se crearon archivos para la ruta base: $outputBase");
} else {
    foreach ($files as $file) {
        error_log("Archivo creado: $file");
    }
}
