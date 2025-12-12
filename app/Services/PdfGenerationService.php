<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\DataObjects\BulletinData;

class PdfGenerationService
{
    public function generateBulletinPdf(BulletinData $bulletinData, $type = 'bulletin')
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 3600);

        $viewName = $type === 'statistics'
            ? 'backend.report.bulletin.annual-statistics_marksheets'
            : 'backend.report.bulletin.class_marksheets';
        
        $data = ($type === 'statistics')
            ? $this->prepareAnnualStatisticsData($bulletinData)
            : ['bulletinData' => $bulletinData->toArray()];

        return Pdf::loadView($viewName, $data)->setPaper('a4', 'portrait');
    }

    protected function prepareAnnualStatisticsData(BulletinData $bulletinData)
    {
        $data = $bulletinData->toArray();
        $validStudents = array_filter($data['students'], fn($student) =>
            isset($student['annual_mean']['mean']) && is_numeric($student['annual_mean']['mean'])
        );
        $annualMeans = array_column($validStudents, 'annual_mean.mean');

        return [
            'bulletinData' => $data,
            'className' => $data['class']['name'],
            'yearName' => $data['year']['name'],
            'termTypeName' => $data['termType']['name'],
            'principalTeacherName' => $data['principalTeacher'],
            'numberOfStudents' => count($data['students']),
            'classAnnualMean' => !empty($annualMeans) ? number_format(array_sum($annualMeans) / count($annualMeans), 2) : 'N/A',
            'highestAnnualMean' => !empty($annualMeans) ? number_format(max($annualMeans), 2) : 'N/A',
            'lowestAnnualMean' => !empty($annualMeans) ? number_format(min($annualMeans), 2) : 'N/A',
            'annualPercentageAbove10' => !empty($annualMeans)
                ? number_format((count(array_filter($annualMeans, fn($mean) => $mean >= 10)) / count($annualMeans)) * 100, 2)
                : '0.00',
            'studentData' => $data['students']
        ];
    }

    /** ✅ Génération PDF Statistiques de Classe */
    public function generateClassStatisticsPdf($bulletinData)
    {
        $data = [
            'className' => $bulletinData->class->name,
            'yearName' => $bulletinData->year->name,
            'termTypeName' => $bulletinData->termType->name,
            'principalTeacherName' => $bulletinData->principalTeacher ?? 'N/A',
            'numberOfStudents' => count($bulletinData->students),
            'studentData' => $bulletinData->students,
            'classTermMean' => collect($bulletinData->students)->avg('term_mean'),
        ];

        return Pdf::loadView('backend.report.statistics.statistics_class', $data)
            ->setPaper('a4', 'portrait');
    }

    /** ✅ Génération PDF Statistiques par Matières */
    public function generateSubjectStatisticsPdf($bulletinData)
    {
        $data = [
            'className' => $bulletinData->class->name,
            'yearName' => $bulletinData->year->name,
            'termTypeName' => $bulletinData->termType->name,
            'principalTeacherName' => $bulletinData->principalTeacher ?? 'N/A',
            'studentData' => $bulletinData->students,
            'subjects' => $bulletinData->subjects,
        ];

        return Pdf::loadView('backend.report.statistics.statistics_class_matiere', $data)
            ->setPaper('a4', 'portrait');
    }

    /** ✅ ZIP combiné avec noms clairs */
    public function generateStatisticsZip($bulletinData)
    {
        $tempPath = storage_path('app/public/temp');
        if (!file_exists($tempPath)) mkdir($tempPath, 0755, true);

        $classPdf = $this->generateClassStatisticsPdf($bulletinData)->output();
        $subjectPdf = $this->generateSubjectStatisticsPdf($bulletinData)->output();

        // ✅ Utiliser noms lisibles
        $yearName = preg_replace('/\s+/', '_', $bulletinData->year->name);
        $className = preg_replace('/\s+/', '_', $bulletinData->class->name);
        $termTypeName = preg_replace('/\s+/', '_', $bulletinData->termType->name);

        $classFilename = "{$className}_Classement_{$termTypeName}_{$yearName}.pdf";
        $subjectFilename = "{$className}_Statistiques_Matieres_{$termTypeName}_{$yearName}.pdf";

        $zipName = "{$className}_Statistiques_{$termTypeName}_{$yearName}.zip";
        $zipPath = $tempPath . '/' . $zipName;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $zip->addFromString($classFilename, $classPdf);
            $zip->addFromString($subjectFilename, $subjectPdf);
            $zip->close();
        } else {
            throw new \Exception("Impossible de créer le ZIP");
        }

        return $zipPath;
    }
}
