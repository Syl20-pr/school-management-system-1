<?php

namespace App\Http\Controllers\Backend\Report\Bulletin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\Reports\BulletinService;
use App\Services\Reports\PdfReportService;

use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\TermType;

class BulletinController extends Controller
{
    public function __construct(
        private BulletinService $bulletin,
        private PdfReportService $pdfs
    ) {}

    public function index()
    {
        $years = StudentYear::all();
        $classes = StudentClass::all();
        $termTypes = TermType::all();
        // Affiche la vue avec formulaire pour choisir l’année, la classe, etc.
        return view('backend.report.bulletin.bulletin.mbulletin.index', compact('years', 'classes', 'termTypes'));
    }

    /**
     * Génération des bulletins (PDF simple ou ZIP avec 2 PDFs si fin de cycle)
     */
    public function generateClassMarksheets(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1024M');

        $yearId     = (int) $request->year_id;
        $classId    = (int) $request->class_id;
        $termTypeId = (int) $request->term_type_id;

        $data = $this->bulletin->buildClassBulletinData($yearId, $classId, $termTypeId);

        $yearName     = $data['meta']['yearName'];
        $className    = $data['meta']['className'];
        $termTypeName = $data['meta']['termTypeName'];

        $common = [
            'studentData'          => $data['studentData'],
            'subjects'             => $data['subjects'],
            'yearName'             => $yearName,
            'className'            => $className,
            'termTypeName'         => $termTypeName,
            'principalTeacherName' => $data['meta']['principalTeacherName'],
            'numberOfStudents'     => $data['counts']['numberOfStudents'],
            'percentageAbove10'    => $data['counts']['percentageAbove10'],
            'highestTermMean'      => $data['stats']['highestTermMean'],
            'lowestTermMean'       => $data['stats']['lowestTermMean'],
            'classTermMean'        => $data['stats']['classTermMean'],
        ];

        $isCycle1 = in_array($termTypeName, ['Trimestre 1','Trimestre 2','Trimestre 3']);
        $isCycle2 = in_array($termTypeName, ['Semestre 1','Semestre 2']);

        // Fin d’année (T3 ou S2) → 2 PDFs + ZIP
        if (($isCycle1 && $termTypeName === 'Trimestre 3') || ($isCycle2 && $termTypeName === 'Semestre 2')) {
            $pdf1 = $this->pdfs->render('backend.report.bulletin.class_marksheets', $common, 'a4', 'portrait');

            $common2 = array_merge($common, [
                'annualPercentageAbove10' => $data['stats']['annualPercentageAbove10'],
                'highestAnnualMean'       => $data['stats']['highestAnnualMean'],
                'lowestAnnualMean'        => $data['stats']['lowestAnnualMean'],
                'classAnnualMean'         => $data['stats']['classAnnualMean'],
            ]);

            $pdf2 = $this->pdfs->render('backend.report.bulletin.annual-statistics_marksheets', $common2, 'a4', 'portrait');

            $zipName = $className . '_Reports_' . $termTypeName . '.zip';
            $zipPath = $this->pdfs->zipPdfs([
                $className . '_Bulletins_'   . $termTypeName . '.pdf' => $pdf1,
                $className . '_Stat-Annuel_' . $termTypeName . '.pdf' => $pdf2,
            ], $zipName);

            if ($zipPath) {
                $headers = [
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma'        => 'no-cache',
                    'Expires'       => 'Fri, 01 Jan 1990 00:00:00 GMT',
                ];
                return response()->download($zipPath, $zipName, $headers)->deleteFileAfterSend(true);
            }

            // fallback si zip échoue
            return response($pdf1, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$className.'_Bulletins_'.$termTypeName.'.pdf"',
            ]);
        }

        // Sinon PDF simple
        $pdf = $this->pdfs->render('backend.report.bulletin.class_marksheets', $common, 'a4', 'portrait');
        $filename = $className . '_Bulletins' . $termTypeName . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
