<?php

session_start(); // Inicia la sesión

require_once 'PdfConverterController.php';

$controller = new PdfConverterController();
$uniqueId = $_GET['uniqueId'] ?? '';
$controller->download($uniqueId);