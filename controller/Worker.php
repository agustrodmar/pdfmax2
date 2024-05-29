<?php
namespace Controller;

use Exception;
use Predis\Client as PredisClient;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Utils\ProgressTracker;
use Utils\Zipper;

require '/var/www/html/pdfmax2/vendor/autoload.php';
require '/var/www/html/pdfmax2/utils/ProgressTracker.php';
require '/var/www/html/pdfmax2/utils/Zipper.php';

class Worker {
    public function run(): void {
        error_log("Worker iniciado.");

        try {
            $redis = new PredisClient([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            error_log("Conectado a Redis.");
        } catch (Exception $e) {
            error_log("No se pudo conectar a Redis: " . $e->getMessage());
            return;
        }

        while (true) {
            try {
                $jobData = $redis->rpop('pdf_conversion_queue');
                if ($jobData) {
                    error_log("Trabajo recibido de la cola: " . $jobData);
                    $job = json_decode($jobData, true);
                    error_log("Trabajo decodificado: " . print_r($job, true));

                    $tracker = new ProgressTracker();
                    $zipper = new Zipper();

                    $inputFile = $job['inputFile'];
                    $outputBase = $job['outputBase'];
                    $format = $job['format'];
                    $page = $job['page'];
                    $uniqueId = $job['uniqueId'];

                    error_log("Verificando existencia del archivo de entrada y directorio de salida...");
                    if (!file_exists($inputFile)) {
                        error_log("Archivo de entrada no existe: " . $inputFile);
                        continue; // Saltar este trabajo y continuar con el siguiente
                    }

                    if (!is_dir(dirname($outputBase))) {
                        error_log("Directorio de salida no existe, creando: " . dirname($outputBase));
                        mkdir(dirname($outputBase), 0777, true);
                    }

                    error_log("Iniciando proceso para la página: " . $page);
                    $process = new Process([
                        'php',
                        '/var/www/html/pdfmax2/controller/convert-pdf-cli.php',
                        $inputFile,
                        $outputBase,
                        $format,
                        $page,
                        $uniqueId
                    ], null, null, null, 300); // Set timeout to 300 seconds
                    error_log("Ejecutando proceso de conversión con argumentos: " . $process->getCommandLine());
                    $process->run();

                    if (!$process->isSuccessful()) {
                        error_log("Error en la ejecución del proceso: " . $process->getErrorOutput());
                        throw new ProcessFailedException($process);
                    } else {
                        error_log("Proceso de conversión completado para la página: " . $page);
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        $tracker->incrementStep($uniqueId);
                        error_log("Progreso actualizado para uniqueId: " . $uniqueId);
                    }

                    // Verificar si el archivo fue creado
                    $extension = $format === 'jpeg' ? 'jpg' : $format;
                    $outputFilePattern = $outputBase . '-' . $page . '.' . $extension;
                    if (file_exists($outputFilePattern)) {
                        error_log("Archivo creado: " . $outputFilePattern);
                    } else {
                        error_log("No se encontró el archivo esperado: " . $outputFilePattern);
                    }

                    // Verificar si todas las páginas han sido procesadas
                    $progress = $_SESSION[$uniqueId . '_progress'] ?? ['totalSteps' => 0, 'currentStep' => 0];
                    if ($progress['totalSteps'] == $progress['currentStep']) {
                        // Crear el archivo ZIP después de que todas las páginas hayan sido convertidas
                        $zipFile = $zipper->createZip($outputBase, $format);
                        if (file_exists($zipFile)) {
                            $_SESSION[$uniqueId . '_zipFile'] = $zipFile;
                            error_log("Archivo ZIP creado y guardado en la sesión: $zipFile");
                        } else {
                            error_log("El archivo ZIP no se creó: $zipFile");
                        }
                        session_write_close();
                    }
                } else {
                    error_log("No hay trabajos en la cola. Esperando...");
                    sleep(5);
                }
            } catch (ProcessFailedException $e) {
                error_log("Error en la ejecución del script CLI: " . $e->getMessage());
            } catch (Exception $e) {
                error_log("Error al procesar la tarea: " . $e->getMessage());
            }
        }
    }
}

// Ejecutar el Worker
$worker = new Worker();
$worker->run();
