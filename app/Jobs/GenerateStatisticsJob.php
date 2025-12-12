<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\BulletinCalculationService;
use App\Services\PdfGenerationService;

class GenerateStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;
    public $tries = 1;

    protected $params;
    protected $userId;
    protected $jobId;

    public function __construct($params, $userId, $jobId)
    {
        $this->params = $params;
        $this->userId = $userId;
        $this->jobId = $jobId;
    }

    public function handle(BulletinCalculationService $calc, PdfGenerationService $pdfService)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1800);

        try {
            Log::info("🚀 JOB STATISTIQUES DÉMARRÉ - ID: {$this->jobId}");
            $this->updateProgress(5, 'Initialisation...');

            // ✅ Étape 1 : calcul des données
            $this->updateProgress(15, 'Calcul des statistiques...');
            $bulletinData = $calc->calculateClassBulletin(
                $this->params['year_id'],
                $this->params['class_id'],
                $this->params['term_type_id']
            );

            // ✅ Étape 2 : génération des PDFs
            $this->updateProgress(50, 'Génération des fichiers PDF...');
            $classPdf = $pdfService->generateClassStatisticsPdf($bulletinData)->output();
            $subjectPdf = $pdfService->generateSubjectStatisticsPdf($bulletinData)->output();

            // ✅ Étape 3 : création du ZIP
            $this->updateProgress(80, 'Compression des fichiers...');
            $tempDir = storage_path('app/public/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            //$zipName = 'statistiques_' . $this->jobId . '.zip';
            /* $year = $bulletinData->year->name;
            $class = $bulletinData->class->name;
            $term = $bulletinData->termType->name;
            $zipName = "{$class}_Statistiques_{$term}_{$year}.zip";
            $zipPath = $tempDir . '/' . $zipName;

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $zip->addFromString('classement.pdf', $classPdf);
                $zip->addFromString('statistiques_matieres.pdf', $subjectPdf);

                $zip->close();
            } else {
                throw new \RuntimeException("Impossible de créer le fichier ZIP.");
            } */
            $year = preg_replace('/\s+/', '_', $bulletinData->year->name);
            $class = preg_replace('/\s+/', '_', $bulletinData->class->name);
            $term = preg_replace('/\s+/', '_', $bulletinData->termType->name);

            $classFilename = "{$class}_Classement_{$term}_{$year}.pdf";
            $subjectFilename = "{$class}_Statistiques_Matieres_{$term}_{$year}.pdf";

            $zipName = "{$class}_Statistiques_{$term}_{$year}.zip";
            $zipPath = $tempDir . '/' . $zipName;

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $zip->addFromString($classFilename, $classPdf);
                $zip->addFromString($subjectFilename, $subjectPdf);
                $zip->close();
            } else {
                throw new \RuntimeException("Impossible de créer le fichier ZIP.");
            }


            // ✅ Vérification du fichier généré
            if (!file_exists($zipPath)) {
                throw new \RuntimeException("Le fichier ZIP est introuvable après génération.");
            }

            // ✅ Étape 4 : mise à jour finale
            $this->updateProgress(100, 'Génération terminée !', $zipPath);
            Cache::put("stats_result_{$this->jobId}", $zipPath, 3600);

            Log::info("🎉 JOB STATISTIQUES TERMINÉ AVEC SUCCÈS - ID: {$this->jobId}");
        } catch (\Exception $e) {
            Log::error("❌ ERREUR dans GenerateStatisticsJob:");
            Log::error("Message: " . $e->getMessage());
            Log::error("Fichier: " . $e->getFile());
            Log::error("Ligne: " . $e->getLine());
            Log::error("Trace: " . $e->getTraceAsString());

            $this->updateProgress(0, 'Erreur lors de la génération', null, $e->getMessage());
        }
    }

    /**
     * ✅ Met à jour la progression du job dans le cache
     */
    protected function updateProgress($progress, $message, $filePath = null, $error = null)
    {
        $status = [
            'status' => $error ? 'error' : ($progress >= 100 ? 'completed' : 'processing'),
            'progress' => $progress,
            'message' => $message,
            'error' => $error,
            'download_url' => null
        ];

        if ($filePath && file_exists($filePath)) {
            // ✅ URL conforme à ta route Laravel
            $status['download_url'] = route('admin.reports.statistics.download', ['jobId' => $this->jobId]);
            Cache::put("stats_file_{$this->jobId}", $filePath, 3600);
        }

        Cache::store('file')->put("stats_progress_{$this->jobId}", $status, 3600);

        Log::info("📊 [JOB {$this->jobId}] Progression mise à jour: {$progress}% - {$message}");
    }

    /**
     * ✅ Gestion des échecs du job
     */
    public function failed(\Throwable $exception)
    {
        Log::error("💀 JOB STATISTIQUES ÉCHOUÉ - ID: {$this->jobId}");
        Log::error("Exception: " . $exception->getMessage());

        $this->updateProgress(0, 'Échec du job', null, $exception->getMessage());
    }
}
