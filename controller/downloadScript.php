<?php
// downloadScript.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Inicia la sesión

require_once '../controller/PdfConverterController.php';

$controller = new PdfConverterController();
$uniqueId = $_GET['uniqueId'] ?? '';
$controller->download($uniqueId);
