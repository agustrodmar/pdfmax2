<?php

class ProgressTracker {
    private int $totalSteps = 0;
    private int $currentStep = 0;
    private string $operationId;

    public function setOperationId($operationId): void
    {
        $this->operationId = $operationId;
        error_log("Operation ID establecido: $operationId");
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

    public function setTotalSteps($totalSteps): void
    {
        $this->totalSteps = $totalSteps;
        error_log("Total de pasos establecido: $totalSteps");
    }

    public function incrementStep(): void
    {
        $this->currentStep++;
        error_log("Paso incrementado: $this->currentStep de $this->totalSteps");
        $this->writeProgressToFile();
    }

    public function reset(): void
    {
        $this->totalSteps = 0;
        $this->currentStep = 0;
        error_log("ProgressTracker reiniciado.");
        $this->writeProgressToFile();
    }

    private function writeProgressToFile(): void
    {
        $progress = [
            'totalSteps' => $this->totalSteps,
            'currentStep' => $this->currentStep
        ];
        $filePath = __DIR__ . '/../tmps/' . $this->operationId . '_progress.json';
        error_log("Escribiendo progreso en $filePath");
        file_put_contents($filePath, json_encode($progress));
    }
}
