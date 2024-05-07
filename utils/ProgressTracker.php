<?php

class ProgressTracker {
    private int $totalSteps = 0;
    private int $currentStep = 0;

    private string $operationId;


    public function setOperationId($operationId): void
    {
        $this->operationId = $operationId;
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

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
        $this->totalSteps = 0;
        $this->currentStep = 0;
        $this->writeProgressToFile();
    }

    private function writeProgressToFile(): void
    {
        $progress = [
            'totalSteps' => $this->totalSteps,
            'currentStep' => $this->currentStep
        ];
        $filePath = __DIR__ . '/../tmps/' . $this->operationId . '_progress.json';
        file_put_contents($filePath, json_encode($progress));
    }
}
