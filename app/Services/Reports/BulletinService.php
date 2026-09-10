<?php

namespace App\Services\Reports;

use App\Helpers\NumberToWords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Log;


// Models
use App\Models\StudentMarks;
use App\Models\AssignSubject;
use App\Models\AssignStudent;
use App\Models\ExamType;
use App\Models\TermType;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\User;

class BulletinService
{
    /** Liste des classes Cycle 1 (Trimestres) */
    private array $cycle1Classes = [
        '6ème A','6ème B','6ème C','6ème D',
        '5ème A','5ème B','5ème C','5ème D',
        '4ème A','4ème B','4ème C','4ème D',
        '3ème A','3ème B','3ème C','3ème D','3ème E',
    ];

    /**
     * Données complètes pour la génération des bulletins (vue/PDF)
     */
    public function buildClassBulletinData(int $yearId, int $classId, int $termTypeId): array
    {
        $year     = StudentYear::findOrFail($yearId);
        $class    = StudentClass::findOrFail($classId);
        $termType = TermType::findOrFail($termTypeId);

        $yearName      = $year->name;
        $className     = $class->name;
        $termTypeName  = $termType->name;
        $principalTeacherName = Config::get('principal_teachers.' . $className, 'N/A');

        $isCycle1 = in_array($termTypeName, ['Trimestre 1','Trimestre 2','Trimestre 3']);
        $isCycle2 = in_array($termTypeName, ['Semestre 1','Semestre 2']);

        $students = $this->getDistinctStudents($yearId, $classId)->unique('student_id')->values();
        $subjects = AssignSubject::where('class_id', $classId)
            ->with([
                'assign_teacher' => function ($q) use ($yearId, $classId) {
                    $q->where('year_id', $yearId)->where('class_id', $classId);
                },
                'assign_teacher.teacher',
                'school_subject'
            ])->get();

        // ==== Données des trimestres/semestres précédents + moyennes annuelles ====
        $previousTermsData = [];
        $annualMeans       = [];
        $annualPercentageAbove10 = null;
        $highestAnnualMean = null;
        $lowestAnnualMean  = null;
        $classAnnualMean   = null;

        if (($isCycle1 && $termTypeName !== 'Trimestre 1') || ($isCycle2 && $termTypeName === 'Semestre 2')) {
            $previousTerms = [];
            if ($isCycle1) {
                if ($termTypeName === 'Trimestre 2') $previousTerms[] = 'Trimestre 1';
                if ($termTypeName === 'Trimestre 3') $previousTerms   = ['Trimestre 1','Trimestre 2'];
            } else {
                if ($termTypeName === 'Semestre 2') $previousTerms[]  = 'Semestre 1';
            }

            foreach ($previousTerms as $prevName) {
                $prevType = TermType::where('name', $prevName)->first();
                if (!$prevType) continue;

                $prevMeans = [];
                foreach ($students as $st) {
                    $meanData = $this->calculateStudentTermMean($yearId, $classId, $st->student_id, $prevType->id);
                    if (is_numeric($meanData['term_mean']) && $meanData['term_mean'] >= 3) {
                        $prevMeans[$st->student_id] = ['mean' => $meanData['term_mean'], 'rank' => null];
                    }
                }

                uasort($prevMeans, fn($a,$b) => $b['mean'] <=> $a['mean']);
                $rk = 1;
                foreach ($prevMeans as &$v) $v['rank'] = $rk++;
                $previousTermsData[$prevName] = $prevMeans;
            }

            if (($isCycle1 && $termTypeName === 'Trimestre 3') || ($isCycle2 && $termTypeName === 'Semestre 2')) {
                foreach ($students as $st) {
                    $termMeans = [];
                    $hasAll    = true;

                    foreach ($previousTermsData as $termName => $data) {
                        if (isset($data[$st->student_id])) {
                            $termMeans[] = $data[$st->student_id]['mean'];
                        } else {
                            $hasAll = false; break;
                        }
                    }

                    $curr = $this->calculateStudentTermMean($yearId, $classId, $st->student_id, $termTypeId);
                    if (is_numeric($curr['term_mean'])) $termMeans[] = $curr['term_mean']; else $hasAll = false;

                    if ($hasAll && count($termMeans) > 0) {
                        $annual = array_sum($termMeans)/count($termMeans);
                        $annualMeans[$st->student_id] = ['mean'=>$annual,'rank'=>null];
                    }
                }

                if (!empty($annualMeans)) {
                    uasort($annualMeans, fn($a,$b) => $b['mean'] <=> $a['mean']);
                    $rk = 1; foreach ($annualMeans as &$v) $v['rank'] = $rk++;

                    $studentsWithAnnualMeanAbove10 = array_filter($annualMeans, fn($v) => $v['mean'] >= 10);
                    $annualPercentageAbove10 = count($annualMeans) > 0
                        ? number_format((count($studentsWithAnnualMeanAbove10)/count($annualMeans))*100, 2)
                        : number_format(0,2);

                    $vals = array_column($annualMeans, 'mean');
                    if (!empty($vals)) {
                        $highestAnnualMean = number_format(max($vals),2);
                        $lowestAnnualMean  = number_format(min($vals),2);
                        $classAnnualMean   = number_format(array_sum($vals)/count($vals),2);
                    }
                }
            }
        }

        // ==== Données par élève / matière ====
        $studentData     = [];
        $validTermMeans  = [];

        foreach ($students as $st) {
            $subjectData         = [];
            $subjectMarksPerStudent = []; // <-- <- ajouter ceci
            $totalWeightedSum    = 0;
            $totalCoefficientSum = 0;

            $group    = AssignStudent::where('student_id', $st->student_id)->first();
            $group_id = $group ? $group->group_id : null;

            foreach ($subjects as $subj) {
                // Exclusions selon groupe
                if (($group_id == 5 && $subj->subject_id == 10) || ($group_id == 4 && $subj->subject_id == 17)) {
                    continue;
                }

                //$examMarks = $this->getExamMarks($yearId, $classId, $st->student_id, $subj->subject_id, $termTypeId);
                //$average   = collect($examMarks)->avg();
                $subjectMarks = $this->calculateSubjectMarks($yearId, $classId, $st->student_id, $subj->subject_id, $termTypeId);

                $moyenneClasse = $subjectMarks['moyenne_classe'] ?? null;
                $moyenneCompo  = $subjectMarks['moyenne_compo'] ?? null;
                $average       = $subjectMarks['moyenne_finale'] ?? null; // utilisé pour la moyenne générale

                $coef      = optional($subj)->subjective_mark ?? 0;

                $isEPS    = optional($subj->school_subject)->name === 'E.P.S';
                $inapte   = $this->isStudentInapte($st->student_id, $subj->subject_id);

                if ($isEPS && $inapte) {
                    $displayAvg      = 'Disp.';
                    $weightedAverage = 'Disp.';
                    $rank            = 'Disp.';
                    $app             = '--';
                } else {
                    $displayAvg      = $average;
                    $weightedAverage = $coef * ($average ?? 0);
                    $rank            = null;
                    $app             = $this->getAppreciation($average ?? 0);

                    if (!is_null($average)) {
                        $totalWeightedSum    += $weightedAverage;
                        $totalCoefficientSum += $coef;
                    }
                }

                $teacherName = optional(optional($subj->assign_teacher)->teacher)->name ?? 'N/A';

                /* $subjectData[] = [
                    'subject_id'                   => $subj->subject_id,
                    'subject_name'                 => optional($subj->school_subject)->name ?? 'N/A',
                    //'exam_marks'                   => $examMarks,
                    //'average_marks'                => $displayAvg,
                    'subjective_mark_coefficient'  => ($isEPS && $inapte) ? '--' : $coef,
                    'weighted_average'             => $weightedAverage,
                    'rank'                         => $rank,
                    'assigned_teacher'             => $teacherName,
                    'appreciation'                 => $app,
                    'is_inapte'                    => ($isEPS && $inapte),
                    'moyenne_classe' => $moyenneClasse,
                    'moyenne_compo'  => $moyenneCompo,
                    'average_marks'  => $average, // la finale

                ]; */

                $subjectData[] = [
                    'subject_id'                  => $subj->subject_id,
                    'subject_name'                => optional($subj->school_subject)->name ?? 'N/A',
                    // nouvelles clés : montrer clairement les 3 moyennes
                    'moyenne_classe'              => $moyenneClasse,
                    'moyenne_compo'               => $moyenneCompo,
                    'average_marks'               => $displayAvg, // moyenne finale affichée
                    'exam_marks'                  => [
                            'interros' => $subjectMarks['notes_interros'],
                            'devoirs'  => $subjectMarks['notes_devoirs'],
                            'compo'    => $subjectMarks['notes_compo'],
                        ],
                    'subjective_mark_coefficient' => ($isEPS && $inapte) ? '--' : $coef,
                    'weighted_average'            => $weightedAverage,
                    'rank'                        => $rank,
                    'assigned_teacher'            => $teacherName,
                    'appreciation'                => $app,
                    'is_inapte'                   => ($isEPS && $inapte),
                ];

                // en plus, tu stockes le détail complet indexé par matière
                $subjectMarksPerStudent[$subj->subject_id] = $subjectMarks;
            }

            $termMean  = $totalCoefficientSum > 0 ? $totalWeightedSum / $totalCoefficientSum : 0;
            $isAbandon = $termMean < 3;

            // Terme(s) précédent(s) pour l’élève (si dispo)
            $studentPreviousTerms = [];
            if (!empty($previousTermsData)) {
                foreach ($previousTermsData as $tName => $termStudentsData) {
                    if (isset($termStudentsData[$st->student_id])) {
                        $m = $termStudentsData[$st->student_id]['mean'];
                        $studentPreviousTerms[$tName] = [
                            'mean'       => is_numeric($m) ? $m : 'N/A',
                            'rank'       => $termStudentsData[$st->student_id]['rank'],
                            'mean_words' => NumberToWords::convertDecimalToWords(is_numeric($m) ? $m : 0),
                        ];
                    } else {
                        $studentPreviousTerms[$tName] = ['mean'=>'N/A','rank'=>'N/A','mean_words'=>'N/A'];
                    }
                }
            }

            // Données annuelles (si calculées)
            $annualMeanData = [
                'mean'         => $annualMeans[$st->student_id]['mean'] ?? 'N/A',
                'rank'         => $annualMeans[$st->student_id]['rank'] ?? 'N/A',
                'mean_words'   => isset($annualMeans[$st->student_id]['mean'])
                    ? NumberToWords::convertDecimalToWords($annualMeans[$st->student_id]['mean']) : 'N/A',
                'appreciation' => isset($annualMeans[$st->student_id]['mean'])
                    ? $this->getAppreciationTermMean($annualMeans[$st->student_id]['mean']) : 'N/A',
            ];

            $studentData[] = [
                'student_id'         => $st->student_id,
                'student_name'       => $st->student->name,
                'gender'             => $st->student->gender,
                'statusclass'        => $st->student->statusclass,
                'subjects'           => $subjectData,
                'subject_marks'      => $subjectMarksPerStudent, // <-- ajouter ceci
                'term_mean'          => $termMean,
                'term_mean_words'    => NumberToWords::convertDecimalToWords($termMean),
                'appreciation_term'  => $this->getAppreciationTermMean($termMean),
                'is_abandon'         => $isAbandon,
                'term_rank'          => 'N/A', // assigné plus tard
                'previous_terms'     => $studentPreviousTerms,
                'annual_mean'        => $annualMeanData,
            ];

            if (!$isAbandon) $validTermMeans[] = $termMean;
        }

        // Classement & Stats
        $this->assignTermRanks($studentData);
        $this->assignSubjectRanks($studentData, $subjects);

        $numberOfStudents = count(array_filter($studentData, fn($st) => !$st['is_abandon']));
        $passers          = array_filter($studentData, fn($st) => !$st['is_abandon'] && $st['term_mean'] >= 10);
        $percentageAbove10 = $numberOfStudents > 0
            ? number_format((count($passers) / $numberOfStudents) * 100, 2)
            : number_format(0, 2);

        $highestTermMean = !empty($validTermMeans) ? number_format(max($validTermMeans), 2) : 'N/A';
        sort($validTermMeans);
        $lowestTermMean  = !empty($validTermMeans) ? number_format($validTermMeans[0], 2) : 'N/A';
        $classTermMean   = !empty($validTermMeans) ? number_format(array_sum($validTermMeans)/count($validTermMeans), 2) : 'N/A';

        return [
            'meta' => compact('yearName','className','termTypeName','principalTeacherName'),
            'subjects' => $subjects,
            'studentData' => $studentData,
            'counts' => [
                'numberOfStudents'  => $numberOfStudents,
                'percentageAbove10' => $percentageAbove10,
            ],
            'stats' => [
                'highestTermMean' => $highestTermMean,
                'lowestTermMean'  => $lowestTermMean,
                'classTermMean'   => $classTermMean,
                'annualPercentageAbove10' => $annualPercentageAbove10,
                'highestAnnualMean' => $highestAnnualMean,
                'lowestAnnualMean'  => $lowestAnnualMean,
                'classAnnualMean'   => $classAnnualMean,
            ],
        ];
    }

    // ====================== OUTILS / LOGIQUE ======================

    /* public function getDistinctStudents(int $yearId, int $classId): Collection
    {
        return StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->with(['student' => function ($q) {
                $q->select('id','name','gender','statusclass');
            }])
            ->select('student_id')
            ->distinct()
            ->orderBy(User::select('name')->whereColumn('users.id','student_id'))
            ->get();
    }  */
/*     public function getDistinctStudents(int $yearId, int $classId): Collection
{
    return AssignStudent::where('year_id', $yearId)
        ->where('class_id', $classId)
        ->with(['student' => function ($q) {
            $q->select('id','name','gender','statusclass');
        }])
        ->orderBy(User::select('name')->whereColumn('users.id','assign_students.student_id'))
        ->get()
        ->map(function ($assign) {
            return (object) [
                'student_id' => $assign->student_id,
                'student'    => $assign->student,
            ];
        });
} */
public function getDistinctStudents(int $yearId, int $classId): Collection
{
    return AssignStudent::where('year_id', $yearId)
        ->where('class_id', $classId)
        ->with(['student:id,name,gender,statusclass'])
        ->get()
        ->map(function ($assign) {
            return (object)[
                'student_id' => $assign->student_id,
                'student'    => $assign->student,
            ];
        });
}


   /* public function getDistinctStudents(int $yearId, int $classId): Collection
    {
        // ✅ CORRECTION : Récupérer d'abord les étudiants INSCRITS dans la classe
        $studentIds = AssignStudent::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->pluck('student_id');
        
        // Ensuite, vérifier qu'ils ont bien des notes pour le trimestre
        return StudentMarks::where('year_id', $yearId)
            ->whereIn('student_id', $studentIds)  // ✅ FILTRE CRITIQUE
            ->select('student_id')
            ->distinct()
            ->with(['student' => function ($q) {
                $q->select('id','name','gender','statusclass');
            }])
            ->orderBy(User::select('name')->whereColumn('users.id','student_id'))
            ->get();
    } */
/* public function getDistinctStudents(int $yearId, int $classId): Collection
{
    Log::info("🔍 getDistinctStudents - Classe ID: $classId, Année ID: $yearId");
    
    // Méthode 1: Depuis assign_students (RECOMMANDÉE)
    $students = AssignStudent::where('year_id', $yearId)
        ->where('class_id', $classId)
        ->with(['student' => function ($q) {
            $q->select('id','name','gender','statusclass');
        }])
        ->get();
    
    Log::info("📊 Étudiants assignés à la classe: " . $students->count());
    
    // Transform pour garder la même structure que l'ancienne méthode
    return $students->map(function($assign) {
        return (object) [
            'student_id' => $assign->student_id,
            'student' => $assign->student
        ];
    });
} */
/* public function getDistinctStudents(int $yearId, int $classId): Collection
{
    Log::info("🔍 getDistinctStudents - Classe ID: $classId, Année ID: $yearId");
    
    // ✅ CORRECTION : Récupérer depuis assign_students
    $assignments = AssignStudent::where('year_id', $yearId)
        ->where('class_id', $classId)
        ->with(['student' => function ($q) {
            $q->select('id','name','gender','statusclass');
        }])
        ->get();
    
    Log::info("📊 Étudiants inscrits dans la classe: " . $assignments->count());
    
    // Transform pour garder la même structure
    return $assignments->map(function($assign) {
        return (object) [
            'student_id' => $assign->student_id,
            'student' => $assign->student
        ];
    });
}
 */
    public function getExamMarks(int $yearId, int $classId, int $studentId, int $subjectId, ?int $termTypeId = null): Collection
    {
        $examTypes = $this->getExamTypesForTerm($termTypeId);
        if ($examTypes->isEmpty()) return collect();

        return StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->whereIn('exam_type_id', $examTypes)
            ->pluck('marks');
    }

    ////////////////Cette methode remplacera dans la suite la methode getExamMarks//////////////////
    protected function calculateSubjectMarks($yearId, $classId, $studentId, $subjectId, $termTypeId): array
    {
        $examTypes = ExamType::where('term_type_id', $termTypeId)->get();

        $interros = $examTypes->filter(fn($e) => str_contains(strtolower($e->name), 'interrogation'))->pluck('id');
        $devoirs  = $examTypes->filter(fn($e) => str_contains(strtolower($e->name), 'devoir'))->pluck('id');
        $compos   = $examTypes->filter(fn($e) => str_contains(strtolower($e->name), 'compo'))->pluck('id');

        $notesInterros = StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->whereIn('exam_type_id', $interros)
            ->pluck('marks');

        $notesDevoirs = StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->whereIn('exam_type_id', $devoirs)
            ->pluck('marks');

        $notesCompos = StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->whereIn('exam_type_id', $compos)
            ->pluck('marks');

        $moyenneClasse = $notesInterros->merge($notesDevoirs)->avg();
        $moyenneCompo  = $notesCompos->avg();

        $moyenneFinale = null;
        if ($moyenneClasse !== null && $moyenneCompo !== null) {
            $moyenneFinale = ($moyenneClasse + $moyenneCompo) / 2;
        } else {
            $moyenneFinale = $moyenneClasse ?? $moyenneCompo;
        }

        return [
            'moyenne_classe' => $moyenneClasse,
            'moyenne_compo'  => $moyenneCompo,
            'moyenne_finale' => $moyenneFinale,
            'notes_interros' => $notesInterros,
            'notes_devoirs'  => $notesDevoirs,
            'notes_compo'    => $notesCompos,
        ];
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////

    public function getExamTypesForTerm(?int $termTypeId = null): Collection
    {
        if (!$termTypeId) return ExamType::pluck('id');

        $term = TermType::find($termTypeId);
        if (!$term) return collect();

        $map = [
            'Trimestre 1' => ['Interrogation 1Trim', 'Devoir 1er Trim', 'Compo 1er Trim'],
            'Trimestre 2' => ['Interrogation 2Trim', 'Devoir 2em Trim', 'Compo 2em Trim'],
            'Trimestre 3' => ['Interrogation 3Trim', 'Devoir 3em Trim', 'Compo 3em Trim'],
            'Semestre 1'  => ['Interrogation 1Sem', 'Devoir 1er Sem', 'Compo 1er Sem'],
            'Semestre 2'  => ['Interrogation 2Sem', 'Devoir 2em Sem', 'Compo 2em Sem'],
        ];

        return isset($map[$term->name])
            ? ExamType::whereIn('name', $map[$term->name])->pluck('id')
            : collect();
    }

    public function isStudentInapte(int $studentId, int $subjectId): bool
    {
        return StudentMarks::where('student_id', $studentId)
            ->where('assign_subject_id', $subjectId)
            ->where('inapte', 1)
            ->exists();
    }

    public function getAppreciation(?float $avg): string
    {
        if ($avg === null) return 'N/A';
        if ($avg >= 16) return 'Très Bien';
        if ($avg >= 14) return 'Bien';
        if ($avg >= 12) return 'Assez Bien';
        if ($avg >= 10) return 'Passable';
        if ($avg >= 8)  return 'Insuffisant';
        if ($avg >= 5)  return 'Faible';
        return 'Très Faible';
    }

    public function getAppreciationTermMean(float $termMean): string
    {
        if ($termMean >= 18) return 'Travail Excellent';
        if ($termMean >= 16) return 'Très Bon Travail';
        if ($termMean >= 14) return 'Bon Travail';
        if ($termMean >= 12) return 'Assez Bon Travail';
        if ($termMean >= 10) return 'Travail Passable';
        if ($termMean >= 8)  return "Travail Insuffisant, Doit Redoubler d'Effort";
        if ($termMean >= 6)  return 'Travail Très Insuffisant. AVERTISSEMENT';
        if ($termMean >= 4)  return 'Travail Faible. BLÂME';
        return 'Très Travail Faible. BLÂME';
    }

    public function calculateStudentTermMean(int $yearId, int $classId, int $studentId, int $termTypeId): array
    {
        $subjects = AssignSubject::where('class_id', $classId)->with('school_subject')->get();
        $totalWeighted = 0;
        $totalCoef     = 0;

        $group    = AssignStudent::where('student_id', $studentId)->first();
        $group_id = $group ? $group->group_id : null;

        foreach ($subjects as $subj) {
            if (($group_id == 5 && $subj->subject_id == 10) || ($group_id == 4 && $subj->subject_id == 17)) {
                continue;
            }

            //$marks   = $this->getExamMarks($yearId, $classId, $studentId, $subj->subject_id, $termTypeId);
            //$avg     = collect($marks)->avg();
            // 🔄 nouvelle logique avec calculateSubjectMarks
            $subjectMarks = $this->calculateSubjectMarks($yearId, $classId, $studentId, $subj->subject_id, $termTypeId);
            $avg          = $subjectMarks['moyenne_finale'] ?? null; // on prend la finale (classe + compo)
            $coef    = optional($subj)->subjective_mark ?? 0;

            $isEPS   = optional($subj->school_subject)->name === 'E.P.S';
            $inapte  = $this->isStudentInapte($studentId, $subj->subject_id);

            if (!($isEPS && $inapte) && $avg !== null) {
                $totalWeighted += $coef * $avg;
                $totalCoef     += $coef;
            }
        }

        $termMean = $totalCoef > 0 ? $totalWeighted / $totalCoef : 0;
        return [
            'term_mean'  => is_numeric($termMean) ? $termMean : 'N/A',
            'is_abandon' => is_numeric($termMean) ? ($termMean < 3) : false,
        ];
    }

    public function assignTermRanks(array &$studentData): void
    {
        $sorted = collect($studentData)->sortByDesc('term_mean')->values();
        $rank = 1;
        $prev = null;

        foreach ($sorted as $i => $st) {
            if ($prev !== null && $st['term_mean'] !== $prev) $rank = $i + 1;
            $key = collect($studentData)->search(fn($s) => $s['student_id'] === $st['student_id']);
            if ($key !== false) $studentData[$key]['term_rank'] = $rank;
            $prev = $st['term_mean'];
        }
    }

    public function assignSubjectRanks(array &$studentData, Collection $subjects): void
    {
        foreach ($subjects as $subject) {
            $subjectRanks = collect($studentData)->map(function ($st) use ($subject) {
                $s = collect($st['subjects'])->firstWhere('subject_id', $subject->subject_id);
                $avg = $s['average_marks'] ?? 0;
                return ['student_id' => $st['student_id'], 'average_marks' => is_numeric($avg) ? $avg : 0];
            });

            $sorted = $subjectRanks->sortByDesc('average_marks')->values();
            $rank = 1; $prev = null;

            foreach ($sorted as $i => $row) {
                if ($prev !== null && $row['average_marks'] != $prev) $rank = $i + 1;

                $key = collect($studentData)->search(fn($s) => $s['student_id'] === $row['student_id']);
                if ($key !== false) {
                    $studentSubjects = &$studentData[$key]['subjects'];
                    $subjectKey = collect($studentSubjects)->search(fn($x) => $x['subject_id'] === $subject->subject_id);
                    if ($subjectKey !== false) $studentSubjects[$subjectKey]['rank'] = $rank;
                }
                $prev = $row['average_marks'];
            }
        }
    }
}
