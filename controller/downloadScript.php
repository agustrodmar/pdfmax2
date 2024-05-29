<?php

session_start(); // Inicia la sesión

require_once 'PdfConverterController.php';

$controller = new PdfConverterController();
$controller->download();