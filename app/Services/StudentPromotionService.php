<?php

namespace App\Services;

use App\Models\AssignStudent;
use App\Models\StudentPromotionHistory;
use App\Models\StudentStatusHistory;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentPromotionService
{
    /**
     * Process a promotion action for one student.
     */
    public function processPromotion($studentId, $action, $targetClassId = null, $reason = null)
    {
        return DB::transaction(function () use ($studentId, $action, $targetClassId, $reason) {
            $currentAssignment = AssignStudent::with(['student_year','student_class','student'])
                ->where('student_id', $studentId)->latest()->firstOrFail();

            // Récupérer le statut actuel AVANT modification
            $oldStatus = $currentAssignment->student->statusclass;

            $historyData = [
                'student_id'   => $studentId,
                'from_year_id' => $currentAssignment->year_id,
                'from_class_id'=> $currentAssignment->class_id,
                'decision_by'  => auth()->id(),
                'decision_date'=> now(),
                'reason'       => $reason,
            ];

            $nextYear = null;
            $newStatus = null;
            $changeReason = null;

            if ($action !== 'exclude') {
                $nextYear = $this->getNextAcademicYear($currentAssignment->year_id);
                if (!$nextYear) throw new Exception("Impossible de déterminer l'année scolaire suivante.");
            }

            switch ($action) {
                case 'promote':
                    $targetClass = $targetClassId ? StudentClass::findOrFail($targetClassId) : $this->getNextClass($currentAssignment->class_id);
                    if (!$targetClass) throw new Exception("Aucune classe cible pour la promotion.");
                    
                    $historyData['to_year_id'] = $nextYear->id;
                    $historyData['to_class_id'] = $targetClass->id;
                    $historyData['action'] = 'promote';
                    
                    // ✅ Nouveau statut = N (peu importe l'ancien)
                    $newStatus = 'N';
                    $changeReason = 'promotion';
                    
                    // ✅ MODIFICATION CRITIQUE : Créer affectation AVEC le statut
                    $this->createAssignmentWithStatus($currentAssignment, $nextYear->id, $targetClass->id, $newStatus);
                    
                    // Mettre à jour le statut actuel dans users
                    $currentAssignment->student->update(['statusclass' => $newStatus]);
                    break;

                case 'repeat':
                    \Log::debug("Processing REPEAT", [
                        'student' => $studentId,
                        'targetClassId' => $targetClassId,
                        'currentClassId' => $currentAssignment->class_id
                    ]);

                    $currClass = StudentClass::findOrFail($currentAssignment->class_id);
                    
                    if (!$targetClassId) {
                        $targetClass = $currClass;
                    } else {
                        $targetClass = StudentClass::findOrFail($targetClassId);
                        
                        if ($targetClass->level !== $currClass->level) {
                            throw new Exception("La classe de redoublement doit être du même niveau.");
                        }
                    }
                    
                    $historyData['to_year_id'] = $nextYear->id;
                    $historyData['to_class_id'] = $targetClass->id;
                    $historyData['action'] = 'repeat';
                    
                    // ✅ Nouveau statut = D (redoublant)
                    $newStatus = 'D';
                    $changeReason = 'repeat';
                    
                    // ✅ MODIFICATION CRITIQUE : Créer affectation AVEC le statut
                    $this->createAssignmentWithStatus($currentAssignment, $nextYear->id, $targetClass->id, $newStatus);
                    
                    $currentAssignment->student->update(['statusclass' => $newStatus]);
                    break;

                case 'exclude':
                    $historyData['action'] = 'exclude';
                    
                    // ✅ Le statut ne change PAS lors d'une exclusion
                    $currentAssignment->student->update(['status' => 0]);
                    // Pas de changement de statusclass, donc pas d'enregistrement d'historique
                    break;

                case 'custom':
                    $targetClass = StudentClass::findOrFail($targetClassId);
                    $historyData['to_year_id'] = $nextYear->id;
                    $historyData['to_class_id'] = $targetClass->id;
                    $historyData['action'] = 'promote';
                    
                    // ✅ Promotion spéciale = Nouveau
                    $newStatus = 'N';
                    $changeReason = 'promotion';
                    
                    $this->createAssignmentWithStatus($currentAssignment, $nextYear->id, $targetClass->id, $newStatus);
                    $currentAssignment->student->update(['statusclass' => $newStatus]);
                    break;

                default:
                    throw new Exception("Action inconnue");
            }

            // Créer l'historique de promotion
            $promotionHistory = StudentPromotionHistory::create($historyData);

            // ✅ ENREGISTRER LE CHANGEMENT DE STATUT (sauf pour exclusion)
            if ($newStatus !== null && $oldStatus !== $newStatus) {
                $this->recordStatusChange(
                    $studentId,
                    $nextYear->id,
                    $historyData['to_class_id'],
                    $oldStatus,
                    $newStatus,
                    $changeReason,
                    $promotionHistory->id,
                    $reason
                );
            }

            return true;
        });
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Créer affectation avec statut
     */
    private function createAssignmentWithStatus($currentAssignment, $yearId, $classId, $status)
    {
        $exists = AssignStudent::where('student_id', $currentAssignment->student_id)
            ->where('year_id', $yearId)
            ->where('class_id', $classId)
            ->exists();

        if (!$exists) {
            $newAssignment = $currentAssignment->replicate();
            $newAssignment->year_id = $yearId;
            $newAssignment->class_id = $classId;
            $newAssignment->statusclass = $status; // ✅ STOCKAGE DU STATUT
            $newAssignment->created_at = now();
            $newAssignment->updated_at = now();
            $newAssignment->save();
            
            \Log::info("✅ Affectation créée avec statut: $status pour student_id: {$currentAssignment->student_id}");
        }
    }

    /**
     * ✅ MÉTHODE : Enregistrer un changement de statut dans l'historique
     */
    private function recordStatusChange(
        $studentId, 
        $yearId, 
        $classId, 
        $oldStatus, 
        $newStatus, 
        $changeReason, 
        $promotionHistoryId = null,
        $comment = null
    ) {
        StudentStatusHistory::create([
            'student_id' => $studentId,
            'year_id' => $yearId,
            'class_id' => $classId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'change_reason' => $changeReason,
            'promotion_history_id' => $promotionHistoryId,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
            'comment' => $comment
        ]);

        \Log::info('Changement de statut enregistré', [
            'student_id' => $studentId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $changeReason
        ]);
    }

    /**
     * Annuler une décision de promotion avec restauration du statut
     */
    public function cancelPromotion(int $historyId, string $reason = null)
    {
        return DB::transaction(function () use ($historyId, $reason) {
            $history = StudentPromotionHistory::with(['student', 'fromYear', 'toYear'])->findOrFail($historyId);

            if ($history->cancelled_at) {
                throw new Exception("Cette décision est déjà annulée.");
            }

            $currentUserId = auth()->id();
            
            if ($history->decision_by !== $currentUserId && !auth()->user()->hasRole('admin')) {
                throw new Exception("Vous n'avez pas l'autorisation d'annuler cette décision.");
            }

            if ($history->decision_date->diffInDays(now()) > 30 && !auth()->user()->hasRole('admin')) {
                throw new Exception("Seuls les administrateurs peuvent annuler les décisions de plus de 30 jours.");
            }

            // Récupérer le statut ACTUEL avant annulation
            $currentStatus = $history->student->statusclass;

            switch ($history->action) {
                case 'promote':
                case 'custom':
                case 'repeat':
                    $assignment = AssignStudent::where('student_id', $history->student_id)
                        ->where('year_id', $history->to_year_id)
                        ->where('class_id', $history->to_class_id)
                        ->first();

                    if ($assignment) {
                        $assignment->delete();
                    }
                    
                    // ✅ Récupérer le statut d'AVANT cette décision
                    $previousStatusRecord = StudentStatusHistory::where('student_id', $history->student_id)
                        ->where('promotion_history_id', $historyId)
                        ->first();
                    
                    $restoredStatus = $previousStatusRecord ? $previousStatusRecord->old_status : 'N';
                    
                    // Restaurer le statut
                    $history->student->update(['statusclass' => $restoredStatus]);
                    
                    // ✅ Enregistrer l'annulation dans l'historique de statut
                    $this->recordStatusChange(
                        $history->student_id,
                        $history->from_year_id,
                        $history->from_class_id,
                        $currentStatus,
                        $restoredStatus,
                        'cancellation',
                        $historyId,
                        $reason
                    );
                    break;

                case 'exclude':
                    $user = User::find($history->student_id);
                    if ($user) {
                        $user->update(['status' => 1]);
                    }
                    break;
            }

            $history->update([
                'cancelled_at' => now(),
                'cancelled_by' => $currentUserId,
                'cancellation_reason' => $reason
            ]);

            \Log::info('Décision de promotion annulée', [
                'history_id' => $historyId,
                'student_id' => $history->student_id,
                'action' => $history->action,
                'cancelled_by' => $currentUserId,
                'restored_status' => $restoredStatus ?? null
            ]);

            return [
                'success' => true,
                'restored_status' => $restoredStatus ?? $history->student->statusclass
            ];
        });
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Obtenir le nombre de redoublements d'un élève
     */
    public function getStudentRepeatCount($studentId)
    {
        return StudentStatusHistory::where('student_id', $studentId)
            ->where('change_reason', 'repeat')
            ->count();
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Obtenir l'historique détaillé des statuts d'un élève
     */
    public function getStudentStatusHistory($studentId)
    {
        return StudentStatusHistory::with(['year', 'class', 'changer'])
            ->where('student_id', $studentId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Statistiques de redoublement par classe
     */
    public function getRepeatStatisticsByClass($yearId)
    {
        return StudentStatusHistory::where('year_id', $yearId)
            ->where('change_reason', 'repeat')
            ->select('class_id', DB::raw('count(*) as repeat_count'))
            ->groupBy('class_id')
            ->with('class')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->class->name => $item->repeat_count];
            });
    }

    /**
     * Déterminer l'année scolaire suivante.
     */
    public function getNextAcademicYear($currentYearId)
    {
        $current = StudentYear::findOrFail($currentYearId);
        return StudentYear::where('id', '>', $currentYearId)->orderBy('id')->first();
    }

    /**
     * Trouver la prochaine classe.
     */
    public function getNextClass($currentClassId)
    {
        $currentClass = StudentClass::findOrFail($currentClassId);

        if ($currentClass->level === 0) return null;

        $nextClasses = $currentClass->getNaturalNextClasses();
        return $nextClasses->count() > 0 ? $nextClasses->first() : null;
    }

    /**
     * Historique des promotions d'un élève.
     */
    public function getStudentPromotionHistory($studentId)
    {
        return StudentPromotionHistory::with(['fromYear', 'toYear', 'fromClass', 'toClass', 'decisionMaker'])
            ->where('student_id', $studentId)
            ->orderBy('decision_date', 'desc')
            ->get();
    }

    /**
     * Options de promotion pour un élève.
     */
    public function getPromotionOptions($studentId)
    {
        $currentAssignment = AssignStudent::where('student_id', $studentId)
            ->with(['student_class'])
            ->latest()
            ->firstOrFail();

        $currentClass = $currentAssignment->student_class;

        return [
            'current_class' => $currentClass,
            'next_classes' => $currentClass->getNaturalNextClasses(),
            'can_promote'  => $currentClass->getNaturalNextClasses()->count() > 0,
            'max_level'    => 0
        ];
    }

    /**
     * Statistiques globales d'une année.
     */
    public function getYearEndStatistics($yearId)
    {
        $promotions = StudentPromotionHistory::where('from_year_id', $yearId)
            ->whereNull('cancelled_at')
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->get()
            ->keyBy('action');
        
        return [
            'promoted' => $promotions->get('promote')->count ?? 0,
            'repeated' => $promotions->get('repeat')->count ?? 0,
            'excluded' => $promotions->get('exclude')->count ?? 0,
            'total'    => AssignStudent::where('year_id', $yearId)->count()
        ];
    }

    /**
     * Statistiques par classe dans une année.
     */
    public function getYearEndStatisticsByClass($yearId)
    {
        $results = StudentPromotionHistory::where('from_year_id', $yearId)
            ->whereNull('cancelled_at')
            ->select('from_class_id', 'action', DB::raw('count(*) as count'))
            ->groupBy('from_class_id', 'action')
            ->get()
            ->groupBy('from_class_id');

        $classes = StudentClass::all()->keyBy('id');

        $data = [];

        foreach ($results as $classId => $stats) {
            $total = AssignStudent::where('year_id', $yearId)->where('class_id', $classId)->count();

            $data[] = [
                'class_name' => $classes[$classId]->name ?? 'Classe inconnue',
                'promoted'   => $stats->where('action', 'promote')->first()->count ?? 0,
                'repeated'   => $stats->where('action', 'repeat')->first()->count ?? 0,
                'excluded'   => $stats->where('action', 'exclude')->first()->count ?? 0,
                'total'      => $total
            ];
        }

        return $data;
    }
}