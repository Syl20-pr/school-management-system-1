<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPromotionRequest;
use App\Http\Requests\ProcessBulkPromotionRequest;
use App\Models\AssignStudent;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\User;
use App\Models\StudentPromotionHistory;
use App\Services\StudentPromotionService;  
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;


class StudentPromotionController extends Controller
{
    protected $promotionService;

    public function __construct(StudentPromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function promotionView($student_id)
    {
        $data['student'] = AssignStudent::with(['student', 'student_year', 'student_class', 'promotionHistory'])
            ->where('student_id', $student_id)
            ->latest()
            ->firstOrFail();

        $data['classes'] = StudentClass::all();
        $data['history'] = $this->promotionService->getStudentPromotionHistory($student_id);
        $data['promotionOptions'] = $this->promotionService->getPromotionOptions($student_id);

        return view('backend.student.student_reg.student_promotion', $data);
    }

    public function processPromotion(ProcessPromotionRequest $request, $student_id)
    {
        try {
            $this->promotionService->processPromotion(
                $student_id,
                $request->action,
                $request->target_class,
                $request->reason
            );

            $message = match($request->action) {
                'promote' => 'Élève promu avec succès',
                'repeat' => 'Redoublement enregistré avec succès',
                'exclude' => 'Élève exclu avec succès',
                'custom' => 'Promotion spéciale enregistrée avec succès',
                default => 'Décision enregistrée avec succès'
            };

            return redirect()->route('student.registration.view')->with(['message' => $message, 'alert-type' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Erreur de promotion: '.$e->getMessage(), ['student'=>$student_id, 'req'=>$request->all()]);
            return redirect()->back()->withInput()->with(['message' => 'Erreur: '.$e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function cancelPromotionDecision($history_id)
    {
        try {
            $this->promotionService->cancelPromotion($history_id);
            return redirect()->back()->with(['message' => 'Décision annulée avec succès','alert-type'=>'success']);
        } catch (\Exception $e) {
            \Log::error('Erreur annulation promotion: '.$e->getMessage(), ['history'=>$history_id]);
            return redirect()->back()->with(['message' => 'Erreur lors de l\'annulation: '.$e->getMessage(), 'alert-type' => 'error']);
        }
    }
    /// Annulation en bulk////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* public function cancelPromotion(Request $request)
    {
        $request->validate([
            'history_id' => 'required|integer|exists:student_promotion_histories,id',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $this->promotionService->cancelPromotion(
                $request->history_id,
                $request->reason
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Décision annulée avec succès'
                ]);
            }

            return redirect()->back()->with([
                'message' => 'Décision annulée avec succès',
                'alert-type' => 'success'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur annulation promotion: '.$e->getMessage(), [
                'history_id' => $request->history_id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: '.$e->getMessage()
                ], 422);
            }

            return redirect()->back()->with([
                'message' => 'Erreur lors de l\'annulation: '.$e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    } */
   public function cancelPromotion(Request $request)
    {
        $request->validate([
            'history_id' => 'required|integer|exists:student_promotion_histories,id',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            // ✅ Le service retourne maintenant le statut restauré
            $result = $this->promotionService->cancelPromotion(
                $request->history_id,
                $request->reason
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Décision annulée avec succès',
                    'restored_status' => $result['restored_status'] // ✅ Retourné au frontend
                ]);
            }

            return redirect()->back()->with([
                'message' => 'Décision annulée avec succès',
                'alert-type' => 'success'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur annulation promotion: '.$e->getMessage(), [
                'history_id' => $request->history_id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: '.$e->getMessage()
                ], 422);
            }

            return redirect()->back()->with([
                'message' => 'Erreur lors de l\'annulation: '.$e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    // ✅ NOUVELLE MÉTHODE pour afficher l'historique des statuts
    public function statusHistoryView($student_id)
    {
        $data['student'] = User::findOrFail($student_id);
        $data['status_history'] = $this->promotionService->getStudentStatusHistory($student_id);
        $data['repeat_count'] = $this->promotionService->getStudentRepeatCount($student_id);
        
        return view('backend.student.student_reg.status_history', $data);
    }
 ////////////////////////////////////////////////////////////////////////   //////////////////////////////

    public function bulkPromotionView(Request $request)
    {
        // Stocker les filtres dans la session
        if ($request->has('year_id') && $request->has('class_id')) {
            session(['promotion_filters' => [
                'year_id' => $request->year_id,
                'class_id' => $request->class_id
            ]]);
        }

        $data['years'] = StudentYear::orderBy('name', 'desc')->get();
        $data['classes'] = StudentClass::all();

        if ($request->has('year_id') && $request->has('class_id')) {

            $data['selected_year'] = (int)$request->year_id;
            $data['selected_class'] = (int)$request->class_id;

            // Charger les élèves et leur dernier historique de promotion (le plus récent en premier)
            $students = AssignStudent::with([
                'student',
                'student_class',
                'promotionHistory' => function ($q) {
                    $q->orderByDesc('decision_date');
                }
            ])
                ->where('year_id', $request->year_id)
                ->where('class_id', $request->class_id)
                ->get();

            $data['all_students'] = $students;

            // Élèves ayant une décision ACTIVE (non annulée)
            $data['students_with_decision'] = $students->filter(function ($s) {
                $latest = $s->promotionHistory->first();
                return $latest && $latest->cancelled_at === null;
            })->values();

            // Élèves sans décision OU avec une dernière décision ANNULÉE
            $data['students_without_decision'] = $students->filter(function ($s) {
                $latest = $s->promotionHistory->first();
                return !$latest || $latest->cancelled_at !== null;
            })->values();

            // Classes courantes et suivantes
            $currentClass = StudentClass::find($request->class_id);
            $data['current_classes'] = $currentClass
                ? StudentClass::where('level', $currentClass->level)->get()
                : collect();

            $data['next_classes'] = $currentClass
                ? $currentClass->getNaturalNextClasses()
                : collect();
        }

        return view('backend.student.student_reg.bulk_promotion', $data);
    }


    public function processBulkPromotion(ProcessBulkPromotionRequest $request)
    {
        \Log::debug('Bulk promotion data:', [
        'action' => $request->action,
        'target_class' => $request->target_class, // Vérifiez que cette valeur est bien passée
        'student_ids' => $request->student_ids,
        'reason' => $request->reason
    ]);

        $success = 0; $errors = [];
        foreach ($request->student_ids as $sid) {
            try {
                $this->promotionService->processPromotion(
                    $sid,
                    $request->action,
                    $request->target_class,
                    $request->reason
                );
                $success++;
            } catch (\Exception $e) {
                $errors[] = "ID {$sid}: ".$e->getMessage();
            }
        }

        $message = "Traitement terminé: {$success} réussite(s), ".count($errors).' échec(s)';
        if (count($errors) > 0) {
            return redirect()->back()->with(['message'=>$message,'alert-type'=>'warning'])->with('bulk_errors', $errors);
        }
       // Récupérer les filtres depuis la session et rediriger
        $filters = session('promotion_filters', []);
        return redirect()->route('student.promotion.bulk',$filters)->with(['message'=>$message,'alert-type'=>'success']);
    }

    public function statistics(Request $request)
    {
        $year_id = $request->get('year_id') ?? StudentYear::orderBy('is_current','desc')->first()->id;
        $data['year'] = StudentYear::findOrFail($year_id);
        $data['years'] = StudentYear::orderBy('name','desc')->get();
        $data['statistics'] = $this->promotionService->getYearEndStatistics($year_id);

        return view('backend.student.student_reg.promotion_statistics', $data);
    }

    public function classStatisticsAjax($year_id)
    {
        $stats = $this->promotionService->getYearEndStatisticsByClass($year_id);
        return response()->json($stats);
    }
}
