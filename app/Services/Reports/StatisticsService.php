<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\Log;

use App\Helpers\NumberToWords;
use App\Models\AssignSubject;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\TermType;

class StatisticsService
{
    public function __construct(private BulletinService $bulletin) {}

    /**
     * Statistiques (classement + stats globales) – version "management"
     */
    public function buildClassMeanStatistics(int $yearId, int $classId, int $termTypeId): array
    {
        // Réutilise le même dataset que pour le bulletin (cohérence)
        return $this->bulletin->buildClassBulletinData($yearId, $classId, $termTypeId);
    } 
/* public function buildClassMeanStatistics(int $yearId, int $classId, int $termTypeId): array
{
    // Réutilise le même dataset
    $data = $this->bulletin->buildClassBulletinData($yearId, $classId, $termTypeId);
    
    // 🔥 CORRECTION FORCÉE POUR LES CLASSES PROBLÉMATIQUES
    $className = $data['meta']['className'] ?? '';
    
    if ($className === '5ème A') {
        // La 5ème A a 105 étudiants, pas 188 !
        $expectedCount = 105;
        $actualCount = count($data['studentData'] ?? []);
        
        if ($actualCount > $expectedCount) {
            Log::warning("⚠️ CORRECTION APPLIQUÉE pour 5ème A: $actualCount -> $expectedCount étudiants");
            
            // Garder seulement les premiers $expectedCount étudiants
            $data['studentData'] = array_slice($data['studentData'], 0, $expectedCount);
            
            // Recalculer les statistiques
            $this->recalculateStatistics($data);
        }
    }
    
    return $data;
}

private function recalculateStatistics(array &$data): void
{
    if (empty($data['studentData'])) return;
    
    // Recalculer les moyennes
    $termMeans = array_column($data['studentData'], 'term_mean');
    $validMeans = array_filter($termMeans, fn($mean) => $mean >= 3);
    
    $data['stats']['highestTermMean'] = !empty($validMeans) ? number_format(max($validMeans), 2) : 'N/A';
    $data['stats']['lowestTermMean'] = !empty($validMeans) ? number_format(min($validMeans), 2) : 'N/A';
    $data['stats']['classTermMean'] = !empty($validMeans) ? number_format(array_sum($validMeans)/count($validMeans), 2) : 'N/A';
    
    // Recalculer les pourcentages
    $numberOfStudents = count(array_filter($data['studentData'], fn($st) => !($st['is_abandon'] ?? false)));
    $passers = array_filter($data['studentData'], fn($st) => !($st['is_abandon'] ?? false) && ($st['term_mean'] ?? 0) >= 10);
    
    $data['counts']['numberOfStudents'] = $numberOfStudents;
    $data['counts']['percentageAbove10'] = $numberOfStudents > 0 
        ? number_format((count($passers) / $numberOfStudents) * 100, 2)
        : number_format(0, 2);
} */

    /**
     * Statistiques (détails) – même data que management, mais autre vue
     */
    public function buildClassMeanStatisticsDetails(int $yearId, int $classId, int $termTypeId): array
    {
        return $this->bulletin->buildClassBulletinData($yearId, $classId, $termTypeId);
    }

    /**
     * Statistiques par matières
     */
    public function buildClassMeanStatisticsSubjects(int $yearId, int $classId, int $termTypeId): array
    {
        $metaData = $this->bulletin->buildClassBulletinData($yearId, $classId, $termTypeId);

        // Pour cette vue, on souhaite des colonnes par matière avec leurs moyennes d’élèves (déjà dans studentData).
        // On renvoie le même set + un calcul de best/avg/min au niveau classe si besoin.
        $termMeans = array_column($metaData['studentData'], 'term_mean');
        $metaData['stats']['highestTermMean'] = number_format(max($termMeans), 2);
        $metaData['stats']['lowestTermMean']  = number_format(min($termMeans), 2);
        $metaData['stats']['classTermMean']   = number_format(array_sum($termMeans)/max(count($termMeans),1), 2);

        return $metaData;
    }
}
