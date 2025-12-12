<?php

namespace App\Services;

use App\DataObjects\BulletinData;
use Illuminate\Support\Facades\Log;

class StudentRankingService
{
    public function calculateRanksAndStatistics(BulletinData $bulletinData)
    {
        // Récupération des étudiants
        $students = $bulletinData->students;
        
        // Calcul des rangs du trimestre
        $this->calculateTermRanks($students);
        
        // Calcul des rangs par matière
        //if (!empty($students)) {
        //    $subjects = $students[0]['subjects'] ?? [];
        //    $this->calculateSubjectRanks($students, $subjects);
        //}
        // 🧩 Calcul des rangs par matière — avec gestion des matières optionnelles
    if (!empty($students)) {
        // ✅ Récupère toutes les matières uniques présentes dans la classe
        $subjects = collect($students)
            ->pluck('subjects')       // récupère la liste des matières de chaque élève
            ->flatten(1)              // fusionne toutes les listes
            ->unique('subject_id')    // supprime les doublons
            ->values()
            ->all();

        // 🔍 Log de vérification
        //\Log::info('MATIERES INCLUSES POUR LE CLASSEMENT', [
        //    'subject_ids' => collect($subjects)->pluck('subject_id')->toArray(),
        //    'subject_names' => collect($subjects)->pluck('subject_name')->toArray(),
        //]);

        $this->calculateSubjectRanks($students, $subjects);
    }

        
        // Calcul des statistiques
        $statistics = $this->calculateStatistics($students);

        // Ajouter les statistiques annuelles si terme final
        $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                        '5ème A', '5ème B', '5ème C', '5ème D', 
                        '4ème A', '4ème B', '4ème C', '4ème D', 
                        '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
        
        $className = $bulletinData->class->name;
        $isCycle1 = in_array($className, $cycle1Classes);
        $isFinalTerm = ($isCycle1 && $bulletinData->termType->name == 'Trimestre 3') || 
                    (!$isCycle1 && $bulletinData->termType->name == 'Semestre 2');
        
        if ($isFinalTerm) {
            $annualStatistics = $this->calculateAnnualStatistics($students);
            $statistics = array_merge($statistics, $annualStatistics);
        }

        $bulletinData->setStatistics($statistics);
        
        // Mise à jour des étudiants dans BulletinData
        $bulletinData->setStudents($students);
        
        // Debug final
        //Log::info("FINAL CHECK - Ranks applied", [
        //    'student_1292' => $this->getStudentRanks($students, 1292),
        //    'student_1435' => $this->getStudentRanks($students, 1435)
        //]);
    }

    // Ajoutez cette méthode dans la classe StudentRankingService
protected function calculateAnnualStatistics(array $students)
{
    \Log::info('Calcul des statistiques annuelles - Début');
    \Log::info('Nombre d\'étudiants: ' . count($students));
    
    $validStudents = array_filter($students, function($student) {
        $hasValidMean = isset($student['annual_mean']['mean']) && 
                       is_numeric($student['annual_mean']['mean']) &&
                       $student['annual_mean']['mean'] > 0;
        
        \Log::info('Étudiant ' . $student['student_id'] . ': ' . 
                  ($hasValidMean ? $student['annual_mean']['mean'] : 'N/A'));
        
        return $hasValidMean;
    });
    
    \Log::info('Étudiants valides: ' . count($validStudents));
    
    if (empty($validStudents)) {
        \Log::info('Aucun étudiant valide - retour N/A');
        return [
            'class_annual_mean' => 'N/A',
            'highest_annual_mean' => 'N/A',
            'lowest_annual_mean' => 'N/A',
            'annual_percentage_above_10' => 'N/A'
        ];
    }
    
    // CORRECTION ICI : Utiliser array_map au lieu de array_column
    $annualMeans = array_map(function($student) {
        return $student['annual_mean']['mean'];
    }, $validStudents);
    
    \Log::info('Moyennes annuelles: ' . json_encode($annualMeans));
    
    // VÉRIFICATION que le tableau n'est pas vide
    if (empty($annualMeans)) {
        \Log::info('Tableau annualMeans vide - retour N/A');
        return [
            'class_annual_mean' => 'N/A',
            'highest_annual_mean' => 'N/A',
            'lowest_annual_mean' => 'N/A',
            'annual_percentage_above_10' => 'N/A'
        ];
    }
    
    $numberOfStudents = count($validStudents);
    $highestAnnualMean = max($annualMeans);
    $lowestAnnualMean = min($annualMeans);
    $classAnnualMean = array_sum($annualMeans) / $numberOfStudents;
    
    $studentsAbove10 = array_filter($validStudents, function($student) {
        return $student['annual_mean']['mean'] >= 10;
    });
    
    $annualPercentageAbove10 = count($studentsAbove10) > 0 ? 
        (count($studentsAbove10) / $numberOfStudents) * 100 : 0;
    
    return [
        'class_annual_mean' => round($classAnnualMean, 2),
        'highest_annual_mean' => round($highestAnnualMean, 2),
        'lowest_annual_mean' => round($lowestAnnualMean, 2),
        'annual_percentage_above_10' => round($annualPercentageAbove10, 2)
    ];
}
//////////// fin mie a Jour////////////////
    
    private function getStudentRanks(array $students, $studentId): array
    {
        foreach ($students as $student) {
            if ($student['student_id'] == $studentId) {
                $ranks = [];
                foreach ($student['subjects'] as $subject) {
                    $ranks[$subject['subject_id']] = $subject['rank'] ?? 'N/A';
                }
                return $ranks;
            }
        }
        return ['error' => 'Student not found'];
    }
    
    protected function calculateTermRanks(array &$students): void
    {
        // Filtrer les étudiants valides
        $validStudents = array_filter($students, function($student) {
            return !$student['is_abandon'] && is_numeric($student['term_mean']);
        });
        
        if (empty($validStudents)) {
            $this->setAllTermRanksToNA($students);
            return;
        }
        
        // Trier par moyenne décroissante
        usort($validStudents, function($a, $b) {
            return $b['term_mean'] <=> $a['term_mean'];
        });
        
        // Calculer les rangs avec gestion des ex-aequo
        //$rankedStudents = $this->assignRanks($validStudents, 'term_mean');
        // 🔥 NOUVELLE MÉTHODE avec gestion ex-æquo
        $rankedStudents = $this->assignTermRanksWithExAequo($validStudents);
        
        // Assigner les rangs aux étudiants
        $this->applyTermRanks($students, $rankedStudents);
    }

    protected function assignTermRanksWithExAequo(array $students): array
{
    $rankedStudents = [];
    $currentRank = 1;
    $previousValue = null;
    $sameRankCount = 0;
    $rankGroups = [];
    
    foreach ($students as $student) {
        $currentValue = $student['term_mean'];
        $studentId = $student['student_id'];
        
        // Vérifier si changement de moyenne
        if ($previousValue !== null && abs($currentValue - $previousValue) > 0.001) {
            $currentRank += $sameRankCount;
            $sameRankCount = 1;
        } else {
            $sameRankCount++;
        }
        
        // Stocker les groupes de rangs
        if (!isset($rankGroups[$currentRank])) {
            $rankGroups[$currentRank] = [];
        }
        $rankGroups[$currentRank][] = $studentId;
        
        $rankedStudents[$studentId] = [
            'student' => $student,
            'rank' => $currentRank,
            'is_ex_aequo' => false, // Initialisé à false
        ];
        
        $previousValue = $currentValue;
    }
    
    // 🔥 Marquer comme ex-æquo seulement à partir du 2ème élève du groupe
    foreach ($rankGroups as $rank => $studentIds) {
        if (count($studentIds) > 1) {
            for ($i = 1; $i < count($studentIds); $i++) {
                $studentId = $studentIds[$i];
                $rankedStudents[$studentId]['is_ex_aequo'] = true;
            }
        }
    }
    
    return $rankedStudents;
}
    
    /* protected function calculateSubjectRanks(array &$students, array $subjects): void
    {
        foreach ($subjects as $subject) {
            $subjectId = $subject['subject_id'];
            
            $subjectData = $this->prepareSubjectRankingData($students, $subjectId);
            
            if (empty($subjectData['validMarks'])) {
                continue;
            }
            
            // Trier par moyenne décroissante
            arsort($subjectData['validMarks']);
            
            // Assigner les rangs
            $rankedStudents = $this->assignRanksToArray($subjectData['validMarks']);

            // 🟩 Détecter les ex æquo
            $rankCounts = array_count_values($rankedStudents);
            $exAequoRanks = array_filter($rankCounts, fn($count) => $count > 1);
            
            // 🟩 Stocker info "ex æquo" dans les rangs
            foreach ($rankedStudents as $studentId => $rank) {
                $rankedStudents[$studentId] = [
                    'rank' => $rank,
                    'is_ex_aequo' => isset($exAequoRanks[$rank]),
                ];
            }
            

            // Appliquer les rangs aux étudiants
            $this->applySubjectRanks($students, $subjectId, $rankedStudents, $subjectData['invalidStudents']);
        }
    } */
   protected function calculateSubjectRanks(array &$students, array $subjects): void
{
    foreach ($subjects as $subject) {
        $subjectId = $subject['subject_id'];
        
        $subjectData = $this->prepareSubjectRankingData($students, $subjectId);
        
        if (empty($subjectData['validMarks'])) {
            $this->setAllSubjectRanksToNull($students, $subjectId);
            continue;
        }
        
        // Assigner les rangs avec gestion robuste des ex-æquo
        $rankedStudents = $this->assignSubjectRanksWithExAequo($subjectData['validMarks']);
        
        // Appliquer les rangs
        $this->applySubjectRanks($students, $subjectId, $rankedStudents, $subjectData['invalidStudents']);
    }
}

protected function assignSubjectRanksWithExAequo(array $marks): array
{
    // Trier par moyenne décroissante
    arsort($marks);
    
    $rankedStudents = [];
    $currentRank = 1;
    $previousValue = null;
    $sameRankCount = 0;
    $rankGroups = [];
    
    foreach ($marks as $studentId => $value) {
        // Nouveau rang si moyenne différente
        if ($previousValue !== null && abs($value - $previousValue) > 0.001) {
            $currentRank += $sameRankCount;
            $sameRankCount = 1;
        } else {
            $sameRankCount++;
        }
        
        $rankedStudents[$studentId] = [
            'rank' => $currentRank,
            'is_ex_aequo' => false, // Initialisé à false
        ];
        
        // Stocker les groupes de rangs pour détection ex-æquo
        if (!isset($rankGroups[$currentRank])) {
            $rankGroups[$currentRank] = [];
        }
        $rankGroups[$currentRank][] = $studentId;
        
        $previousValue = $value;
    }
    
    // Marquer les ex-æquo (seulement si plus d'un étudiant a le même rang)
    foreach ($rankGroups as $rank => $studentIds) {
        if (count($studentIds) > 1) {
            //foreach ($studentIds as $studentId) {
            //    $rankedStudents[$studentId]['is_ex_aequo'] = true;
            //}
            // Le premier étudiant garde is_ex_aequo = false
            // Les suivants sont marqués comme ex-æquo
            for ($i = 1; $i < count($studentIds); $i++) {
                $studentId = $studentIds[$i];
                $rankedStudents[$studentId]['is_ex_aequo'] = true;
            }
        }
    }
    
    return $rankedStudents;
}

protected function setAllSubjectRanksToNull(array &$students, $subjectId): void
{
    foreach ($students as &$student) {
        foreach ($student['subjects'] as &$subject) {
            if ($subject['subject_id'] === $subjectId) {
                $subject['rank'] = null;
                $subject['is_ex_aequo'] = false;
                break;
            }
        }
    }
}
    
    protected function prepareSubjectRankingData(array $students, $subjectId): array
    {
        $validMarks = [];
        $invalidStudents = [];
        
        

        foreach ($students as $student) {
            $studentId = $student['student_id'];
            
            if ($subjectId === 17) { // remplace 99 par l'ID de la matière Espagnol
            \Log::info('DEBUG ESPAGNOL', [
                'student_id' => $student['student_id'],
                'student_name' => $student['student_name'],
                'avg_mark' => $this->getStudentSubjectMark($student, $subjectId),
            ]);
        }
        
            if ($student['is_abandon']) {
                //$invalidStudents[$studentId] = 'N/A';
                $invalidStudents[$studentId] = null;
                continue;
            }
            
            $subjectMark = $this->getStudentSubjectMark($student, $subjectId);
            
            if ($subjectMark === null || !is_numeric($subjectMark)) {
                //$invalidStudents[$studentId] = 'N/A';
                $invalidStudents[$studentId] = null;
                continue;
            }
            
            $validMarks[$studentId] = (float) $subjectMark;
        }
        
        return [
            'validMarks' => $validMarks,
            'invalidStudents' => $invalidStudents
        ];
    }
    
    protected function getStudentSubjectMark(array $student, $subjectId)
    {
        foreach ($student['subjects'] as $subject) {
            if ($subject['subject_id'] === $subjectId) {
                return $subject['average_marks'];
            }
        }
        return null;
    }
    
    protected function assignRanks(array $students, string $field): array
    {
        $rankedStudents = [];
        $currentRank = 1;
        $previousValue = null;
        $sameRankCount = 0;
        
        foreach ($students as $student) {
            $currentValue = $student[$field];
            
            if ($previousValue !== null && abs($currentValue - $previousValue) > 0.001) {
                $currentRank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }
            
            $student['rank'] = $currentRank;
            $rankedStudents[$student['student_id']] = $student;
            $previousValue = $currentValue;
        }
        
        return $rankedStudents;
    }
    
    protected function assignRanksToArray(array $values): array
    {
        $ranked = [];
        $currentRank = 1;
        $previousValue = null;
        $sameRankCount = 0;
        
        foreach ($values as $studentId => $value) {
            if ($previousValue !== null && abs($value - $previousValue) > 0.001) {
                $currentRank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }
            
            $ranked[$studentId] = $currentRank;
            $previousValue = $value;
        }
        
        return $ranked;
    }
    
    protected function applyTermRanks(array &$students, array $rankedStudents): void
    {
        foreach ($students as &$student) {
            $studentId = $student['student_id'];
            
            if ($student['is_abandon'] || !is_numeric($student['term_mean'])) {
                $student['term_rank'] = 'N/A';
                $student['term_is_ex_aequo'] = false;
            } elseif (isset($rankedStudents[$studentId])) {
                $student['term_rank'] = $rankedStudents[$studentId]['rank'];
                $student['term_is_ex_aequo'] = $rankedStudents[$studentId]['is_ex_aequo'];
            } else {
                $student['term_rank'] = 'N/A';
                $student['term_is_ex_aequo'] = false;
            }
        }
    }
    
    protected function applySubjectRanks(array &$students, $subjectId, array $rankedStudents, array $invalidStudents): void
    {
        foreach ($students as &$student) {
            $studentId = $student['student_id'];
            
            if (!isset($student['subjects']) || !is_array($student['subjects'])) {
                continue;
            }
            
            /* foreach ($student['subjects'] as &$subject) {
                if (!is_array($subject) || !isset($subject['subject_id'])) {
                    continue;
                }
                
                if ($subject['subject_id'] === $subjectId) {
                    if (isset($invalidStudents[$studentId])) {
                        $subject['rank'] = $invalidStudents[$studentId];
                    } elseif (isset($rankedStudents[$studentId])) {
                        $subject['rank'] = $rankedStudents[$studentId];
                    } else {
                        $subject['rank'] = 'N/A';
                    }
                    break;
                }
            } */
           foreach ($student['subjects'] as &$subject) {
                if (!isset($subject['subject_id'])) {
                    continue;
                }

                if ($subject['subject_id'] === $subjectId) {
                    // Cas 1️⃣ : élève invalide (pas de moyenne finale)
                    if (array_key_exists($studentId, $invalidStudents)) {
                        $subject['rank'] = null;

                    // Cas 2️⃣ : élève classé
                    } elseif (isset($rankedStudents[$studentId])) {
                        //$subject['rank'] = $rankedStudents[$studentId];
                        $subject['rank'] = $rankedStudents[$studentId]['rank'];
                        $subject['is_ex_aequo'] = $rankedStudents[$studentId]['is_ex_aequo'];

                    // Cas 3️⃣ : sécurité — aucun rang trouvé
                    } else {
                        $subject['rank'] = null;
                    }

                    break; // on arrête dès qu’on a trouvé la matière
                }
            }
        }
    }
    
    protected function setAllTermRanksToNA(array &$students): void
    {
        foreach ($students as &$student) {
            $student['term_rank'] = 'N/A';
            $student['term_is_ex_aequo'] = false;
        }
    }
    
    protected function calculateStatistics(array $students): array
    {
        $validStudents = array_filter($students, function($student) {
            return !$student['is_abandon'] && is_numeric($student['term_mean']);
        });
        
        $termMeans = array_column($validStudents, 'term_mean');
        
        if (empty($termMeans)) {
            return [
                'number_of_students' => 0,
                'highest_term_mean' => 0,
                'lowest_term_mean' => 0,
                'class_term_mean' => 0,
                'percentage_above_10' => 0,
            ];
        }
        
        $numberOfStudents = count($validStudents);
        $highestTermMean = max($termMeans);
        $lowestTermMean = min($termMeans);
        $classTermMean = array_sum($termMeans) / $numberOfStudents;
        
        $studentsAbove10 = array_filter($validStudents, function($student) {
            return $student['term_mean'] >= 10;
        });
        
        $percentageAbove10 = (count($studentsAbove10) / $numberOfStudents) * 100;
        
        return [
            'number_of_students' => $numberOfStudents,
            'highest_term_mean' => round($highestTermMean, 2),
            'lowest_term_mean' => round($lowestTermMean, 2),
            'class_term_mean' => round($classTermMean, 2),
            'percentage_above_10' => round($percentageAbove10, 2),
        ];
    }

    public function generateBulletinPdfOptimized(BulletinData $bulletinData, $type = 'bulletin')
{
    // ✅ OPTIMISATION : Augmentation des limites
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 1800);
    ini_set('pcre.backtrack_limit', '10000000');
    ini_set('pcre.recursion_limit', '10000000');

    $viewName = $type === 'statistics' 
        ? 'backend.report.bulletin.annual-statistics_marksheets'
        : 'backend.report.bulletin.class_marksheets';
    
    // ✅ OPTIMISATION : Options PDF pour performance
    $pdf = Pdf::loadView($viewName, [
        'bulletinData' => $bulletinData->toArray()
    ])
    ->setPaper('a4', 'portrait')
    ->setOptions([
        'enable_php' => true,
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'dpi' => 96, // Réduit la qualité pour plus de vitesse
        'defaultFont' => 'Arial',
    ]);
    
    return $pdf;
}
}