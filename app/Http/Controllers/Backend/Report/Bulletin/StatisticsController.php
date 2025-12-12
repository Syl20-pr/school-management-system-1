<?php

namespace App\Http\Controllers\Backend\Report\Bulletin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Reports\StatisticsService;
use App\Services\Reports\PdfReportService;

class StatisticsController extends Controller
{
    public function __construct(
        private StatisticsService $stats,
        private PdfReportService $pdfs
    ) {}

    /**
     * Classement et stats (management)
     */
    public function generateClassMeanStatistics(Request $request)
    {
        set_time_limit(600);
        $yearId     = (int) $request->year_id;
        $classId    = (int) $request->class_id;
        $termTypeId = (int) $request->term_type_id;

        $data = $this->stats->buildClassMeanStatistics($yearId, $classId, $termTypeId);

        $payload = [
            'studentData'          => $data['studentData'],
            'subjects'             => $data['subjects'],
            'yearName'             => $data['meta']['yearName'],
            'className'            => $data['meta']['className'],
            'termTypeName'         => $data['meta']['termTypeName'],
            'principalTeacherName' => $data['meta']['principalTeacherName'],
            'highestTermMean'      => $data['stats']['highestTermMean'],
            'lowestTermMean'       => $data['stats']['lowestTermMean'],
            'classTermMean'        => $data['stats']['classTermMean'],
            'numberOfStudents'     => $data['counts']['numberOfStudents'],
            'percentageAbove10'    => $data['counts']['percentageAbove10'],
        ];

        $pdf = $this->pdfs->render('backend.report.bulletin.class_rank_management', $payload, 'a4', 'portrait');
        $filename = $payload['className'] . '_Statistic_Moyenne_' . $payload['termTypeName'] . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Statistiques détaillées (classe)
     */
    public function generateClassMeanStatisticsDetails(Request $request)
    {
        set_time_limit(600);
        $yearId     = (int) $request->year_id;
        $classId    = (int) $request->class_id;
        $termTypeId = (int) $request->term_type_id;

        $data = $this->stats->buildClassMeanStatisticsDetails($yearId, $classId, $termTypeId);

        $payload = [
            'studentData'          => $data['studentData'],
            'subjects'             => $data['subjects'],
            'yearName'             => $data['meta']['yearName'],
            'className'            => $data['meta']['className'],
            'termTypeName'         => $data['meta']['termTypeName'],
            'principalTeacherName' => $data['meta']['principalTeacherName'],
            'highestTermMean'      => $data['stats']['highestTermMean'],
            'lowestTermMean'       => $data['stats']['lowestTermMean'],
            'classTermMean'        => $data['stats']['classTermMean'],
            'numberOfStudents'     => $data['counts']['numberOfStudents'],
            'percentageAbove10'    => $data['counts']['percentageAbove10'],
        ];

        $pdf = $this->pdfs->render('backend.report.bulletin.statistics_class', $payload, 'a4', 'portrait');
        $filename = $payload['className'] . '_Statistic_Moyenne_details_' . $payload['termTypeName'] . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Statistiques par matières (classe)
     */
    public function generateClassMeanStatisticsSubjects(Request $request)
    {
        set_time_limit(600);
        $yearId     = (int) $request->year_id;
        $classId    = (int) $request->class_id;
        $termTypeId = (int) $request->term_type_id;

        $data = $this->stats->buildClassMeanStatisticsSubjects($yearId, $classId, $termTypeId);

        $payload = [
            'studentData'          => $data['studentData'],
            'subjects'             => $data['subjects'],
            'yearName'             => $data['meta']['yearName'],
            'className'            => $data['meta']['className'],
            'termTypeName'         => $data['meta']['termTypeName'],
            'principalTeacherName' => $data['meta']['principalTeacherName'],
            'highestTermMean'      => $data['stats']['highestTermMean'],
            'lowestTermMean'       => $data['stats']['lowestTermMean'],
            'classTermMean'        => $data['stats']['classTermMean'],
            'numberOfStudents'     => $data['counts']['numberOfStudents'],
        ];

        $pdf = $this->pdfs->render('backend.report.bulletin.statistics_class_matiere', $payload, 'a4', 'portrait');
        $filename = $payload['className'] . '_Statistic_Moyenne_details_par_Matieres' . $payload['termTypeName'] . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
