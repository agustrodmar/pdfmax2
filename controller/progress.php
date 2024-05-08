<?php
session_start();

header('Content-Type: application/json');
echo json_encode($_SESSION['progress'] ?? ['currentStep' => 0, 'totalSteps' => 0]);
