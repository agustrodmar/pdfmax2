<?php

class ProgressTracker {
    public function setTotalSteps($totalSteps): void
    {
        $_SESSION['totalSteps'] = $totalSteps;
    }

    public function incrementStep(): void
    {
        if (!isset($_SESSION['currentStep'])) {
            $_SESSION['currentStep'] = 0;
        }
        $_SESSION['currentStep']++;
        $this->writeProgressToSession();
    }

    private function writeProgressToSession(): void
    {
        $_SESSION['progress'] = [
            'totalSteps' => $_SESSION['totalSteps'],
            'currentStep' => $_SESSION['currentStep']
        ];
    }

    public function reset(): void
    {
        $_SESSION['currentStep'] = 0;
        $_SESSION['totalSteps'] = 0;
        $this->writeProgressToSession();
    }
}