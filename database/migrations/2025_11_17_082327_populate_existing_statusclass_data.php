<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use App\Models\AssignStudent;
use App\Models\StudentPromotionHistory;
use App\Models\User;
use App\Models\StudentClass;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $this->initializeStatusForAllAssignments();
        });

        Log::info('✅ Migration des statuts terminée');
    }

    private function initializeStatusForAllAssignments(): void
    {
        // Récupérer tous les élèves distincts inscrits
        $studentIds = AssignStudent::distinct()->pluck('student_id');

        foreach ($studentIds as $studentId) {
            $this->processStudentHistory($studentId);
        }
    }

    private function processStudentHistory(int $studentId): void
    {
        // Récupérer toutes les affectations de cet élève, triées par année puis par ID
        $assignments = AssignStudent::where('student_id', $studentId)
            ->orderBy('year_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $previousClassLevel = null;

        foreach ($assignments as $index => $assignment) {
            // ✅ PREMIÈRE AFFECTATION → Toujours 'N' (Nouveau)
            if ($index === 0) {
                $assignment->update(['statusclass' => 'N']);
                Log::info("Student {$studentId} - Première affectation → N");

                $currentClass = StudentClass::find($assignment->class_id);
                $previousClassLevel = $currentClass ? $currentClass->level : 0;
                continue;
            }

            // ✅ AFFECTATIONS SUIVANTES
            $currentClass = StudentClass::find($assignment->class_id);
            $currentLevel = $currentClass ? $currentClass->level : 0;

            // Vérifier s'il y a une décision de promotion enregistrée
            $promotionHistory = StudentPromotionHistory::where('student_id', $studentId)
                ->where('to_year_id', $assignment->year_id)
                ->where('to_class_id', $assignment->class_id)
                ->whereNull('cancelled_at')
                ->first();

            if ($promotionHistory) {
                // ✅ CAS 1 : Promotion enregistrée
                $status = ($promotionHistory->action === 'repeat') ? 'D' : 'N';
                Log::info("Student {$studentId} - Promotion trouvée: {$promotionHistory->action} → {$status}");
            } else {
                // ✅ CAS 2 : Pas de promotion enregistrée, déduire du niveau de classe
                if ($currentLevel > $previousClassLevel) {
                    $status = 'N'; // Niveau supérieur → Promu → Nouveau
                    Log::info("Student {$studentId} - Niveau supérieur (Prev:{$previousClassLevel}, Curr:{$currentLevel}) → N");
                } elseif ($currentLevel === $previousClassLevel) {
                    $status = 'D'; // Même niveau → Redoublant
                    Log::info("Student {$studentId} - Même niveau ({$currentLevel}) → D");
                } else {
                    $status = 'N'; // Niveau inférieur (rare : réorientation)
                    Log::info("Student {$studentId} - Niveau inférieur → N (réorientation)");
                }
            }

            $assignment->update(['statusclass' => $status]);
            $previousClassLevel = $currentLevel;
        }
    }

    public function down(): void
    {
        AssignStudent::query()->update(['statusclass' => null]);
        Log::info('❌ Statuts réinitialisés');
    }
};