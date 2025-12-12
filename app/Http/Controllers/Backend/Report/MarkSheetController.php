<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentMarks;
use App\Models\ExamType;
use App\Models\StudentClass; 
use App\Models\StudentYear;
use App\Models\MarksGrade;
use App\Models\AssignSubjectTeach;
use App\Models\AssignSubject;
use Barryvdh\DomPDF\Facade\Pdf;

class MarkSheetController extends Controller
{
    public function MarkSheetView() 
    {
        $data['years'] = StudentYear::orderBy('id', 'desc')->get();
        $data['classes'] = StudentClass::all();
        $data['exam_types'] = ExamType::all();
        return view('backend.report.marksheet.marksheet_view', $data);
    }

    /**
     * Display the marksheet for a given exam type and student.
     */
    public function DisplayMarksheet(Request $request)
    {
        $studentMarks = $this->getStudentMarks($request->year_id, $request->class_id, $request->exam_type_id, $request->id_no);

    if ($studentMarks->isEmpty()) {
        return redirect()->back()->with('error', 'Désolé, Ces Critères Ne Correspondent Pas.');
    }

    $grades = MarksGrade::all();
    $totalMarks = $studentMarks->sum('marks');
    $averagePoint = $this->calculateAveragePoint($studentMarks);
    $finalGrade = $this->getFinalGrade($averagePoint);

    // Retrieve subject names and grade information per student mark
    foreach ($studentMarks as $mark) {
        $mark->subject_name = optional($mark->assignedSubject->school_subject)->name ?? 'N/A';
        $mark->subjective_mark = optional($mark->assignedSubject)->subjective_mark ?? 0;
        $mark->grade_name = $this->getGradeName($mark->marks);
        $mark->grade_point = $this->getGradePoint($mark->marks);
    }

    return view('backend.report.marksheet.marksheet_pdf', compact('studentMarks', 'grades', 'totalMarks', 'averagePoint', 'finalGrade'));
    }

    /**
     * Download the marksheet as a PDF.
     */
    public function DownloadMarksheet(Request $request)
    {
            $studentMarks = $this->getStudentMarks($request->year_id, $request->class_id, $request->exam_type_id, $request->id_no);

        if ($studentMarks->isEmpty()) {
            return redirect()->route('marksheet.generate.view')->with('error', 'Aucune donnée trouvée pour cet étudiant et cet examen.');
        }

        $grades = MarksGrade::all();
        $totalMarks = $studentMarks->sum('marks');
        $averagePoint = $this->calculateAveragePoint($studentMarks);
        $finalGrade = $this->getFinalGrade($averagePoint);

        foreach ($studentMarks as $mark) {
            $mark->subject_name = optional($mark->assignedSubject->school_subject)->name ?? 'N/A';
            $mark->subjective_mark = optional($mark->assignedSubject)->subjective_mark ?? 0;
            $mark->grade_name = $this->getGradeName($mark->marks);
            $mark->grade_point = $this->getGradePoint($mark->marks);
        }

        $pdf = Pdf::loadView('backend.report.marksheet.bulletin_scolaire', compact('studentMarks', 'grades', 'totalMarks', 'averagePoint', 'finalGrade'))->setPaper('a4')
        ->setOptions([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);

        return $pdf->download('marksheet.pdf');
    }

    // private function getStudentMarks($yearId, $classId, $examTypeId, $idNo)
    // {
    //     return StudentMarks::with(['assign_subject', 'year', 'student'])
    //         ->where('year_id', $yearId)
    //         ->where('class_id', $classId)
    //         ->where('exam_type_id', $examTypeId)
    //         ->where('id_no', $idNo)
    //         ->get();
    // }

    private function getStudentMarks($yearId, $classId, $examTypeId, $idNo)
{
    return StudentMarks::with([
        'assignedSubject.school_subject', // Subject details
        'assignedSubject.assign_teacher.teacher',        // Teacher details (User model)
        'year', 
        'student'
    ])
    ->where('year_id', $yearId)
    ->where('class_id', $classId)
    ->where('exam_type_id', $examTypeId)
    ->where('id_no', $idNo)
    ->get();
}


    private function calculateAveragePoint($studentMarks)
    {
        $totalPoint = $studentMarks->reduce(function ($carry, $mark) {
            $grade = MarksGrade::where('start_marks', '<=', $mark->marks)
                ->where('end_marks', '>=', $mark->marks)
                ->first();
            return $carry + ($grade ? $grade->grade_point : 0);
        }, 0);

        $totalSubjects = $studentMarks->count() ?: 1;

        return $totalPoint / $totalSubjects;
    }

    private function getFinalGrade($averagePoint)
    {
        return MarksGrade::where('start_point', '<=', $averagePoint)
            ->where('end_point', '>=', $averagePoint)
            ->first();
    }

    private function getGradeName($marks)
    {
        $grade = MarksGrade::where('start_marks', '<=', $marks)
            ->where('end_marks', '>=', $marks)
            ->first();

        return $grade->grade_name ?? 'N/A';
    }

    private function getGradePoint($marks)
    {
        $grade = MarksGrade::where('start_marks', '<=', $marks)
            ->where('end_marks', '>=', $marks)
            ->first();

        return $grade->grade_point ?? 'N/A';
    }


}
