<?php

class ProgressTracker {
    /**
     * Establece el número total de pasos en una sesión específica.
     * @param int $totalSteps Total de pasos a configurar.
     * @param string $uniqueId Identificador único para la sesión actual.
     */
    public function setTotalSteps(int $totalSteps, string $uniqueId): void
    {
        $_SESSION[$uniqueId . '_progress'] = ['totalSteps' => $totalSteps, 'currentStep' => 0];
    }

    public function incrementStep($uniqueId): void
    {
        if (isset($_SESSION[$uniqueId . '_progress'])) {
            $_SESSION[$uniqueId . '_progress']['currentStep']++;
        }
    }

    /**
     * Resetea los valores de progreso para una sesión dada.
     * @param string $uniqueId Identificador único para la sesión actual.
     */
    public function reset(string $uniqueId): void
    {
        $_SESSION[$uniqueId . '_currentStep'] = 0;
        $_SESSION[$uniqueId . '_totalSteps'] = 0;
    }
}
