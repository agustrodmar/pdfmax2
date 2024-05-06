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

    private function writeProgressToFile(): void
    {
        $progress = [
            'totalSteps' => $this->totalSteps,
            'currentStep' => $this->currentStep
        ];
        file_put_contents('progress.json', json_encode($progress));
    }
}
