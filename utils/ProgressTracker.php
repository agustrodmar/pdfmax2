<?php

class ProgressTracker {
    private int $totalSteps = 0;
    private int $currentStep = 0;

    public function setTotalSteps($totalSteps): void
    {
        $this->totalSteps = $totalSteps;
    }

    public function incrementStep(): void
    {
        $this->currentStep++;
        $this->writeProgressToFile();
    }

    public function reset(): void
    {
        $this->currentStep = 0;
        $this->totalSteps = 0;  // Asegúrate de que también totalSteps sea restablecido si necesario
        $this->writeProgressToFile();  // Asegura que el progreso reiniciado se guarde en el archivo
    }

    private function writeProgressToFile(): void
    {
        $progress = [
            'totalSteps' => $this->totalSteps,
            'currentStep' => $this->currentStep
        ];
        echo json_encode($progress);  // Temporal para depuración
        file_put_contents('progress.json', json_encode($progress));
    }
}