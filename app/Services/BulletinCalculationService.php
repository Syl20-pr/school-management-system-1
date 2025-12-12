<?php

namespace App\Services;

use App\Models\{
    StudentYear, StudentClass, TermType, StudentMarks,
    AssignSubject, AssignSubjectTeach, AssignStudent, User, SchoolSubject, ExamType, StudentAbsence
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\DataObjects\BulletinData;

class BulletinCalculationService
{
    protected $rankingService;

    public function __construct(StudentRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    /* public function calculateClassBulletin($yearId, $classId, $termTypeId)
    {
        return DB::transaction(function () use ($yearId, $classId, $termTypeId) {
            // ✅ OPTIMISATION : Cache des données de base
            $cacheKey = "bulletin_data_{$yearId}_{$classId}_{$termTypeId}";
            
            return Cache::remember($cacheKey, 3600, function() use ($yearId, $classId, $termTypeId) {
                $year = StudentYear::findOrFail($yearId);
                $class = StudentClass::findOrFail($classId);
                $termType = TermType::findOrFail($termTypeId);
                
                $bulletinData = new BulletinData($year, $class, $termType);
                
                // ✅ OPTIMISATION : Chargement groupé des données
                $studentsData = $this->getAllStudentsDataOptimized($yearId, $classId, $termTypeId);
                $subjects = $this->getClassSubjectsWithCache($classId);
                
                $principalTeacher = $this->getPrincipalTeacher($class->name);
                $bulletinData->setPrincipalTeacher($principalTeacher);
                
                // ✅ OPTIMISATION : Traitement par lots
                $studentResults = [];
                foreach ($studentsData as $studentId => $studentData) {
                    $studentResults[] = $this->calculateStudentDataOptimized(
                        $studentData, 
                        $subjects, 
                        $termTypeId,
                        $yearId,
                        $classId
                    );
                }
                
                // Ajouter tous les étudiants d'un coup
                foreach ($studentResults as $studentData) {
                    $bulletinData->addStudent($studentData);
                }
                
                // Calcul des rangs et statistiques
                $this->rankingService->calculateRanksAndStatistics($bulletinData);
                
                return $bulletinData;
            });
        });
    } */
    public function calculateClassBulletin($yearId, $classId, $termTypeId)
    {
        //\Log::info("🔍 CALCUL BULLETIN - Début - Year: $yearId, Class: $classId, Term: $termTypeId");
        
        return DB::transaction(function () use ($yearId, $classId, $termTypeId) {
            try {
                //\Log::info("✅ Transaction DB démarrée");
                
                $cacheKey = "bulletin_data_{$yearId}_{$classId}_{$termTypeId}";
                //\Log::info("🔑 Cache key: " . $cacheKey);
                
                return Cache::remember($cacheKey, 3600, function() use ($yearId, $classId, $termTypeId) {
                   // \Log::info("🔄 Calcul sans cache - Récupération des données de base");
                    
                    $year = StudentYear::findOrFail($yearId);
                    $class = StudentClass::findOrFail($classId);
                    $termType = TermType::findOrFail($termTypeId);
                    
                    //\Log::info("✅ Modèles chargés - Year: {$year->name}, Class: {$class->name}, Term: {$termType->name}");
                    
                    $bulletinData = new BulletinData($year, $class, $termType);
                    //\Log::info("✅ BulletinData créé");
                    
                    //\Log::info("📊 Chargement des données étudiants...");
                    $studentsData = $this->getAllStudentsDataOptimized($yearId, $classId, $termTypeId);
                    //\Log::info("✅ Données étudiants chargées: " . count($studentsData) . " étudiants");
                    
                    $subjects = $this->getClassSubjectsWithCache($classId);
                    //\Log::info("✅ Matières chargées: " . count($subjects) . " matières");
                    
                    $principalTeacher = $this->getPrincipalTeacher($class->name);
                    $bulletinData->setPrincipalTeacher($principalTeacher);
                    //\Log::info("✅ Professeur principal: " . $principalTeacher);
                    
                    //\Log::info("🧮 Calcul des données par étudiant...");
                    $studentResults = [];
                    foreach ($studentsData as $studentId => $studentData) {
                    // \Log::info("   👤 Calcul étudiant ID: " . $studentId);
                        $studentResults[] = $this->calculateStudentDataOptimized(
                            $studentData, 
                            $subjects, 
                            $termTypeId,
                            $yearId,
                            $classId
                        );
                    }
                    //\Log::info("✅ Données étudiants calculées: " . count($studentResults) . " résultats");
                    
                    // Ajouter tous les étudiants d'un coup
                    foreach ($studentResults as $studentData) {
                        $bulletinData->addStudent($studentData);
                    }
                    //\Log::info("✅ Étudiants ajoutés à BulletinData");

                    // Calcul des rangs et statistiques
                    /* \Log::info("🏆 Calcul des rangs et statistiques...");
                    $this->rankingService->calculateRanksAndStatistics($bulletinData);
                    \Log::info("✅ Rangs et statistiques calculés");
                    
                    \Log::info("🎉 CALCUL BULLETIN TERMINÉ AVEC SUCCÈS");
                    return $bulletinData; */
                    //\Log::info("🏆 Calcul des rangs et statistiques...");
                    $this->rankingService->calculateRanksAndStatistics($bulletinData);
                    //\Log::info("✅ Rangs et statistiques calculés");

                    // ✅ Ajouter les matières dans BulletinData
                    if (method_exists($bulletinData, 'setSubjects')) {
                        $bulletinData->setSubjects($subjects);
                        //\Log::info("✅ Matières ajoutées à BulletinData via setSubjects()");
                    } else {
                        // Si la classe n’a pas de setter, on assigne directement
                        $bulletinData->subjects = $subjects;
                        //\Log::info("✅ Matières ajoutées à BulletinData (attribut direct)");
                    }

                    //\Log::info("🎉 CALCUL BULLETIN TERMINÉ AVEC SUCCÈS");
                    return $bulletinData;

                });
                
            } catch (\Exception $e) {
                \Log::error("❌ ERREUR dans calculateClassBulletin:");
                \Log::error("Message: " . $e->getMessage());
                \Log::error("Fichier: " . $e->getFile());
                \Log::error("Ligne: " . $e->getLine());
                throw $e; // Important: relancer l'exception
            }
        });
    }
    
    /**
     * ✅ OPTIMISATION CRITIQUE : Chargement de TOUTES les données en une seule requête
     */
   /**
 * ✅ CORRECTION : Chargement des données sans relations problématiques
 */
    /* protected function getAllStudentsDataOptimized($yearId, $classId, $termTypeId)
    {
        $cacheKey = "students_marks_{$yearId}_{$classId}_{$termTypeId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($yearId, $classId, $termTypeId) {
            Log::info("🔍 Chargement des données sans relations problématiques");

            // 1. Récupérer toutes les notes SANS eager loading problématique
            $allMarks = StudentMarks::where('year_id', $yearId)
                ->where('class_id', $classId)
                ->where('term_type_id', $termTypeId)
                ->get();
                
            Log::info("📊 " . $allMarks->count() . " notes trouvées");

            // 2. Récupérer les étudiants
            $studentIds = $allMarks->pluck('student_id')->unique();
            $students = User::whereIn('id', $studentIds)
                ->get(['id', 'name', 'gender', 'statusclass'])
                ->keyBy('id');
                
            Log::info("👥 " . $students->count() . " étudiants trouvés");

            // 3. Récupérer les types d'examen séparément
            $examTypeIds = $allMarks->pluck('exam_type_id')->unique();
            $examTypes = ExamType::whereIn('id', $examTypeIds)->get()->keyBy('id');
            Log::info("📝 " . $examTypes->count() . " types d'examen trouvés");

            // 4. Récupérer les matières
            $assignSubjectIds = $allMarks->pluck('assign_subject_id')->filter()->unique();
            $assignSubjects = AssignSubject::whereIn('id', $assignSubjectIds)
                ->with('school_subject')
                ->get()
                ->keyBy('id');
            Log::info("📚 " . $assignSubjects->count() . " matières trouvées");

            // 5. Organiser les données
            $studentsData = [];
            foreach ($studentIds as $studentId) {
                if (isset($students[$studentId])) {
                    $studentsData[$studentId] = [
                        'student' => $students[$studentId],
                        'marks' => $allMarks->where('student_id', $studentId)->groupBy('assign_subject_id'),
                        'group' => AssignStudent::where('student_id', $studentId)->first(),
                        'examTypes' => $examTypes, // ✅ On passe les examTypes séparément
                        'assignSubjects' => $assignSubjects // ✅ On passe les matières séparément
                    ];
                }
            }

            Log::info("✅ Données organisées pour " . count($studentsData) . " étudiants");
            return $studentsData;
        });
    } */
   /* protected function getAllStudentsDataOptimized($yearId, $classId, $termTypeId)
    {
        \Log::info("🔍 GET ALL STUDENTS DATA - Year: $yearId, Class: $classId, Term: $termTypeId");
        
        $cacheKey = "students_marks_{$yearId}_{$classId}_{$termTypeId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($yearId, $classId, $termTypeId) {
            try {
                \Log::info("🔄 Chargement sans cache des données étudiants...");

                // 1. Récupérer toutes les notes
                \Log::info("📊 Récupération des notes...");
                $allMarks = StudentMarks::where('year_id', $yearId)
                    ->where('class_id', $classId)
                    ->where('term_type_id', $termTypeId)
                    ->get();
                    
                \Log::info("✅ " . $allMarks->count() . " notes trouvées");

                // 2. Récupérer les étudiants
                $studentIds = $allMarks->pluck('student_id')->unique();
                \Log::info("👥 Student IDs: " . $studentIds->count() . " étudiants");
                
                $students = User::whereIn('id', $studentIds)
                    ->get(['id', 'name', 'gender', 'statusclass'])
                    ->keyBy('id');
                    
                \Log::info("✅ " . $students->count() . " étudiants chargés");

                // ... le reste de votre code ...

                \Log::info("🎉 DONNÉES ÉTUDIANTS PRÊTES: " . count($studentsData) . " étudiants");
                return $studentsData;
                
            } catch (\Exception $e) {
                \Log::error("❌ ERREUR dans getAllStudentsDataOptimized:");
                \Log::error("Message: " . $e->getMessage());
                \Log::error("Fichier: " . $e->getFile());
                \Log::error("Ligne: " . $e->getLine());
                throw $e;
            }
        });
    } */

   /* protected function getAllStudentsDataOptimized($yearId, $classId, $termTypeId)
    {
        \Log::info("🔍 GET ALL STUDENTS DATA - Year: $yearId, Class: $classId, Term: $termTypeId");
        
        $cacheKey = "students_marks_{$yearId}_{$classId}_{$termTypeId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($yearId, $classId, $termTypeId) {
            try {
                \Log::info("🔄 Chargement sans cache des données étudiants...");

                // 1. Récupérer toutes les notes
                \Log::info("📊 Récupération des notes...");
                $allMarks = StudentMarks::where('year_id', $yearId)
                    ->where('class_id', $classId)
                    ->where('term_type_id', $termTypeId)
                    ->get();
                    
                \Log::info("✅ " . $allMarks->count() . " notes trouvées");

                // 2. Récupérer les étudiants
                $studentIds = $allMarks->pluck('student_id')->unique();
                \Log::info("👥 Student IDs: " . $studentIds->count() . " étudiants");
                
                $students = User::whereIn('id', $studentIds)
                    ->get(['id', 'name', 'gender', 'statusclass'])
                    ->keyBy('id');
                    
                \Log::info("✅ " . $students->count() . " étudiants chargés");

                // 3. Récupérer les types d'examen séparément
                $examTypeIds = $allMarks->pluck('exam_type_id')->unique();
                $examTypes = ExamType::whereIn('id', $examTypeIds)->get()->keyBy('id');
                \Log::info("📝 " . $examTypes->count() . " types d'examen trouvés");

                // 4. Récupérer les matières
                $assignSubjectIds = $allMarks->pluck('assign_subject_id')->filter()->unique();
                $assignSubjects = AssignSubject::whereIn('id', $assignSubjectIds)
                    ->with('school_subject')
                    ->get()
                    ->keyBy('id');
                \Log::info("📚 " . $assignSubjects->count() . " matières trouvées");

                // 5. Organiser les données - CORRECTION CRITIQUE ICI
                $studentsData = []; // ← INITIALISATION MANQUANTE
                
                foreach ($studentIds as $studentId) {
                    if (isset($students[$studentId])) {
                        $studentsData[$studentId] = [
                            'student' => $students[$studentId],
                            'marks' => $allMarks->where('student_id', $studentId)->groupBy('assign_subject_id'),
                            'group' => AssignStudent::where('student_id', $studentId)->first(),
                            'examTypes' => $examTypes,
                            'assignSubjects' => $assignSubjects
                        ];
                    }
                }

                \Log::info("🎉 DONNÉES ÉTUDIANTS PRÊTES: " . count($studentsData) . " étudiants");
                return $studentsData;
                
            } catch (\Exception $e) {
                \Log::error("❌ ERREUR dans getAllStudentsDataOptimized:");
                \Log::error("Message: " . $e->getMessage());
                \Log::error("Fichier: " . $e->getFile());
                \Log::error("Ligne: " . $e->getLine());
                throw $e;
            }
        });
    } */
   protected function getAllStudentsDataOptimized($yearId, $classId, $termTypeId)
{
    //\Log::info("🔍 GET ALL STUDENTS DATA OPTIMIZED - Year: $yearId, Class: $classId, Term: $termTypeId");
    
    $cacheKey = "students_marks_{$yearId}_{$classId}_{$termTypeId}";
    
    return Cache::remember($cacheKey, 1800, function() use ($yearId, $classId, $termTypeId) {
        try {
            //\Log::info("🔄 Chargement des données depuis la base...");

            // 1. Récupérer TOUTES les notes pour cette classe/année/terme
            $allMarks = StudentMarks::where('year_id', $yearId)
                ->where('class_id', $classId)
                ->where('term_type_id', $termTypeId)
                ->get();
                
            //\Log::info("📊 " . $allMarks->count() . " notes trouvées dans la base");

            if ($allMarks->isEmpty()) {
                \Log::warning("⚠️ AUCUNE NOTE trouvée pour Year:$yearId, Class:$classId, Term:$termTypeId");
                return [];
            }

            // 2. Récupérer les étudiants qui ont des notes
            $studentIds = $allMarks->pluck('student_id')->unique();
            \Log::info("👥 " . $studentIds->count() . " étudiants avec des notes");

            $students = User::whereIn('id', $studentIds)
                ->get(['id', 'name', 'gender', 'statusclass'])
                ->keyBy('id');
                
            \Log::info("✅ " . $students->count() . " étudiants chargés");

            // 3. ✅ CORRECTION CRITIQUE : Récupérer les matières de la CLASSE
            $classSubjects = AssignSubject::where('class_id', $classId)
                ->with('school_subject:id,name')
                ->get()
                ->keyBy('subject_id');
                
            \Log::info("📚 Matières de la classe: " . $classSubjects->count() . " matières trouvées");

            // Debug: afficher les matières trouvées
            foreach ($classSubjects as $subject) {
                \Log::info("   📖 Matière: " . ($subject->school_subject->name ?? 'N/A') . " (ID:{$subject->id}, Subject ID:{$subject->subject_id})");
            }

            // 4. Récupérer les assign_subject_id utilisés dans les notes
            $usedAssignSubjectIds = $allMarks->pluck('assign_subject_id')->unique();
            //\Log::info("🎯 AssignSubject IDs utilisés dans les notes: " . $usedAssignSubjectIds->implode(', '));

            // 5. Vérifier la correspondance entre les matières de la classe et celles utilisées dans les notes
            $matchingSubjects = $classSubjects->whereIn('subject_id', $usedAssignSubjectIds);
            //\Log::info("🔗 Correspondances trouvées: " . $matchingSubjects->count() . " matières");

            if ($matchingSubjects->isEmpty()) {
                \Log::error("❌ AUCUNE CORRESPONDANCE entre les matières de la classe et les assign_subject_id des notes!");
                //\Log::error("   AssignSubject IDs dans notes: " . $usedAssignSubjectIds->implode(', '));
                //\Log::error("   AssignSubject IDs de la classe: " . $classSubjects->pluck('id')->implode(', '));
            }

            // 6. Récupérer les types d'examen
            $examTypeIds = $allMarks->pluck('exam_type_id')->unique();
            $examTypes = ExamType::whereIn('id', $examTypeIds)->get()->keyBy('id');
            //\Log::info("📝 " . $examTypes->count() . " types d'examen trouvés");

            // 7. Organiser les données par étudiant
            $studentsData = [];
            
            foreach ($studentIds as $studentId) {
                if (!isset($students[$studentId])) {
                    continue;
                }

                // Récupérer toutes les notes de cet étudiant
                $studentMarks = $allMarks->where('student_id', $studentId);
                
                // Grouper par assign_subject_id
                $marksBySubject = $studentMarks->groupBy('assign_subject_id');
                
                $studentsData[$studentId] = [
                    'student' => $students[$studentId],
                    'marks' => $marksBySubject,
                    'group' => AssignStudent::where('student_id', $studentId)->first(),
                    'examTypes' => $examTypes,
                    'assignSubjects' => $classSubjects // ✅ Utiliser les matières de la classe
                ];
            }

            //\Log::info("🎉 Données organisées pour " . count($studentsData) . " étudiants");
            
            // Debug du premier étudiant
            if (!empty($studentsData)) {
                $firstStudentId = array_key_first($studentsData);
                $firstStudentData = $studentsData[$firstStudentId];
                //\Log::info("🔍 PREMIER ÉTUDIANT - ID: $firstStudentId, Nom: " . $firstStudentData['student']->name);
                //\Log::info("   📚 Matières avec notes: " . $firstStudentData['marks']->count());
                
                foreach ($firstStudentData['marks'] as $assignSubjectId => $marks) {
                    $subjectName = $classSubjects[$assignSubjectId]->school_subject->name ?? "Inconnu (ID:$assignSubjectId)";
                   // \Log::info("      📖 $subjectName - Notes: " . $marks->count());
                }
            }

            return $studentsData;
            
        } catch (\Exception $e) {
            \Log::error("❌ ERREUR dans getAllStudentsDataOptimized:");
            \Log::error("Message: " . $e->getMessage());
            \Log::error("Fichier: " . $e->getFile());
            \Log::error("Ligne: " . $e->getLine());
            throw $e;
        }
    });
}
    
    /**
     * ✅ OPTIMISATION : Cache des matières
     */
    protected function getClassSubjectsWithCache($classId)
    {
        $cacheKey = "class_subjects_{$classId}";
        
        return Cache::remember($cacheKey, 86400, function() use ($classId) { // 24 heures
            return AssignSubject::where('class_id', $classId)
                ->with(['school_subject:id,name'])
                ->get(['id', 'subject_id', 'class_id', 'subjective_mark']);
        });
    }
    
    /**
     * ✅ OPTIMISATION : Calcul optimisé des données étudiant
     */
    ////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// MISE A JOUR calculateStudentDataOptimized////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////
    protected function calculateStudentDataOptimized($studentData, $subjects, $termTypeId, $yearId, $classId)
    {
        $student = $studentData['student'];
        $studentMarks = $studentData['marks'];
        $group = $studentData['group'];
        $group_id = $group ? $group->group_id : null;

        $subjectData = [];
        $totalWeightedSum = 0;
        $totalCoefficientSum = 0;

        foreach ($subjects as $subject) {
            // Vérifier si la matière doit être sautée selon le groupe
            if (($group_id == 5 && $subject->subject_id == 10) || ($group_id == 4 && $subject->subject_id == 17)) {
                continue;
            }

            // ✅ OPTIMISATION : Récupération des notes depuis les données préchargées
            $subjectMarks = $this->calculateSubjectMarksOptimized(
                $studentMarks->get($subject->subject_id, collect()), 
                $termTypeId
            );

            $moyenneClasse = $subjectMarks['moyenne_classe'];
            $moyenneCompo = $subjectMarks['moyenne_compo'];
            $averageMarks = $subjectMarks['moyenne_finale'];

            $subjectiveMarkCoefficient = $subject->subjective_mark ?? 0;
            $isEPS = optional($subject->school_subject)->name === 'E.P.S';
            $isInapte = $this->isStudentInapteOptimized($studentMarks->get($subject->id, collect()));

            if ($isEPS && $isInapte) {
                $subjectMarks = [
                    'moyenne_classe' => 'Disp.',
                    'moyenne_compo' => 'Disp.',
                    'moyenne_finale' => 'Disp.',
                    'notes_interros' => 'Disp.',
                    'notes_devoirs' => 'Disp.',
                    'notes_compo' => 'Disp.',
                ];
                $averageMarks = 'Disp.';
                $weightedAverage = 'Disp.';
                $rank = 'Disp.';
                $appreciation = 'Dispensé(e)';
                $subjectiveMarkCoefficient = '--';
            } else {
                $weightedAverage = $subjectiveMarkCoefficient * ($averageMarks ?? 0);
                $rank = null;
                $appreciation = $this->getAppreciation($averageMarks ?? 0);

                if ($averageMarks !== null && $averageMarks !== 'Disp.' && is_numeric($averageMarks)) {
                    $totalWeightedSum += $weightedAverage;
                    $totalCoefficientSum += $subjectiveMarkCoefficient;
                }
            }

            $teacher = $this->getSubjectTeacher($subject->id, $yearId, $classId);

            $subjectData[] = [
                'subject_id' => $subject->subject_id,
                'subject_name' => optional($subject->school_subject)->name ?? 'N/A',
                'moyenne_classe' => $subjectMarks['moyenne_classe'],
                'moyenne_compo' => $subjectMarks['moyenne_compo'],
                'average_marks' => $subjectMarks['moyenne_finale'],
                'exam_marks' => [
                    'notes_interros' => $subjectMarks['notes_interros'],
                    'notes_devoirs' => $subjectMarks['notes_devoirs'],
                    'notes_compo' => $subjectMarks['notes_compo'],
                ],
                'subjective_mark_coefficient' => $subjectiveMarkCoefficient,
                'weighted_average' => $weightedAverage,
                'rank' => $rank,
                'assigned_teacher' => $teacher,
                'appreciation' => $appreciation,
            ];
        }

        // Calcul de la moyenne générale
        $termMean = $totalCoefficientSum > 0 ? $totalWeightedSum / $totalCoefficientSum : 0;
        $termMeanWords = $this->convertDecimalToWords($termMean);

        // Cycle & appréciation
        $cycle1Classes = ['6ème A','6ème B','6ème C','6ème D',
                        '5ème A','5ème B','5ème C','5ème D',
                        '4ème A','4ème B','4ème C','4ème D',
                        '3ème A','3ème B','3ème C','3ème D','3ème E'];

        $className = StudentClass::find($classId)->name;
        $isCycle1 = in_array($className, $cycle1Classes);
        $isFinalTerm = ($isCycle1 && $termTypeId == 5) || (!$isCycle1 && $termTypeId == 7);

        // ✅ OPTIMISATION : Cache des termes précédents
        $previousTerms = $this->getPreviousTermsDataOptimized($student->id, $yearId, $classId, $termTypeId);
        $annualMean = $this->calculateAnnualMeanOptimized($student->id, $yearId, $classId, $termTypeId);

        $appreciationTerm = ($isFinalTerm && isset($annualMean['mean']) && is_numeric($annualMean['mean']))
            ? ($annualMean['appreciation'] ?? $this->getAppreciationTermMean($annualMean['mean']))
            : $this->getAppreciationTermMean($termMean);
        
        // ✅ AJOUT : Récupération des absences ICI (AVANT le return)
        $absences = $this->getStudentAbsences($student->id, $yearId, $classId, $termTypeId);

        return [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'gender' => $student->gender,
            //'statusclass' => $student->statusclass,
            'statusclass' => $group->statusclass ?? $student->statusclass,
            'subjects' => $subjectData,
            'term_mean' => $termMean,
            'term_mean_words' => $termMeanWords,
            'appreciation_term' => $appreciationTerm,
            'is_abandon' => $termMean < 3,
            'term_rank' => null,
            'previous_terms' => $previousTerms,
            'annual_mean' => $annualMean,
            'absences' => $absences, // ✅ LIGNE AJOUTÉE ICI
        ];
    }
    ////////////////////////////////////FIN////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * ✅ OPTIMISATION : Calcul des notes depuis données préchargées
     */
    ////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////// calculateSubjectMarksOptimized///////////////////////////////////////
    protected function findExamTypesByTerm($termTypeId) { 
       // \Log::info("🔍 Recherche des ExamTypes pour le terme: $termTypeId"); 
        $assignedExamTypes = \App\Models\AssignExamType::where('term_type_id', $termTypeId) ->with('exam_type') ->get(); 
        if ($assignedExamTypes->isNotEmpty()) { 
           // \Log::info("✅ ExamTypes trouvés via assign_exam_types"); 
            return $this->extractExamTypesFromAssignment($assignedExamTypes); 
        } 
        // ✅ Retour explicite si rien trouvé
        return [
            'interro' => null,
            'devoir' => null,
            'compo' => null,
        ];
    }

    protected function extractExamTypesFromAssignment($assignedExamTypes)
    {
        $result = ['interro' => null, 'devoir' => null, 'compo' => null];
        
        foreach ($assignedExamTypes as $assignment) {
            $examTypeName = strtolower($assignment->exam_type->name);
            
            if (str_contains($examTypeName, 'interrogation') || str_contains($examTypeName, 'interro')) {
                $result['interro'] = $assignment->exam_type_id;
               // \Log::info("✅ Interro trouvée: {$assignment->exam_type->name} (ID:{$assignment->exam_type_id})");
            }
            elseif (str_contains($examTypeName, 'devoir')) {
                $result['devoir'] = $assignment->exam_type_id;
                //\Log::info("✅ Devoir trouvé: {$assignment->exam_type->name} (ID:{$assignment->exam_type_id})");
            }
            elseif (str_contains($examTypeName, 'compo') || str_contains($examTypeName, 'composition')) {
                $result['compo'] = $assignment->exam_type_id;
               // \Log::info("✅ Compo trouvée: {$assignment->exam_type->name} (ID:{$assignment->exam_type_id})");
            }
        }

        return $result;
    }

    protected function calculateSubjectMarksOptimized($marksCollection, $termTypeId)
    {
        //\Log::info("🔍 CALCUL NOTES - Terme: $termTypeId, Notes: " . $marksCollection->count());
        
        if ($marksCollection->isEmpty()) {
            return $this->getEmptyMarksResult();
        }

        // ✅ RECHERCHE DYNAMIQUE des ExamTypes
        $examTypeIds = $this->findExamTypesByTerm($termTypeId);
        
        $interroId = $examTypeIds['interro'];
        $devoirId = $examTypeIds['devoir']; 
        $compoId = $examTypeIds['compo'];

        // ✅ VÉRIFICATION que tous les ExamTypes sont trouvés
        if (!$interroId || !$devoirId || !$compoId) {
            //\Log::error("❌ ExamTypes manquants - Interro: $interroId, Devoir: $devoirId, Compo: $compoId");
            
            // Fallback: utiliser les IDs des notes disponibles
            return $this->calculateWithAvailableMarks($marksCollection);
        }

        //\Log::info("🎯 ExamTypes dynamiques - Interro:$interroId, Devoir:$devoirId, Compo:$compoId");

        // Récupération et calcul des notes (identique)
        $noteInterro = $marksCollection->firstWhere('exam_type_id', $interroId);
        $noteDevoir = $marksCollection->firstWhere('exam_type_id', $devoirId);
        $noteCompo = $marksCollection->firstWhere('exam_type_id', $compoId);

        //\Log::info("📊 Notes trouvées - Interro: " . ($noteInterro ? $noteInterro->marks : 'N/A') . 
        //        ", Devoir: " . ($noteDevoir ? $noteDevoir->marks : 'N/A') . 
        //        ", Compo: " . ($noteCompo ? $noteCompo->marks : 'N/A'));

        return $this->calculateMarks($noteInterro, $noteDevoir, $noteCompo);
    }

    protected function getEmptyMarksResult()
    {
        return [
            'moyenne_classe' => null,
            'moyenne_compo' => null,
            'moyenne_finale' => null,
            'notes_interros' => null,
            'notes_devoirs' => null,
            'notes_compo' => null,
        ];
    }

    protected function calculateWithAvailableMarks($marksCollection)
    {
        //\Log::warning("⚠️ Utilisation du fallback avec notes disponibles");
        
        // Prendre les 3 premiers ExamTypes disponibles
        $availableExamTypes = $marksCollection->pluck('exam_type_id')->unique()->take(3)->values();
        
        $note1 = $marksCollection->firstWhere('exam_type_id', $availableExamTypes[0] ?? null);
        $note2 = $marksCollection->firstWhere('exam_type_id', $availableExamTypes[1] ?? null);
        $note3 = $marksCollection->firstWhere('exam_type_id', $availableExamTypes[2] ?? null);

        //\Log::info("📊 Fallback - Notes disponibles: " . $availableExamTypes->implode(','));

        return $this->calculateMarks($note1, $note2, $note3);
    }

    protected function calculateMarks($noteInterro, $noteDevoir, $noteCompo)
    {
        // Extraction des valeurs
        $noteInterroValue = $noteInterro->marks ?? null;
        $noteDevoirValue = $noteDevoir->marks ?? null;
        $noteCompoValue = $noteCompo->marks ?? null;

        // Traitement des 0
        //$noteInterroValue = ($noteInterroValue === 0.0 || $noteInterroValue === 0) ? null : $noteInterroValue;
        //$noteDevoirValue = ($noteDevoirValue === 0.0 || $noteDevoirValue === 0) ? null : $noteDevoirValue;
        //$noteCompoValue = ($noteCompoValue === 0.0 || $noteCompoValue === 0) ? null : $noteCompoValue;

        // Calcul des moyennes
        $moyenneClasse = null;
        if ($noteInterroValue !== null && $noteDevoirValue !== null) {
            $moyenneClasse = ($noteInterroValue + $noteDevoirValue) / 2;
        } elseif ($noteInterroValue !== null) {
            $moyenneClasse = $noteInterroValue;
        } elseif ($noteDevoirValue !== null) {
            $moyenneClasse = $noteDevoirValue;
        }

        $moyenneFinale = null;
        if ($moyenneClasse !== null && $noteCompoValue !== null) {
            $moyenneFinale = ($moyenneClasse + $noteCompoValue) / 2;
        //} elseif ($moyenneClasse !== null) {
        //    $moyenneFinale = $moyenneClasse;
        //} elseif ($noteCompoValue !== null) {
        //    $moyenneFinale = $noteCompoValue;
        }

        //\Log::info("📈 CALCUL TERMINÉ - Moy.Classe: " . ($moyenneClasse ?? 'N/A') . 
         //       ", Moy.Finale: " . ($moyenneFinale ?? 'N/A'));

        return [
            'moyenne_classe' => $moyenneClasse,
            'moyenne_compo' => $noteCompoValue,
            'moyenne_finale' => $moyenneFinale,
            'notes_interros' => $noteInterroValue,
            'notes_devoirs' => $noteDevoirValue,
            'notes_compo' => $noteCompoValue,
        ];
    }
 ///////////////////////////////////////FIN MISE A JOUR//////////////////////////////////////////////////
 /////////////////////////////////////////////////////////////////////////////////////////////////   
    /**
     * ✅ OPTIMISATION : Vérification inapte depuis données préchargées
     */
    protected function isStudentInapteOptimized($marksCollection)
    {
        return $marksCollection->contains('inapte', 1);
    }

    /**
     * ✅ OPTIMISATION : Cache des données des termes précédents
     */
    protected function getPreviousTermsDataOptimized($studentId, $yearId, $classId, $currentTermTypeId)
    {
        $cacheKey = "previous_terms_{$studentId}_{$yearId}_{$classId}";
        
        return Cache::remember($cacheKey, 3600, function() use ($studentId, $yearId, $classId, $currentTermTypeId) {
            $previousTerms = [];
            $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                            '5ème A', '5ème B', '5ème C', '5ème D', 
                            '4ème A', '4ème B', '4ème C', '4ème D', 
                            '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];

            $className = StudentClass::find($classId)->name;
            $isCycle1 = in_array($className, $cycle1Classes);
            $termIds = $isCycle1 ? [1, 4, 5] : [6, 7];

            foreach ($termIds as $termId) {
                if ($termId >= $currentTermTypeId) continue;

                $termMean = $this->calculateStudentTermMeanOptimized($studentId, $yearId, $classId, $termId);
                $termRank = $this->calculateStudentTermRankOptimized($studentId, $yearId, $classId, $termId);

                $termName = TermType::find($termId)->name ?? "Terme $termId";
                $previousTerms[$termName] = [
                    'mean' => $termMean,
                    'rank' => $termRank
                ];
            }

            return $previousTerms;
        });
    }

    /**
     * ✅ OPTIMISATION : Calcul de la moyenne du terme avec cache
     */
    protected function calculateStudentTermMeanOptimized($studentId, $yearId, $classId, $termTypeId)
    {
        $cacheKey = "term_mean_{$studentId}_{$yearId}_{$classId}_{$termTypeId}";
        
        return Cache::remember($cacheKey, 3600, function() use ($studentId, $yearId, $classId, $termTypeId) {
            $subjects = $this->getClassSubjectsWithCache($classId);
            $totalWeightedSum = 0;
            $totalCoefficientSum = 0;
            
            $group = AssignStudent::where('student_id', $studentId)->first();
            $group_id = $group ? $group->group_id : null;

            foreach ($subjects as $subject) {
                if (($group_id == 5 && $subject->subject_id == 10) || ($group_id == 4 && $subject->subject_id == 17)) {
                    continue;
                }
                
                $subjectMarks = $this->calculateSubjectMarksForTerm($studentId, $yearId, $classId, $subject->subject_id, $termTypeId);
                $averageMarks = $subjectMarks['moyenne_finale'];

                if (is_numeric($averageMarks)) {
                    $subjectiveMarkCoefficient = $subject->subjective_mark ?? 0;
                    $totalWeightedSum += $subjectiveMarkCoefficient * $averageMarks;
                    $totalCoefficientSum += $subjectiveMarkCoefficient;
                }
            }

            if ($totalCoefficientSum > 0) {
                return round($totalWeightedSum / $totalCoefficientSum, 2);
            }

            return null;
        });
    }

    /**
     * ✅ OPTIMISATION : Calcul du rang du terme avec cache
     */
    /* protected function calculateStudentTermRankOptimized($studentId, $yearId, $classId, $termTypeId)
    {
        $cacheKey = "term_rank_{$yearId}_{$classId}_{$termTypeId}";
        
        $allRanks = Cache::remember($cacheKey, 3600, function() use ($yearId, $classId, $termTypeId) {
            $students = User::whereHas('studentMarks', function($query) use ($yearId, $classId, $termTypeId) {
                $query->where('year_id', $yearId)
                      ->where('class_id', $classId)
                      ->where('term_type_id', $termTypeId);
            })->get(['id']);

            $studentMeans = [];
            foreach ($students as $student) {
                $mean = $this->calculateStudentTermMeanOptimized($student->id, $yearId, $classId, $termTypeId);
                if (is_numeric($mean)) {
                    $studentMeans[$student->id] = $mean;
                }
            }
            
            arsort($studentMeans);
            
            $rank = 1;
            $previousMean = null;
            $sameRankCount = 1;
            $studentRanks = [];
            
            foreach ($studentMeans as $id => $mean) {
                if ($previousMean !== null && abs($mean - $previousMean) > 0.001) {
                    $rank += $sameRankCount;
                    $sameRankCount = 1;
                } else {
                    $sameRankCount++;
                }
                
                $studentRanks[$id] = $rank;
                $previousMean = $mean;
            }
            
            return $studentRanks;
        });
        
        return $allRanks[$studentId] ?? 'N/A';
    } */
   /**
 * ✅ CORRECTION : Calcul du rang du terme avec cache (SANS utiliser la relation)
 */
protected function calculateStudentTermRankOptimized($studentId, $yearId, $classId, $termTypeId)
{
    $cacheKey = "term_rank_{$yearId}_{$classId}_{$termTypeId}";
    
    $allRanks = Cache::remember($cacheKey, 3600, function() use ($yearId, $classId, $termTypeId) {
        // ✅ CORRECTION : Utiliser StudentMarks directement au lieu de la relation User
        $studentIds = StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('term_type_id', $termTypeId)
            ->distinct()
            ->pluck('student_id');

        $studentMeans = [];
        foreach ($studentIds as $id) {
            $mean = $this->calculateStudentTermMeanOptimized($id, $yearId, $classId, $termTypeId);
            if (is_numeric($mean)) {
                $studentMeans[$id] = $mean;
            }
        }
        
        arsort($studentMeans);
        
        $rank = 1;
        $previousMean = null;
        $sameRankCount = 1;
        $studentRanks = [];
        
        foreach ($studentMeans as $id => $mean) {
            if ($previousMean !== null && abs($mean - $previousMean) > 0.001) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }
            
            $studentRanks[$id] = $rank;
            $previousMean = $mean;
        }
        
        return $studentRanks;
    });
    
    return $allRanks[$studentId] ?? 'N/A';
}

    /**
     * ✅ OPTIMISATION : Calcul de la moyenne annuelle avec cache
     */
    protected function calculateAnnualMeanOptimized($studentId, $yearId, $classId, $currentTermTypeId)
    {
        $cacheKey = "annual_mean_{$studentId}_{$yearId}_{$classId}";
        
        return Cache::remember($cacheKey, 3600, function() use ($studentId, $yearId, $classId, $currentTermTypeId) {
            $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                            '5ème A', '5ème B', '5ème C', '5ème D', 
                            '4ème A', '4ème B', '4ème C', '4ème D', 
                            '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
            
            $className = StudentClass::find($classId)->name;
            $isCycle1 = in_array($className, $cycle1Classes);
            
            $canCalculateAnnual = ($isCycle1 && $currentTermTypeId == 5) || (!$isCycle1 && $currentTermTypeId == 7);
            
            if (!$canCalculateAnnual) {
                return ['mean' => 'N/A', 'rank' => 'N/A', 'mean_words' => 'N/A', 'appreciation' => 'N/A'];
            }
            
            $termMeans = [];
            $requiredTermCount = $isCycle1 ? 3 : 2;
            
            if ($isCycle1) {
                $termIds = [1, 4, 5];
                foreach ($termIds as $termId) {
                    $mean = $this->calculateStudentTermMeanOptimized($studentId, $yearId, $classId, $termId);
                    $termMeans[] = $mean;
                }
            } else {
                $termIds = [6, 7];
                foreach ($termIds as $termId) {
                    $mean = $this->calculateStudentTermMeanOptimized($studentId, $yearId, $classId, $termId);
                    $termMeans[] = $mean;
                }
            }
            
            $validMeans = array_filter($termMeans, function($mean) {
                return is_numeric($mean);
            });
            
            if (count($validMeans) !== $requiredTermCount) {
                return ['mean' => 'N/A', 'rank' => 'N/A', 'mean_words' => 'N/A', 'appreciation' => 'N/A'];
            }
            
            $annualMean = array_sum($validMeans) / count($validMeans);
            $annualRank = $this->calculateAnnualRankOptimized($studentId, $yearId, $classId, $isCycle1);
            
            return [
                'mean' => round($annualMean, 2),
                'rank' => $annualRank,
                'mean_words' => $this->convertDecimalToWords($annualMean),
                'appreciation' => $this->getAppreciationTermMean($annualMean)
            ];
        });
    }

    /**
     * ✅ OPTIMISATION : Calcul du rang annuel avec cache
     */
    /* protected function calculateAnnualRankOptimized($studentId, $yearId, $classId, $isCycle1)
    {
        $cacheKey = "annual_rank_{$yearId}_{$classId}";
        
        $allRanks = Cache::remember($cacheKey, 3600, function() use ($yearId, $classId, $isCycle1) {
            $students = User::whereHas('studentMarks', function($query) use ($yearId, $classId) {
                $query->where('year_id', $yearId)
                      ->where('class_id', $classId);
            })->get(['id']);

            $annualMeans = [];
            $requiredTermCount = $isCycle1 ? 3 : 2;
            
            foreach ($students as $student) {
                $means = [];
                
                if ($isCycle1) {
                    $termIds = [1, 4, 5];
                    foreach ($termIds as $termId) {
                        $mean = $this->calculateStudentTermMeanOptimized($student->id, $yearId, $classId, $termId);
                        if (is_numeric($mean)) {
                            $means[] = $mean;
                        }
                    }
                } else {
                    $termIds = [6, 7];
                    foreach ($termIds as $termId) {
                        $mean = $this->calculateStudentTermMeanOptimized($student->id, $yearId, $classId, $termId);
                        if (is_numeric($mean)) {
                            $means[] = $mean;
                        }
                    }
                }
                
                if (count($means) === $requiredTermCount) {
                    $annualMean = array_sum($means) / count($means);
                    $annualMeans[$student->id] = $annualMean;
                }
            }
            
            arsort($annualMeans);
            
            $rank = 1;
            $previousMean = null;
            $sameRankCount = 1;
            $studentRanks = [];
            
            foreach ($annualMeans as $id => $mean) {
                if ($previousMean !== null && abs($mean - $previousMean) > 0.001) {
                    $rank += $sameRankCount;
                    $sameRankCount = 1;
                } else {
                    $sameRankCount++;
                }
                
                $studentRanks[$id] = $rank;
                $previousMean = $mean;
            }
            
            return $studentRanks;
        });
        
        return $allRanks[$studentId] ?? 'N/A';
    } */
   /**
 * ✅ CORRECTION : Calcul du rang annuel avec cache (SANS utiliser la relation)
 */
protected function calculateAnnualRankOptimized($studentId, $yearId, $classId, $isCycle1)
{
    $cacheKey = "annual_rank_{$yearId}_{$classId}";
    
    $allRanks = Cache::remember($cacheKey, 3600, function() use ($yearId, $classId, $isCycle1) {
        // ✅ CORRECTION : Utiliser StudentMarks directement
        $studentIds = StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->distinct()
            ->pluck('student_id');

        $annualMeans = [];
        $requiredTermCount = $isCycle1 ? 3 : 2;
        
        foreach ($studentIds as $id) {
            $means = [];
            
            if ($isCycle1) {
                $termIds = [1, 4, 5];
                foreach ($termIds as $termId) {
                    $mean = $this->calculateStudentTermMeanOptimized($id, $yearId, $classId, $termId);
                    if (is_numeric($mean)) {
                        $means[] = $mean;
                    }
                }
            } else {
                $termIds = [6, 7];
                foreach ($termIds as $termId) {
                    $mean = $this->calculateStudentTermMeanOptimized($id, $yearId, $classId, $termId);
                    if (is_numeric($mean)) {
                        $means[] = $mean;
                    }
                }
            }
            
            if (count($means) === $requiredTermCount) {
                $annualMean = array_sum($means) / count($means);
                $annualMeans[$id] = $annualMean;
            }
        }
        
        arsort($annualMeans);
        
        $rank = 1;
        $previousMean = null;
        $sameRankCount = 1;
        $studentRanks = [];
        
        foreach ($annualMeans as $id => $mean) {
            if ($previousMean !== null && abs($mean - $previousMean) > 0.001) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }
            
            $studentRanks[$id] = $rank;
            $previousMean = $mean;
        }
        
        return $studentRanks;
    });
    
    return $allRanks[$studentId] ?? 'N/A';
}

    /**
     * Méthode utilitaire pour calcul des notes d'un terme spécifique
     */
    protected function calculateSubjectMarksForTerm($studentId, $yearId, $classId, $subjectId, $termTypeId)
    {
        $examTypes = ExamType::where('term_type_id', $termTypeId)->get();

        $interro = $examTypes->first(fn($e) => str_contains(strtolower($e->name), 'interrogation'));
        $devoir = $examTypes->first(fn($e) => str_contains(strtolower($e->name), 'devoir'));
        $compo = $examTypes->first(fn($e) => str_contains(strtolower($e->name), 'compo'));

        $noteInterro = $interro ? optional(StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->where('exam_type_id', $interro->id)
            ->first())->marks : null;

        $noteDevoir = $devoir ? optional(StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->where('exam_type_id', $devoir->id)
            ->first())->marks : null;

        $noteCompo = $compo ? optional(StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->where('exam_type_id', $compo->id)
            ->first())->marks : null;

        $noteInterro = ($noteInterro === 0.0) ? null : $noteInterro;
        $noteDevoir = ($noteDevoir === 0.0) ? null : $noteDevoir;
        $noteCompo = ($noteCompo === 0.0) ? null : $noteCompo;

        $moyenneClasse = null;
        if ($noteInterro !== null && $noteDevoir !== null) {
            $moyenneClasse = ($noteInterro + $noteDevoir) / 2;
        } elseif ($noteInterro !== null) {
            $moyenneClasse = $noteInterro;
        } elseif ($noteDevoir !== null) {
            $moyenneClasse = $noteDevoir;
        }

        $moyenneFinale = null;
        if ($moyenneClasse !== null && $noteCompo !== null) {
            $moyenneFinale = ($moyenneClasse + $noteCompo) / 2;
        }

        return [
            'moyenne_classe' => $moyenneClasse,
            'moyenne_compo' => $noteCompo,
            'moyenne_finale' => $moyenneFinale,
            'notes_interros' => $noteInterro,
            'notes_devoirs' => $noteDevoir,
            'notes_compo' => $noteCompo,
        ];
    }

    // =========================================================================
    // MÉTHODES EXISTANTES CONSERVÉES (optimisées quand possible)
    // =========================================================================

    protected function getPrincipalTeacher($className)
    {
        $principalTeachers = [
            '3ème C' => 'Mme KOUMAKO',
            '3ème D' => 'Mr KONDI',
            '3ème B' => 'Mme BISSALOUWE',
            '3ème E' => 'Mr AGBEMEBIO',
            '3ème A' => 'Mr OURO-TAGBA',
            '4ème A' => 'Mr AVEGAN',
            '4ème B' => 'Mr de SOUZA',
            '4ème C' => 'Mr AMEGNONA',
            '4ème D' => 'Mr KOLANI',
            '5ème A' => 'Mr YOMBO',
            '5ème B' => 'Mme KASSOTE',
            '5ème C' => 'Mme KPELI-POUKPESSI',
            '5ème D' => 'Mr KERIM',
            '6ème A' => 'Mr OGBONE',
            '6ème B' => 'Mr DOLOU',
            '6ème C' => 'Mme KEGBENA',
            '6ème D' => 'Mr MINDIZINA',
            '2nd A4-1' => 'Mr NEBADI',
            '2nd A4-2' => 'Mr DJIGUI',
            '2nd CD' => 'Mr ZABOUH',
            '1ère A4-1' => 'Mme TCHAKOUN',
            '1ère A4-2' => 'Mme TAKASSI',
            '1ere D4' => 'Mr LAMBONI',
            'Tle D4-1' => 'Mr OUDANE',
            'Tle D4-2' => 'Mme KOSSI',
            'Tle A4-1' => 'Mr POTCHOWAÏ',
            'Tle A4-2' => 'Mr GOTA',
        ];
        
        return $principalTeachers[$className] ?? 'N/A';
    }

    protected function getSubjectTeacher($assignSubjectId, $yearId, $classId)
    {
        $assignSubject = AssignSubject::find($assignSubjectId);

        if (!$assignSubject) {
            return 'N/A';
        }

        $assignTeacher = AssignSubjectTeach::where('subject_id', $assignSubject->subject_id)
            ->where('class_id', $assignSubject->class_id)
            ->where('year_id', $yearId)
            ->with('teacher')
            ->first();

        return $assignTeacher && $assignTeacher->teacher
            ? $assignTeacher->teacher->name
            : 'N/A';
    }

    protected function getAppreciation($averageMarks)
    {
        if ($averageMarks === 'N/A' || $averageMarks === 'Disp.') {
            return 'N/A';
        }

        if ($averageMarks >= 16) {
            return 'Très Bien';
        } elseif ($averageMarks >= 14) {
            return 'Bien';
        } elseif ($averageMarks >= 12) {
            return 'Assez Bien';
        } elseif ($averageMarks >= 10) {
            return 'Passable';
        } elseif ($averageMarks >= 8) {
            return 'Insuffisant';
        } elseif ($averageMarks >= 5) {
            return 'Faible';
        } else {
            return 'Très Faible';
        }
    }

    protected function getAppreciationTermMean($termMean)
    {
        if ($termMean >= 18) {
            return 'Travail Excellent';
        } elseif ($termMean >= 16) {
            return 'Très Bon Travail';
        } elseif ($termMean >= 14) {
            return 'Bon Travail';
        } elseif ($termMean >= 12) {
            return 'Assez Bon Travail';
        } elseif ($termMean >= 10) {
            return 'Travail Passable';
        } elseif ($termMean >= 8) {
            return 'Travail Insuffisant, Doit Redoubler d\'Effort';
        } elseif ($termMean >= 6) {
            return 'Travail Très Insuffisant. AVERTISSEMENT';
        } elseif ($termMean >= 4) {
            return 'Travail Faible. BLÂME';
        } else {
            return 'Très Travail Faible. BLÂME';
        }
    }

    protected function convertDecimalToWords($number)
    {
        if (!is_numeric($number)) {
            return 'zéro';
        }

        $number = round($number, 2);

        if (strpos((string) $number, '.') !== false) {
            list($integerPart, $fractionalPart) = explode('.', (string) $number);
        } else {
            $integerPart = $number;
            $fractionalPart = null;
        }

        $integerWords = $this->convertToWords((int)$integerPart);

        if (is_null($fractionalPart)) {
            return $integerWords;
        }

        $fractionalPart = str_pad($fractionalPart, 2, '0', STR_PAD_RIGHT);
        $fractionalWords = $this->convertToWords((int)$fractionalPart);

        return $integerWords . ' virgule ' . $fractionalWords;
    }

    protected function convertToWords($number)
    {
        $words = [
            0 => 'zéro', 1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre',
            5 => 'cinq', 6 => 'six', 7 => 'sept', 8 => 'huit', 9 => 'neuf',
            10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze',
            15 => 'quinze', 16 => 'seize', 17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf',
            20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
            60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingts', 90 => 'quatre-vingt-dix'
        ];

        if ($number <= 20) {
            return $words[$number];
        }

        if ($number < 100) {
            $tens = intval($number / 10) * 10;
            $units = $number % 10;

            if ($tens == 70 || $tens == 90) {
                return $words[$tens - 10] . '-' . $words[$units + 10];
            }

            return $units ? $words[$tens] . '-' . $words[$units] : $words[$tens];
        }

        if ($number < 1000) {
            $hundreds = intval($number / 100);
            $remainder = $number % 100;
            $hundredText = $hundreds > 1 ? $words[$hundreds] . ' cent' : 'cent';

            if ($remainder === 0 && $hundreds > 1) {
                $hundredText .= 's';
            }

            return $hundredText . ($remainder ? ' ' . $this->convertToWords($remainder) : '');
        }

        if ($number < 1000000) {
            $thousands = intval($number / 1000);
            $remainder = $number % 1000;
            $thousandText = $thousands > 1 ? $this->convertToWords($thousands) . ' mille' : 'mille';

            return $thousandText . ($remainder ? ' ' . $this->convertToWords($remainder) : '');
        }

        return 'Nombre trop grand';
    }

    /**
     * Méthode de debug pour les moyennes annuelles
     */
    public function debugAnnualMeans($yearId, $classId, $termTypeId)
    {
        $studentsData = $this->getAllStudentsDataOptimized($yearId, $classId, $termTypeId);
        $debugInfo = [];
        
        foreach ($studentsData as $studentId => $studentData) {
            $annualMean = $this->calculateAnnualMeanOptimized($studentId, $yearId, $classId, $termTypeId);
            
            $debugInfo[] = [
                'student_id' => $studentId,
                'student_name' => $studentData['student']->name,
                'annual_mean' => $annualMean,
                'term_means' => []
            ];
            
            $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                             '5ème A', '5ème B', '5ème C', '5ème D', 
                             '4ème A', '4ème B', '4ème C', '4ème D', 
                             '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
            
            $className = StudentClass::find($classId)->name;
            $isCycle1 = in_array($className, $cycle1Classes);
            
            $maxTerms = $isCycle1 ? 3 : 2;
            $termIds = $isCycle1 ? [1, 4, 5] : [6, 7];
            
            foreach ($termIds as $termId) {
                $termMean = $this->calculateStudentTermMeanOptimized($studentId, $yearId, $classId, $termId);
                $debugInfo[count($debugInfo)-1]['term_means']["Terme $termId"] = $termMean;
            }
        }
        
        return $debugInfo;
    }
    /////////////////////////////////////////////////METHODE POUR AFFICHAGE DES ABSENCES SUR LE BULLETIN/
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // ✅ Ajoutez cette méthode dans la classe BulletinCalculationService
    protected function getStudentAbsences($studentId, $yearId, $classId, $termTypeId)
    {
        try {
            $absence = StudentAbsence::where('student_id', $studentId)
                ->where('year_id', $yearId)
                ->where('class_id', $classId)
                ->where('type_tri_sem', $termTypeId)
                ->first();
            
            // ✅ Retourner le nombre d'heures d'absence (0 si non trouvé)
            return $absence ? (int)$absence->absences : 0;
            
        } catch (\Exception $e) {
            \Log::error("Erreur récupération absences pour student_id {$studentId}: " . $e->getMessage());
            return 0; // Par défaut 0 en cas d'erreur
        }
    }
}