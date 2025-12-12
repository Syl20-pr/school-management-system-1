<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BulletinCalculationService;
use App\Services\PdfGenerationService;
use App\Jobs\GenerateStatisticsJob; // optional
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TermStatisticsController extends Controller
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

    // Show the form (similar to index_bulletin)
    public function index()
    {
        $data = [
            'years' => \App\Models\StudentYear::orderBy('id','desc')->get(),
            'classes' => \App\Models\StudentClass::all(),
            'term_types' => \App\Models\TermType::all(),
        ];
        return view('backend.report.statistics.index_term_statistics', $data);
    }

    // SYNCHRONOUS: generate class ranking PDF and return download
    public function generateClassPdf(Request $request)
    {
        $request->validate([
           'year_id' => 'required|integer',
           'class_id' => 'required|integer',
           'term_type_id' => 'required|integer',
        ]);

        $bulletinData = $this->calculationService->calculateClassBulletin(
            $request->year_id, $request->class_id, $request->term_type_id
        );

        $pdf = $this->pdfService->generateClassStatisticsPdf($bulletinData);
        $filename = "{$bulletinData->class->name}_Classement_{$bulletinData->termType->name}.pdf";

        return $pdf->download($filename);
    }

    // SYNCHRONOUS: subject stats PDF
    public function generateSubjectPdf(Request $request)
    {
        $request->validate([
           'year_id' => 'required|integer',
           'class_id' => 'required|integer',
           'term_type_id' => 'required|integer',
        ]);

        $bulletinData = $this->calculationService->calculateClassBulletin(
            $request->year_id, $request->class_id, $request->term_type_id
        );

        $pdf = $this->pdfService->generateSubjectStatisticsPdf($bulletinData);
        $filename = "{$bulletinData->class->name}_Statistiques_Matieres_{$bulletinData->termType->name}.pdf";

        return $pdf->download($filename);
    }

    // SYNCHRONOUS: both files packaged into a ZIP
    public function generateAllZip(Request $request)
    {
        $request->validate([
           'year_id' => 'required|integer',
           'class_id' => 'required|integer',
           'term_type_id' => 'required|integer',
        ]);

        $bulletinData = $this->calculationService->calculateClassBulletin(
            $request->year_id, $request->class_id, $request->term_type_id
        );

        // let PdfGenerationService produce a zip & return path
        $zipPath = $this->pdfService->generateStatisticsZip($bulletinData);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // OPTIONAL: Async generation (dispatch job) - same pattern as your GenerateBulletinJob
    public function generateAsync(Request $request)
    {
        $request->validate([
           'year_id' => 'required|integer',
           'class_id' => 'required|integer',
           'term_type_id' => 'required|integer',
        ]);

        $jobId = uniqid('stats_', true);
        $params = $request->only(['year_id','class_id','term_type_id']);
        // store params & init progress (cache keys)
        Cache::put("stats_params_{$jobId}", $params, 3600);
        Cache::put("stats_progress_{$jobId}", [
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Initialisation...',
            'download_url' => null,
            'error' => null
        ], 3600);

        // Dispatch job (you will write GenerateStatisticsJob similar to GenerateBulletinJob)
        GenerateStatisticsJob::dispatch($params, auth()->id(), $jobId);

        return response()->json([
            'success' => true,
            'job_id' => $jobId
        ]);
    }

    public function checkProgress($jobId)
    {
        $progress = Cache::get("stats_progress_{$jobId}", [
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Initialisation...',
            'download_url' => null,
            'error' => null
        ]);
        return response()->json($progress);
    }

    public function downloadResult($jobId)
    {
        $filePath = Cache::get("stats_result_{$jobId}");
        if (!$filePath || !file_exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'Fichier non trouvé/expiré.'], 404);
        }
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
