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
use App\Models\TermType;

class GenerateBulletinJob implements ShouldQueue
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

    /* public function handle()
    {
        try {
            Log::info("🚀 Début du job de génération de bulletin - Job ID: " . $this->jobId);
            Log::info("📋 Paramètres:", $this->params);
            
            $this->updateProgress(5, 'Démarrage du calcul des données...');
            
            // Test de débogage
            Log::info("🔍 Test des modèles...");
            $testUser = \App\Models\User::find(1343);
            if ($testUser) {
                Log::info("✅ User modèle fonctionne: " . $testUser->name);
                $marksCount = $testUser->studentMarks()->count();
                Log::info("✅ Relation studentMarks fonctionne: " . $marksCount . " notes");
            }

            // Initialiser les services
            $calculationService = app(BulletinCalculationService::class);
            $pdfService = app(PdfGenerationService::class);
            
            $this->updateProgress(15, 'Calcul des moyennes des élèves...');
            
            Log::info("📊 Début du calcul des bulletins...");
            $bulletinData = $calculationService->calculateClassBulletin(
                $this->params['year_id'],
                $this->params['class_id'],
                $this->params['term_type_id']
            );
            Log::info("✅ Calcul des bulletins terminé");

            $this->updateProgress(40, 'Génération des PDF...');
            
            $yearName = $bulletinData->year->name;
            $className = $bulletinData->class->name;
            $termType = TermType::find($this->params['term_type_id']);
            $termTypeName = $termType ? $termType->name : 'N/A';

            Log::info("📄 Génération PDF pour: $className - $termTypeName");

            $isCycle1Final = ($this->params['term_type_id'] == 5);
            $isCycle2Final = ($this->params['term_type_id'] == 7);

            $this->updateProgress(70, 'Finalisation...');
            
            if ($isCycle1Final || $isCycle2Final) {
                Log::info("🗜️ Génération ZIP (terme final)");
                $filePath = $pdfService->generateZipWithBothReports(
                    $bulletinData,
                    $className,
                    $termTypeName
                );
            } else {
                Log::info("📑 Génération PDF simple");
                $pdf = $pdfService->generateBulletinPdf($bulletinData);
                $filename = $className . '_Bulletins_' . $termTypeName . '.pdf';
                $filePath = storage_path('app/public/temp/' . $filename);
                
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }
                
                $pdf->save($filePath);
                Log::info("💾 PDF sauvegardé: " . $filePath);
            }

            $this->updateProgress(100, 'Génération terminée!', $filePath);
            
            Cache::put("bulletin_result_{$this->jobId}", $filePath, 3600);
            Log::info("🎉 Génération terminée avec succès!");

        } catch (\Exception $e) {
            Log::error("❌ ERREUR dans GenerateBulletinJob:");
            Log::error("Message: " . $e->getMessage());
            Log::error("Fichier: " . $e->getFile());
            Log::error("Ligne: " . $e->getLine());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            $this->updateProgress(0, 'Erreur lors de la génération', null, $e->getMessage());
        }
    } */
/*    public function handle()
{
    try {
        \Log::info("🚀🚀🚀 DÉBUT DU JOB - Job ID: " . $this->jobId);
        \Log::info("📋 Paramètres reçus:", $this->params);
        
        // TEST IMMÉDIAT : Mettre à jour la progression dès le début
        $this->updateProgress(10, 'Job démarré avec succès');

        \Log::info("✅ Test progression initiale envoyé");

        // Vérifier que les services sont disponibles
        $calculationService = app(BulletinCalculationService::class);
        $pdfService = app(PdfGenerationService::class);
        
        \Log::info("✅ Services initialisés");

        $this->updateProgress(20, 'Calcul des données en cours...');

        // ... le reste de votre code existant ...

    } catch (\Exception $e) {
        \Log::error("❌❌❌ ERREUR CRITIQUE dans GenerateBulletinJob:");
        \Log::error("Message: " . $e->getMessage());
        \Log::error("Fichier: " . $e->getFile());
        \Log::error("Ligne: " . $e->getLine());
        \Log::error("Stack trace: " . $e->getTraceAsString());
        
        $this->updateProgress(0, 'Erreur critique', null, $e->getMessage());
    }
} */
public function handle()
{
    // ✅ Augmentation limites PHP
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 1800);

    $startTime = time();
    
    try {
        \Log::info("🚀 JOB DÉMARRÉ - ID: {$this->jobId}");
        $this->updateProgress(5, 'Initialisation...');

        // ✅ VÉRIFICATION TIMEOUT
        if (time() - $startTime > 1500) { // 25 minutes
            throw new \RuntimeException("Timeout: génération trop longue");
        }

        // ✅ Initialiser les services
        $calculationService = app(BulletinCalculationService::class);
        $pdfService = app(PdfGenerationService::class);
        
        \Log::info("✅ Services initialisés");
        $this->updateProgress(15, 'Calcul des données...');
        
        // ✅ Calcul avec gestion d'erreur
        try {
            \Log::info("📊 Début calcul bulletin...");
            $bulletinData = $calculationService->calculateClassBulletin(
                $this->params['year_id'],
                $this->params['class_id'],
                $this->params['term_type_id']
            );
            \Log::info("✅ Calcul terminé");
        } catch (\Exception $e) {
            \Log::error("❌ Erreur calcul bulletin: " . $e->getMessage());
            throw new \RuntimeException("Erreur lors du calcul des données: " . $e->getMessage());
        }

        $this->updateProgress(50, 'Génération du PDF...');
        
        $yearName = $bulletinData->year->name;
        $className = $bulletinData->class->name;
        $termType = TermType::find($this->params['term_type_id']);
        $termTypeName = $termType ? $termType->name : 'N/A';

        \Log::info("📄 Génération pour: $className - $termTypeName");

        // ✅ Déterminer le type de génération
        $isCycle1Final = ($this->params['term_type_id'] == 5);
        $isCycle2Final = ($this->params['term_type_id'] == 7);

        $this->updateProgress(75, 'Finalisation du fichier...');
        
        // ✅ Générer le fichier selon le type
        if ($isCycle1Final || $isCycle2Final) {
            \Log::info("🗜️ Génération ZIP (terme final)");
            $filePath = $pdfService->generateZipWithBothReports(
                $bulletinData,
                $className,
                $termTypeName
            );
        } else {
            \Log::info("📑 Génération PDF simple");
            $pdf = $pdfService->generateBulletinPdf($bulletinData);
            
            $filename = $className . '_Bulletins_' . $termTypeName . '.pdf';
            $tempDir = storage_path('app/public/temp');
            
            // ✅ Créer le dossier si nécessaire
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $filePath = $tempDir . '/' . $filename;
            \Log::info("💾 Sauvegarde PDF: " . $filePath);
            
            $pdf->save($filePath);
            
            \Log::info("✅ PDF sauvegardé");
        }

        // ✅ Vérifier que le fichier existe vraiment
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Le fichier généré est introuvable: $filePath");
        }

        \Log::info("📁 Fichier confirmé: " . $filePath);
        $this->updateProgress(100, 'Génération terminée!', $filePath);
        
        // ✅ Stocker le chemin pour téléchargement
        Cache::put("bulletin_result_{$this->jobId}", $filePath, 3600);
        
        \Log::info("🎉 JOB TERMINÉ AVEC SUCCÈS - Job ID: {$this->jobId}");

    } catch (\Exception $e) {
        \Log::error("❌ ERREUR CRITIQUE dans GenerateBulletinJob:");
        \Log::error("Message: " . $e->getMessage());
        \Log::error("Fichier: " . $e->getFile());
        \Log::error("Ligne: " . $e->getLine());
        \Log::error("Trace: " . $e->getTraceAsString());
        
        // ✅ Mettre à jour la progression avec l'erreur
        $this->updateProgress(0, 'Erreur lors de la génération', null, $e->getMessage());
        
        // ✅ Relancer l'exception pour marquer le job comme failed
        throw $e;
    }
}

    /* protected function updateProgress($progress, $message, $filePath = null, $error = null)
    {
        $status = [
            'status' => $error ? 'error' : ($progress >= 100 ? 'completed' : 'processing'),
            'progress' => $progress,
            'message' => $message,
            'error' => $error
        ];

        // ✅ CORRECTION : Utilisez la route exacte selon vos routes
        if ($filePath) {
            $status['download_url'] = route('admin.reports.bulletin.download', ['job_id' => $this->jobId]);
        }

        Cache::put("bulletin_progress_{$this->jobId}", $status, 3600);
        
        Log::info("📊 Progression mise à jour: " . $progress . "% - " . $message);
    } */
   // Dans GenerateBulletinJob.php - CORRIGER cette méthode
    /**
 * ✅ Mise à jour de la progression avec URL correcte
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

    // ✅ CORRECTION : Construire l'URL de téléchargement correctement
    if ($filePath && file_exists($filePath)) {
        $status['download_url'] = url('/admin/reports/bulletin/download/' . $this->jobId);
        // ✅ STOCKER LE FICHIER DANS LE CACHE AUSSI
        Cache::put("bulletin_file_{$this->jobId}", $filePath, 3600);
    }

    // ✅ FORCER LE STOCKAGE AVEC LE DRIVER 'file' EXPLICITEMENT
    $cache = Cache::store('file');
    // ✅ Stocker dans le cache avec un TTL de 1 heure
    $cache->put("bulletin_progress_{$this->jobId}", $status, 3600); // ← LIGNE CORRIGÉE

    // ✅ DEBUG : Vérifier immédiatement que c'est bien stocké
    $stored = $cache->get("bulletin_progress_{$this->jobId}");
    \Log::info("✅ PROGRESS STORED - Job: {$this->jobId}, Progress: {$progress}%, Stored: " . ($stored ? 'YES' : 'NO'));
    
    \Log::info("📊 PROGRESS UPDATE - Job: {$this->jobId}, Progress: {$progress}%, Message: {$message}");
}

/**
 * ✅ Gestion des erreurs lors de l'échec du job
 */
public function failed(\Throwable $exception)
{
    \Log::error("💀 JOB FAILED - Job ID: {$this->jobId}");
    \Log::error("Exception: " . $exception->getMessage());
    
    // Mettre à jour le cache avec l'erreur
    $this->updateProgress(0, 'Échec de la génération', null, $exception->getMessage());
}

}