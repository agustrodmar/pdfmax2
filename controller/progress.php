<?php

session_start(); // Inicia la sesión

$uniqueId = $_GET['uniqueId'] ?? '';
$progress = $_SESSION[$uniqueId . '_progress'] ?? ['totalSteps' => 0, 'currentStep' => 0];

header('Content-Type: application/json');
echo json_encode($progress);

