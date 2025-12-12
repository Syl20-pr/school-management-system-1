<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateBulletinJob;
use App\Http\Requests\GenerateBulletinRequest;
use App\Models\{StudentYear, StudentClass, TermType};
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Services\BulletinCalculationService;
use App\Services\PdfGenerationService;

class BulletinSheetController extends Controller
{
    protected $calculationService;
    protected $pdfService;

    public function __construct(
        BulletinCalculationService $calculationService,
        PdfGenerationService $pdfService
    ) {
        $this->calculationService = $calculationService;
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $data = [
            'years' => StudentYear::orderBy('id', 'desc')->get(),
            'classes' => StudentClass::all(),
            'term_types' => TermType::all(),
        ];

        return view('backend.report.bulletin.index_bulletin', $data);
    }

    // ✅ CORRECTION : Renommez cette méthode pour éviter le conflit
    // AJOUTER cette méthode pour nettoyer les vieux jobs
private function cleanupOldJobs()
{
    try {
        // Supprimer les jobs de plus de 2 heures
        $keys = Cache::get('bulletin_job_keys', []);
        $now = now();
        $cleaned = [];
        
        foreach ($keys as $jobId => $createdAt) {
            if ($now->diffInHours($createdAt) > 2) {
                Cache::forget("bulletin_progress_{$jobId}");
                Cache::forget("bulletin_result_{$jobId}");
                Cache::forget("bulletin_params_{$jobId}");
            } else {
                $cleaned[$jobId] = $createdAt;
            }
        }
        
        Cache::put('bulletin_job_keys', $cleaned, 7200); // 2 heures
    } catch (\Exception $e) {
        \Log::warning("Nettoyage des vieux jobs échoué: " . $e->getMessage());
    }
}

// MODIFIER generateAsync pour enregistrer les nouveaux jobs
/* public function generateAsync(GenerateBulletinRequest $request)
{
    try {
        $validated = $request->validated();
        $userId = auth()->id();
        
        // ✅ VALIDATION RENFORCÉE
        if (!$validated['year_id'] || !$validated['class_id'] || !$validated['term_type_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires'
            ], 422);
        }

        $jobId = uniqid('bulletin_', true);
        
        \Log::info("🎯 NOUVEAU JOB CRÉÉ - Job ID: " . $jobId);
        \Log::info("📋 Paramètres validés:", $validated);
        
        // ✅ STOCKAGE SÉCURISÉ des paramètres
        Cache::put("bulletin_params_{$jobId}", $validated, 3600);
        
        // ✅ INITIALISATION PROGRESSION
        Cache::put("bulletin_progress_{$jobId}", [
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Initialisation...',
            'download_url' => null,
            'error' => null
        ], 3600);
        
        // ✅ LANCEMENT DU JOB avec gestion d'erreur
        try {
            GenerateBulletinJob::dispatch($validated, $userId, $jobId);
            \Log::info("🚀 JOB DISPATCHÉ AVEC SUCCÈS - Job ID: " . $jobId);
            
            return response()->json([
                'success' => true,
                'message' => 'Génération des bulletins en cours...',
                'job_id' => $jobId
            ]);
            
        } catch (\Exception $dispatchError) {
            \Log::error("❌ ERREUR DISPATCH JOB: " . $dispatchError->getMessage());
            
            // Nettoyer le cache en cas d'erreur
            Cache::forget("bulletin_params_{$jobId}");
            Cache::forget("bulletin_progress_{$jobId}");
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du lancement du traitement: ' . $dispatchError->getMessage()
            ], 500);
        }

    } catch (\Exception $e) {
        \Log::error("❌ ERREUR GÉNÉRATION BULLETIN ASYNC: " . $e->getMessage());
        \Log::error("Stack trace: " . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
} */
public function generateAsync(GenerateBulletinRequest $request)
{
    try {
        $validated = $request->validated();
        $userId = auth()->id();
        
        // ✅ VALIDATION RENFORCÉE
        if (!$validated['year_id'] || !$validated['class_id'] || !$validated['term_type_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires'
            ], 422);
        }

        $jobId = uniqid('bulletin_', true);
        
        \Log::info("🎯 NOUVEAU JOB CRÉÉ - Job ID: " . $jobId);
        \Log::info("📋 Paramètres validés:", $validated);
        
        // ✅ STOCKAGE SÉCURISÉ des paramètres
        Cache::put("bulletin_params_{$jobId}", $validated, 3600);
        
        // ✅ LANCEMENT DU JOB D'ABORD
        try {
            GenerateBulletinJob::dispatch($validated, $userId, $jobId);
            \Log::info("🚀 JOB DISPATCHÉ AVEC SUCCÈS - Job ID: " . $jobId);
            
            // ✅ INITIALISATION PROGRESSION APRÈS le dispatch
            Cache::put("bulletin_progress_{$jobId}", [
                'status' => 'processing',
                'progress' => 0,
                'message' => 'Lancement du job...',
                'download_url' => null,
                'error' => null
            ], 3600);
            
            return response()->json([
                'success' => true,
                'message' => 'Génération des bulletins en cours...',
                'job_id' => $jobId
            ]);
            
        } catch (\Exception $dispatchError) {
            \Log::error("❌ ERREUR DISPATCH JOB: " . $dispatchError->getMessage());
            
            // Nettoyer le cache en cas d'erreur
            Cache::forget("bulletin_params_{$jobId}");
            Cache::forget("bulletin_progress_{$jobId}");
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du lancement du traitement: ' . $dispatchError->getMessage()
            ], 500);
        }

    } catch (\Exception $e) {
        \Log::error("❌ ERREUR GÉNÉRATION BULLETIN ASYNC: " . $e->getMessage());
        \Log::error("Stack trace: " . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
}

    /* public function checkProgress(Request $request, $jobId)
    {
        try {
            $progress = Cache::get("bulletin_progress_{$jobId}", [
                'status' => 'processing',
                'progress' => 0,
                'message' => 'Initialisation...',
                'download_url' => null,
                'error' => null
            ]);

            return response()->json($progress);
        } catch (\Exception $e) {
            \Log::error("Erreur checkProgress: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'progress' => 0,
                'message' => 'Erreur de vérification',
                'error' => $e->getMessage()
            ], 500);
        }
    } */
   public function checkProgress(Request $request, $jobId)
    {
        try {
            \Log::info("🔍 CHECK PROGRESS - Job ID: " . $jobId);
            
            /*$progress = Cache::get("bulletin_progress_{$jobId}", [
                'status' => 'processing',
                'progress' => 0,
                'message' => 'Initialisation...',
                'download_url' => null,
                'error' => null
            ]);
            */
            // ✅ UTILISER LE MÊME DRIVER 'file' EXPLICITEMENT
            $cache = Cache::store('file');
            $progress = $cache->get("bulletin_progress_{$jobId}");

            // ✅ DEBUG DÉTAILLÉ
            \Log::info("🔍 CACHE CHECK - Key: bulletin_progress_{$jobId}, Found: " . ($progress ? 'YES' : 'NO'));
            
            if (!$progress) {
                \Log::warning("⚠️ PROGRESS NOT FOUND - Job ID: " . $jobId);
                $progress = [
                    'status' => 'processing',
                    'progress' => 0,
                    'message' => 'Initialisation...',
                    'download_url' => null,
                    'error' => null
                ];
            }else {
                \Log::info("✅ PROGRESS FOUND - Status: " . $progress['status'] . ", Progress: " . $progress['progress'] . "%");
            }

            \Log::info("📋 PROGRESS DATA:" . json_encode($progress));

            return response()->json($progress);
            
        } catch (\Exception $e) {
            \Log::error("❌ ERREUR checkProgress: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'progress' => 0,
                'message' => 'Erreur de vérification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* public function downloadResult(Request $request, $jobId)
    {
        try {
            $filePath = Cache::get("bulletin_result_{$jobId}");
            
            if (!$filePath || !file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé ou expiré. Veuillez relancer la génération.'
                ], 404);
            }

            $filename = basename($filePath);
            
            return response()->download($filePath, $filename)
                ->deleteFileAfterSend(true);
                
        } catch (\Exception $e) {
            \Log::error("Erreur downloadResult: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement: ' . $e->getMessage()
            ], 500);
        }
    } */
   public function downloadResult(Request $request, $jobId)
{
    try {
        \Log::info("📥 TENTATIVE DE TÉLÉCHARGEMENT - Job ID: " . $jobId);
        
        $filePath = Cache::get("bulletin_result_{$jobId}");
        
        if (!$filePath) {
            \Log::error("❌ Fichier non trouvé dans le cache pour: " . $jobId);
            return response()->json([
                'success' => false,
                'message' => 'Fichier non trouvé ou expiré. Veuillez relancer la génération.'
            ], 404);
        }

        \Log::info("📁 Chemin du fichier: " . $filePath);
        
        if (!file_exists($filePath)) {
            \Log::error("❌ Fichier n'existe pas sur le disque: " . $filePath);
            return response()->json([
                'success' => false,
                'message' => 'Fichier introuvable sur le serveur.'
            ], 404);
        }

        $filename = basename($filePath);
        \Log::info("✅ Téléchargement du fichier: " . $filename);
        
        // Forcer le téléchargement avec les bons headers
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ])->deleteFileAfterSend(true);
        
    } catch (\Exception $e) {
        \Log::error("❌ ERREUR downloadResult: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du téléchargement: ' . $e->getMessage()
        ], 500);
    }
}

    // ✅ GARDER votre méthode generate originale pour la compatibilité
    public function generate(GenerateBulletinRequest $request)
    {
        try {
            $validated = $request->validated();

            // Calcul des données du bulletin
            $bulletinData = $this->calculationService->calculateClassBulletin(
                $validated['year_id'],
                $validated['class_id'],
                $validated['term_type_id']
            );

            $yearName = $bulletinData->year->name;
            $className = $bulletinData->class->name;
            $termType = TermType::find($validated['term_type_id']);
            $termTypeName = $termType ? $termType->name : 'N/A';

            // Détermination du cycle et du terme final basé sur l'ID
            $isCycle1Final = ($validated['term_type_id'] == 5); // Trimestre 3
            $isCycle2Final = ($validated['term_type_id'] == 7); // Semestre 2

            if ($isCycle1Final || $isCycle2Final) {
                // Génération du ZIP avec les deux rapports
                $zipPath = $this->pdfService->generateZipWithBothReports(
                    $bulletinData,
                    $className,
                    $termTypeName
                );

                if ($zipPath && file_exists($zipPath)) {
                    $headers = [
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                    ];

                    return response()->download($zipPath, $className . '_Reports_' . $termTypeName . '.zip', $headers)
                        ->deleteFileAfterSend(true);
                }
            }

            // Génération d'un seul PDF
            $pdf = $this->pdfService->generateBulletinPdf($bulletinData);
            $filename = $className . '_Bulletins_' . $termTypeName . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error("Erreur génération bulletin: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la génération: ' . $e->getMessage());
        }
    }

    // ✅ GARDER vos autres méthodes existantes
    public function preview(GenerateBulletinRequest $request)
    {
        try {
            $validated = $request->validated();

            $bulletinData = $this->calculationService->calculateClassBulletin(
                $validated['year_id'],
                $validated['class_id'],
                $validated['term_type_id']
            );

            return view('backend.report.bulletin.preview', [
                'bulletinData' => $bulletinData->toArray()
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur preview bulletin: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la génération de l\'aperçu: ' . $e->getMessage());
        }
    }

    public function apiGenerate(GenerateBulletinRequest $request)
    {
        try {
            $validated = $request->validated();

            $bulletinData = $this->calculationService->calculateClassBulletin(
                $validated['year_id'],
                $validated['class_id'],
                $validated['term_type_id']
            );

            // Transformer en tableau
            $data = $bulletinData->toArray();

            // Renommer la clé 'class' -> 'classe'
            if (isset($data['class'])) {
                $data['classe'] = $data['class'];
                unset($data['class']);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur apiGenerate bulletin: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage()
            ], 500);
        }
    }
}