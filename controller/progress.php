<?php
session_start();
header('Content-Type: application/json');

$uniqueId = $_GET['uniqueId'] ?? '';
// Accediendo a la estructura correcta basada en uniqueId
$progress = $_SESSION[$uniqueId . '_progress'] ?? ['currentStep' => 0, 'totalSteps' => 0];
echo json_encode($progress);
