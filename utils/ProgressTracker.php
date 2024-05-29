<?php
namespace Utils;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class ProgressTracker {
    public function setTotalSteps(int $totalSteps, string $uniqueId): void {
        $_SESSION[$uniqueId . '_progress'] = ['totalSteps' => $totalSteps, 'currentStep' => 0];
        error_log("Progreso inicializado: Total Steps - $totalSteps para uniqueId: $uniqueId");
        session_write_close(); // Esto debería estar bien aquí.
    }

    public function incrementStep(string $uniqueId): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION[$uniqueId . '_progress'])) {
            $_SESSION[$uniqueId . '_progress']['currentStep']++;
            error_log("Current step incrementado: " . $_SESSION[$uniqueId . '_progress']['currentStep']); // Log para verificar el incremento
        }
        session_write_close(); // Esto debería estar bien aquí.
    }

    public function reset(string $uniqueId): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[$uniqueId . '_progress']);
        error_log("Progreso reseteado para uniqueId: $uniqueId");
        session_write_close();
    }
}

