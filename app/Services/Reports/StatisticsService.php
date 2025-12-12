<?php

namespace App\Services\Reports;

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
